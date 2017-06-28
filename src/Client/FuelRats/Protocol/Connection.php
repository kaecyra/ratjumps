<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\ChatBot\Client\FuelRats\Protocol;

use Kaecyra\ChatBot\Client\FuelRats\RatClient;
use Kaecyra\ChatBot\Socket\MessageInterface;

use Kaecyra\ChatBot\Client\FuelRats\Strategy\StartupStrategy;

use Kaecyra\ChatBot\Bot\Command\SimpleCommand;

use Psr\Log\LogLevel;

use \Exception;

/**
 * Connection protocol handler
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package ratjumps
 */
class Connection extends AbstractProtocolHandler {

    /**
     *
     * @param RatClient $client
     */
    public function start(RatClient $client) {
        $client->addMessageHandler('welcome', [$this, 'message_welcome']);
        $client->addMessageHandler('authorization', [$this, 'message_authorization']);
        $client->addMessageHandler('stream:subscribe', [$this, 'message_subscribe']);
    }

    /**
     * Handle 'welcome' message
     *
     * Connection is established and server is ready for messages. We should now
     * get synced up.
     *
     * @param RatClient $client
     * @param MessageInterface $message
     */
    public function message_welcome(RatClient $client, MessageInterface $message) {
        $this->tLog(LogLevel::NOTICE, "Server sent 'welcome'. Connected.");

        // Authenticate

    }

    /**
     * Handle 'authorization' message
     *
     * Server has responded to authorization.
     *
     * @param RatClient $client
     * @param MessageInterface $message
     */
    public function message_authorization(RatClient $client, MessageInterface $message) {
        $this->tLog(LogLevel::NOTICE, "Server sent 'authorization'.");

        // Subscribe

    }

    /**
     * Handle 'stream:subscribe' message
     *
     * Server has responded to subscription request.
     *
     * @param RatClient $client
     * @param MessageInterface $message
     */
    public function message_subscribe(RatClient $client, MessageInterface $message) {
        $this->tLog(LogLevel::NOTICE, "Server sent 'stream:subscribe'.");
    }

}