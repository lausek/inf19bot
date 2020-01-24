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
            if (false === $inbox)
            {
                Log::etrace('email login credentials seem to be wrong');
                return '';
            }
            // Util::inform_nerds($msg);
            Log::forward($msg);
        }
        return '';
    }
}
