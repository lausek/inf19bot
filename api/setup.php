<?php

require_once __DIR__ . '/../vendor/autoload.php';

Util::protect_call_using('key', $_POST['key'] ?? null, function ()
{
    if (!function_exists('curl_version'))
    {
        Log::trace('curl is not installed');
        echo 'curl is not installed';
        die();
    }
    
    if (isset($_POST['active']))
    {
        $active = $_POST['active'];
    
        if ('0' === $active)
        {
            echo Util::set_webhook_active(false);
        }
        else if ('1' === $active)
        {
            echo Util::set_webhook_active(true);
        }
        else
        {
            echo "invalid value for parameter 'active' (0 | 1)";
        }
    }
    else
    {
        echo "supply parameter 'active' (0 | 1)";
    }
});
