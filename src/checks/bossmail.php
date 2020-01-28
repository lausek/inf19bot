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
            $query = 'TO "' . $creds['bossmail'] . '" UNDELETED';

            try
            {
                $inbox = new PhpImap\Mailbox(
                    $creds['mailbox'],
                    $creds['email'],
                    $creds['password'],
                    Util::path('/asset')
                );

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
                        $msg = trim(strip_tags($msg));
                    }
                    else
                    {
                        $msg = $mail->textPlain;
                    }

                    if (Util::inform_nerds($msg))
                    {
                        $client = Util::get_client();
                        $nerds_id = Cache::get_nerds_id();

                        if ($mail->hasAttachments())
                        {
                            foreach ($mail->getAttachments() as $attachment)
                            {
                                $asset_path = basename($attachment->filePath);
                                $asset_url = Util::get_asset_url($asset_path);
                                $client->sendDocument($nerds_id, $asset_url);
                            }
                        }

                        // $inbox->deleteMail($mail_id);
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
}
