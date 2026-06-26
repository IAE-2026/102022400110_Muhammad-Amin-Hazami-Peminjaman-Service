<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        set_error_handler(function ($errno, $errstr) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                return true;
            }
            return false;
        }, E_DEPRECATED | E_USER_DEPRECATED);

        parent::setUp();
    }
}
