<?php

require_once __DIR__ . '/../vendor/autoload.php';

function set_header($path)
{
    $ctype = mime_content_type($path);
    header("Content-Type: $ctype");
    header("Content-Length: " . filesize($path));
}

Util::protect_call_using('asset', $_GET['key'] ?? null, function ()
{
    $path = $_GET['path'] or die('no asset path given');
    $abs_path = Util::path("asset/$path");

    $handle = @fopen($abs_path, 'rb');
    if (false !== $handle)
    {
        set_header($abs_path);
        fpassthru($handle);
        fclose($handle);
    }
    else
    {
        Util::etrace("asset $abs_path requested but not found");
    }
}, false);
