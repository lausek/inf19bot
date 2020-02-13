<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if our boss has sent us an email.

class BossmailCheck extends Check
{
    function run($update = null) : String
    {
        $creds = Util::load_secret_from_json(Util::path('/secret/bossmail.json'));

        if (isset($creds['mailbox']) && isset($creds['email']) && isset($creds['password']))
        {
            $asset_dir = Util::path('/asset');
            $queries = [
                'TO "' . $creds['bossmail'] . '" UNDELETED',
                'CC "' . $creds['bossmail'] . '" UNDELETED',
                'BCC "' . $creds['bossmail'] . '" UNDELETED',
                'FROM "' . $creds['bossmail'] . '" UNDELETED'
            ];

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

                foreach ($queries as $query)
                {
                    $this->find_and_forward($inbox, $query);
                }
            }
            catch (Exception $e)
            {
                Log::etrace('email login credentials seem to be wrong');
            }
        }

        return '';
    }

    function find_and_forward($inbox, $query)
    {
        $mail_ids = $inbox->searchMailbox($query);
        if (empty($mail_ids))
        {
            return '';
        }

        Util::inform_nerds(Language::get('CHK_BOSSMAIL_RECEIVED'));

        foreach ($mail_ids as $mail_id)
        {
            $mail = $inbox->getMail($mail_id);
            if ($mail->textHtml)
            {
                $msg = $mail->textHtml;
                $msg = preg_replace('/<br>/', "\n", $msg);
                $msg = trim(strip_tags($msg, '<b><i><u><s><a><pre><code>'));
                $markup = 'html';
            }
            else
            {
                $msg = $mail->textPlain;
                $markup = 'markdown';
            }

            $msg = preg_replace('/\S*google\S*/', '', $msg);

            if (Util::inform_nerds($msg, $markup))
            {
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

                $inbox->deleteMail($mail_id);
            }
        }
    }
}
