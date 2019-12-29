<?php

require_once __DIR__ . '/../vendor/autoload.php';

function help()
{
    return "" 
    . "help - display commands and functionality\n"
    . "webhook - set parameter `active` to 0 or 1 to activate webhook\n"
    . "forward - set parameter `id` to add a new chat to the error forwarding feature\n";
}

function webhook()
{
    if (isset($_POST['active']))
    {
        $active = $_POST['active'];
    
        if ('0' === $active)
        {
            return Util::set_webhook_active(false);
        }
        else if ('1' === $active)
        {
            return Util::set_webhook_active(true);
        }
        else
        {
            return 'invalid value for parameter `active` (0 | 1)';
        }
    }
    else
    {
        return 'supply parameter `active` (0 | 1)';
    }
}

function forward()
{
    if (isset($_POST['id']))
    {
        $id = $_POST['id'];
        $cache = new Cache('Ids');
        if (isset($cache->forward_err_to))
        {
            $cache->forward_err_to[] = $id;
        }
        else
        {
            $cache->forward_err_to = [$id];
        }
        Log::etrace("forwarding errors to $id now");
        return 'success';
    }
    else
    {
        return 'supply parameter `id`';
    }
}

Util::protect_call_using('key', $_POST['key'] ?? null, function ()
{
    if (!function_exists('curl_version'))
    {
        Log::etrace('curl is not installed');
        echo 'curl is not installed';
        die();
    }

    switch (@$_POST['cmd'])
    {
        case 'webhook':
            echo webhook();
            break;
        case 'forward':
            echo forward();
            break;
        case 'help':
        default:
            echo help();
            break;
    }
});
