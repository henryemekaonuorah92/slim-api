<?php

namespace App\Base\Helper;

use App\Base\AppContainer;
use App\Base\EventManager;

class Event
{

    /**
     * @param $method
     * @param array ...$parameters
     */
    public static function emit($method, ...$parameters)
    {
        /** @var EventManager $eventManager */
        $eventManager = AppContainer::getContainer()->get('event_manager');
        $eventManager->emit($method, ...$parameters);
    }
}