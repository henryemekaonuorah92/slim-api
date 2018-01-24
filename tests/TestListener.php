<?php

namespace Tests;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class TestListener implements \PHPUnit\Framework\TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test)
    {
    }

    public function startTestSuite(TestSuite $suite)
    {
    }

}