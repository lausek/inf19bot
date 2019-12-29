<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class StartCommand extends Command
{
    function run() : String
    {
        $client = Util::get_client();

        $forward_err_to = Util::get_config('forward_err_to');
        if (null !== $forward_err_to)
        {
            if (in_array($client->message->from->username, $forward_err_to))
            {
                Util::add_forward_id($client->message->from->id);
            }
        }

        return Language::get('GEN_PONG');
    }
}
