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
            $inbox = imap_open($creds['mailbox'], $creds['email'], $creds['password']);
            $query = 'TO "' . $creds['bossmail'] . '" UNDELETED';
            if (false === $inbox)
            {
                Log::etrace('email login credentials seem to be wrong');
                return '';
            }

            // returns false if query was invalid or nothing was found
            $mail_ids = imap_search($inbox, $query);

            if (false !== $mail_ids)
            {
                Util::inform_nerds(Language::get('CHK_BOSSMAIL_RECEIVED'));

                foreach ($mail_ids as $mail_id)
                {
                    // fetch email as plain text
                    $msg = imap_fetchbody($inbox, $mail_id, 1);
                    if (empty($msg))
                    {
                        // fetch email as html
                        $msg = imap_fetchbody($inbox, $mail_id, 2);
                    }

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
