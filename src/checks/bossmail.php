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
            $inbox = imap_open($creds['mailbox'], $creds['email'], $cred['password']);
            $query = 'FROM "' . $creds['bossmail'] . '"';
            if (false === $inbox)
            {
                Log::etrace('email login credentials seem to be wrong');
                return '';
            }

            // returns false if query was invalid or nothing was found
            $mail_ids = imap_search($inbox, $query);

            if (false !== $mail_ids)
            {
                foreach ($mail_ids as $mail_id)
                {
                    $mail_text = imap_fetchtext($inbox, $mail_id);
                    $mail_unbased = imap_base64($mail_text);
                    $msg = false !== $mail_unbased ? $mail_unbased : $mail_text;
                    if (Util::inform_nerds($msg))
                    {
                        imap_delete($inbox, $mail_id);
                    }
                }

                // delete emails marked for deletion
                imap_expunge($inbox);
                imap_close($inbox);
            }
        }

        return '';
    }
}
