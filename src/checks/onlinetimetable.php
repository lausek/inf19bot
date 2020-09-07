<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if timetable was updated. Create a notification
// message if so.

class OnlinetimetableCheck extends Check
{
    function run(Response $response, $update = null)
    {
        $today = date('d.m.y');
        $dt_today = new DateTime($today);

        $dt_last_update = null;
        if (isset($this->cache['last_update']))
        {
            $dt_last_update = new DateTime($this->cache['last_update']);
        }

        if ($dt_last_update === null || $dt_last_update < $dt_today)
        {
            $url = Util::get_config('online_timetable_url');
            if (null !== $url)
            {
                $content = file_get_contents($url);
                if ($content === false)
                {
                    Log::etrace("error while fetching online timetable $content");
                    return;
                }

                $dom = new DOMDocument();
                @$dom->loadHTMLFile($url);
                $xpath = new DOMXPath($dom);

                $calendar = [];
                $nodes = $xpath->query('//*[contains(@class, "week_block")]');
                foreach ($nodes as $node)
                {
                    $tooltip = $node->getElementsByTagName('span')->item(0);
                    $tooltip->parentNode->removeChild($tooltip);
                    preg_match_all("/(\d{2}:\d{2}.+\d{2}:\d{2})(.+)(MGH-TINF19)/", $node->textContent, $matches);
                    $topic = $matches[2][0];

                    if (!$matches)
                    {
                        echo "error\n";
                    }
                    else
                    {
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

                if (isset($calendar[$today]))
                {
                    $msg = "$date - " . Language::get('CHK_ONLINETIMETABLE_TODAY') . "\n";
                    $msg .= "\n";
                    foreach ($calendar[$today] as $module)
                    {
                        $msg .= "- $module\n";
                    }
                    $response->add_message($msg);
                }
            }

            $this->cache['last_update'] = $today;
        }
    }
}
