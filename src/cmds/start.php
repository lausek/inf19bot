<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class StartCommand extends Command
{
    function run($update = null) : string
    {
        if (isset($update->message))
        {
            $forward_err_to = Util::get_config('forward_err_to');
            if (null !== $forward_err_to)
            {
                if (in_array($update->message->from->username, $forward_err_to))
                {
                    Util::add_forward_id($update->message->from->id);
                }
            }
        }

        return Language::get('GEN_PONG');
    }
}
