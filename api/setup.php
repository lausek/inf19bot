<?php

require_once __DIR__ . '/../vendor/autoload.php';

Util::critical_section();

function set_webhook_active(bool $active)
{
    $url = $active ? Util::get_config('webhook_url') : '';
    $cons = Util::get_config('webhook_connections') ?? 20;
    $subscribe_to = Util::get_config('webhook_subscribe') ?? [];

    $client = Util::get_client();
    $result = $client->setWebhook($url, null, $cons, $subscribe_to);

    if (true === $result->ok)
    {
        Log::trace("webhook enabled: $active");
        echo "webhook enabled: $active";
    }
    else
    {
        Log::trace($result->description);
        echo $result->description;
    }
}

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
        set_webhook_active(false);
    }
    else if ('1' === $active)
    {
        set_webhook_active(true);
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
