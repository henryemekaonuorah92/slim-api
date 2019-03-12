<?php

namespace Tests\Base;

use App\Base\AppContainer;

class AppContainerTest extends BaseCase
{
    public function testInstance()
    {
        $appId = 10;
        $app   = AppContainer::getAppInstance(['settings' =>
            [
                'httpVersion' => 2,
                'determineRouteBeforeAppMiddleware' => true,
                'routerCacheFile' => true,
                'displayErrorDetails' => true,
            ],
        ], $appId);

        $container = AppContainer::getContainer($appId);

        $this->assertEquals($app->getContainer(), $container);
        $configVal = AppContainer::config('httpVersion', null, $appId);

        $this->assertEquals(2, $configVal);
    }
}
