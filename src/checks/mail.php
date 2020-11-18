<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if our boss has sent us an email.

class MailCheck extends Check
{
    const MAX_MESSAGE_SIZE = 4096;

    function run(Response $response, $update = null)
    {
        $creds = Util::load_secret_from_json(Util::path('/secret/mail.json'));

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
                    return;
                }

                foreach ($unread_ids as $unread_id)
                {
                    $mail = $inbox->getMail($unread_id);
                    if ($this->is_forward_allowed($mail))
                    {
                        if ($this->forward($mail, $response))
                        {
                            //$inbox->deleteMail($unread_id);
                        }
                        else
                        {
                            //$inbox->markMailAsUnread($unread_id);
                        }
                        $inbox->markMailAsUnread($unread_id);
                    }
                }
            }
            catch (Exception $e)
            {
                throw new Exception('email login credentials seem to be wrong');
            }
        }
    }

    function is_forward_allowed($mail) : bool
    {
        foreach (get_object_vars($mail) as $var)
        {
            echo $var;
        }
        return false;
    }

    function forward($mail, Response $response): bool
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

            $response->add_message($chunk, $markup);

            // FIXME: can this be moved out of loop?
            if ($mail->hasAttachments())
            {
                foreach ($mail->getAttachments() as $attachment)
                {
                    $asset_path = basename($attachment->filePath);
                    $asset_url = Util::get_asset_url($asset_path);
                    $response->add_document($attachment->name, $asset_url);
                }
            }
        }

        $response->add_message(Language::get('CHK_MAIL_RECEIVED'));

        return true;
    }

    // remove google groups email footer
    function strip_footer(string $msg): string
    {
        // if email has no footer, return original
        if (false === strpos($msg, '--'))
        {
            return $msg;
        }
        $parts = explode('--', $msg);
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
