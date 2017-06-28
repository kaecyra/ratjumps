<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\ChatBot\Client\FuelRats\Strategy;

use \Exception;

/**
 * Startup sync strategy
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package ratjumps
 */
class StartupStrategy extends AbstractStrategy {

    protected $phases = [
        'authenticate',
        'subscribe'
    ];

}