<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class ResourcesTest extends TestCase
{
    public function testPathApi()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/home/www/htdocs/api';
        $expected = '/home/www/htdocs/data/secret/key';
        $this->assertEquals($expected, Util::path('secret/key'));
        $this->assertEquals($expected, Util::path('/secret/key'));
    }

    public function testPath()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/home/www/htdocs';
        $expected = '/home/www/htdocs/data/secret/key';
        $this->assertEquals($expected, Util::path('secret/key'));
        $this->assertEquals($expected, Util::path('/secret/key'));
    }
}
