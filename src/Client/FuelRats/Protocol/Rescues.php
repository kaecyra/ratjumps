<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\ChatBot\Client\FuelRats\Protocol;

use Kaecyra\ChatBot\Client\FuelRats\RatClient;
use Kaecyra\ChatBot\Socket\MessageInterface;

use Kaecyra\ChatBot\Bot\Command\SimpleCommand;

use Psr\Log\LogLevel;

use \Exception;

/**
 * Rescue protocol handler
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package ratjumps
 */
class Rescues extends AbstractProtocolHandler {

    /**
     *
     * @param RatClient $client
     */
    public function start(RatClient $client) {
        $client->addMessageHandler('rescue:created', [$this, 'message_rescuecreated']);
        $client->addMessageHandler('rescue:updated', [$this, 'message_rescueupdated']);
    }

    /**
     * Handle 'rescue:created' message
     *
     * @param RatClient $client
     * @param MessageInterface $message
     */
    public function message_rescuecreated(RatClient $client, MessageInterface $message) {
        $this->tLog(LogLevel::NOTICE, "Server sent 'rescue:created'.");

        // Queue newrescue
        $sync = new SimpleCommand('newrescue');
        $client->queueCommand($sync);
    }

    /**
     * Handle 'rescue:updated' message
     *
     * @param RatClient $client
     * @param MessageInterface $message
     */
    public function message_rescueupdated(RatClient $client, MessageInterface $message) {

    }

}