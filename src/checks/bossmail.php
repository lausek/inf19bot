<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if our boss has sent us an email.

class BossmailCheck extends Check
{
    const MAX_MESSAGE_SIZE = 4096;

    function run($update = null) : String
    {
        $creds = Util::load_secret_from_json(Util::path('/secret/bossmail.json'));

        if (isset($creds['mailbox']) && isset($creds['email']) && isset($creds['password']))
        {
            $asset_dir = Util::path('/asset');

            if (!file_exists($asset_dir))
            {
                mkdir($asset_dir);
            }

            try
            {
                $inbox = new PhpImap\Mailbox(
                    $creds['mailbox'],
                    $creds['email'],
                    $creds['password'],
                    $asset_dir
                );

                $unread_ids = $inbox->searchMailbox('UNSEEN');

                if (empty($unread_ids))
                {
                    return '';
                }

                foreach ($unread_ids as $unread_id)
                {
                    $mail = $inbox->getMail($unread_id);
                    if (false !== strpos($mail->senderAddress, $creds['bossmail']))
                    {
                        if ($this->forward($mail))
                        {
                            // $inbox->deleteMail($unread_id);
                        }
                        else
                        {
                            $inbox->markMailAsUnread($unread_id);
                        }
                    }
                }
            }
            catch (Exception $e)
            {
                Log::etrace('email login credentials seem to be wrong');
            }
        }

        return '';
    }

    function forward($mail): bool
    {
        if ($mail->textHtml)
        {
            $msg = $this->normalize_html($mail->textHtml);
            $markup = 'html';
        }
        else
        {
            $msg = $mail->textPlain;
            $markup = 'markdown';
        }

        $msg = $this->strip_footer($msg);
        $len = mb_strlen($msg);

        for ($i = 0; $i < $len; $i += self::MAX_MESSAGE_SIZE)
        {
            $chunk = mb_substr($msg, $i, self::MAX_MESSAGE_SIZE);

            if (!Util::inform_nerds($chunk, $markup))
            {
                return false;
            }

            $client = Util::get_client();
            $nerds_id = Cache::get_nerds_id();

            if ($mail->hasAttachments())
            {
                foreach ($mail->getAttachments() as $attachment)
                {
                    $asset_path = basename($attachment->filePath);
                    $asset_url = Util::get_asset_url($asset_path);
                    $client->sendDocument($nerds_id, $asset_url, null, $attachment->name);
                }
            }

        }

        Util::inform_nerds(Language::get('CHK_BOSSMAIL_RECEIVED'));

        return true;
    }

    // remove google groups email footer
    function strip_footer(string $msg): string
    {
        $parts = explode('--', $msg);
        array_pop($parts);
        return implode('--', $parts);
    }

    function normalize_html(string $html): string
    {
        $html = preg_replace('/(<br>|<\/p>)/', "\n", $html);
        $html = preg_replace('/(\n|\r)+/', "\n", $html);
        $html = trim(strip_tags($html, '<b><i><u><s><a><pre><code>'));
        $html = html_entity_decode($html);

        $drops = ['An:', 'Von:', 'Cc:', 'Bcc:', 'Betreff:'];

        foreach ($drops as $drop)
        {
            $html = preg_replace("/\s*$drop.*\\n/", '', $html);
        }

        return $html;
    }
}
