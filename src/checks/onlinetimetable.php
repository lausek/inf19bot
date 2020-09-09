<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if timetable was updated. Create a notification
// message if so.

class OnlinetimetableCheck extends Check
{
    function run(Response $response, $update = null)
    {
        $dt_today = new DateTime();
        // format date as string; save for later, because adding to
        // date mutates it...
        $today = $dt_today->format('d.m.y');

        // try to lookup date from cache
        $dt_last_update = null;
        if (isset($this->cache['last_update']))
        {
            $dt_last_update = new DateTime($this->cache['last_update']);
        }

        // is it after 18 o'clock
        $late_engouh = 18 <= intval($dt_today->format('H'));

        // if we do not have an update yet or our current date is later than the previous update
        if (($dt_last_update === null || $dt_last_update < new DateTime($today)) && $late_engouh)
        {
            $calendar = [];

            $url = Util::get_config('online_timetable_url');
            if (null !== $url)
            {
                $dom = new DOMDocument();
                @$dom->loadHTMLFile($url);
                $xpath = new DOMXPath($dom);
                // query the source for calendar dates
                $nodes = $xpath->query('//*[contains(@class, "week_block")]');
                foreach ($nodes as $node)
                {
                    $tooltip = $node->getElementsByTagName('span')->item(0);
                    $tooltip->parentNode->removeChild($tooltip);

                    // extract module name from block
                    preg_match_all("/(\d{2}:\d{2}.+\d{2}:\d{2})(.+)(MGH-TINF19)/", $node->textContent, $matches);
                    $topic = $matches[2][0];

                    if (!$matches)
                    {
                        echo "error\n";
                    }
                    else
                    {
                        // tooltip contains the date and other useful information
                        $when = $tooltip->getElementsByTagName('div')->item(1)->nodeValue;
                        preg_match_all("/\w{2}\s(\S+)\s(\S+)-(\S+)/", $when, $matches);

                        $date = $matches[1][0];
                        $start = $matches[2][0];
                        $end = $matches[3][0];

                        if (!isset($calendar[$date]))
                        {
                            $calendar[$date] = [];
                        }

                        array_push($calendar[$date], "$start-$end Uhr: $topic");
                    }
                }

                // this actuall mutates `today` and invalidates it as such...
                $tomorrow = $dt_today->add(new DateInterval('P1D'));
                $tomorrow = $tomorrow->format('d.m.y');
                if (isset($calendar[$tomorrow]))
                {
                    $msg = "$tomorrow - " . Language::get('CHK_ONLINETIMETABLE_TOMORROW') . "\n";
                    $msg .= "\n";
                    foreach ($calendar[$tomorrow] as $module)
                    {
                        $msg .= "- $module\n";
                    }
                    $response->add_message($msg);

                    // if this process was requested from the nerds chat, don't emit it twice 
                    if (!$response->is_nerds()) {
                        Util::inform_nerds($msg);
                    }
                }
            }

            $this->cache['last_update'] = $today;
        }
    }
}
