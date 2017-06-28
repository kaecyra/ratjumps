<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\ChatBot\Client\FuelRats;

use Kaecyra\ChatBot\Client\ClientInterface;
use Kaecyra\ChatBot\Socket\SocketClient;

use Kaecyra\ChatBot\Socket\MessageInterface;

use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;

use React\EventLoop\LoopInterface;

use Ratchet\Client\WebSocket;

use Exception;

/**
 * Rat Socket Client
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package ratjumps
 */
class RatClient extends SocketClient {

    /**
     * Dependency Injection Container
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Array of protocol handlers
     * @var array
     */
    protected $protocol = [];

    /**
     * Array of callables, by message type
     * @var array
     */
    protected $handlers = [];

    /**
     * Sent message ID
     * @var int
     */
    protected $messageSequence;

    /**
     * Start RTM client
     *
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param array $settings
     */
    public function __construct(
        ContainerInterface $container,
        LoopInterface $loop,
        array $settings
    ) {
        parent::__construct($loop, $settings);

        $this->container = $container;
    }

    /**
     * Initialize
     *
     */
    public function initialize() {
        $this->tLog(LogLevel::DEBUG, "Initializing Rat client");

        // Instantiate protocol handlers
        $this->tLog(LogLevel::NOTICE, "Adding Rat API protocol handlers");
        $protocolNS = '\\Kaecyra\\ChatBot\\Client\\FuelRats\\Protocol';
        foreach ([
            'Connection'
        ] as $protocolHandler) {
            $handlerClass = "{$protocolNS}\\{$protocolHandler}";
            if (!$this->container->has($handlerClass)) {
                $this->tLog(LogLevel::WARNING, " missing protocol handler: {handler} ({class})", [
                    'handler' => $protocolHandler,
                    'class' => $handlerClass
                ]);
                continue;
            }

            $this->tLog(LogLevel::INFO, " protocol handler: {handler}", [
                'handler' => $protocolHandler
            ]);
            $this->protocol[$protocolHandler] = $this->container->get($handlerClass);
            $this->protocol[$protocolHandler]->start($this);
        }

        // Prepare message handling
        $this->setMessageFactory(function(string $direction) {
            $message = new \Kaecyra\ChatBot\Client\FuelRats\SocketMessage;
            return $message;
        });

        // Mark configured
        $this->setState(ClientInterface::STATE_CONFIGURED);
    }

    /**
     * Receive parsed socket message
     *
     * @param MessageInterface $message
     */
    public function onMessage(MessageInterface $message) {
        $this->callMessageHandlers($message);
    }

    /**
     * Receive socket close event
     *
     * @param int $code
     * @param string $reason
     */
    public function onClose($code = null, $reason = null) {

    }

    /**
     * Receive socket error event
     *
     * @param int $code
     * @param string $reason
     */
    public function onError($code = null, $reason = null) {

    }

    /**
     * Add a socket message handler
     *
     * Messages are triggered externally by the arrival of a socket message.
     *
     * @param string $method
     * @param callable $callback
     */
    public function addMessageHandler(string $method, callable $callback) {
        $this->addHandler('message', $method, $callback);
    }

    /**
     * Add a socket action handler
     *
     * Actions are triggered internally and not in response to an incoming
     * message.
     *
     * @param string $method
     * @param callable $callback
     */
    public function addActionHandler(string $method, callable $callback) {
        $this->addHandler('action', $method, $callback);
    }

    /**
     * Add a generic handler
     *
     * @param string $type
     * @param string $method
     * @param callable $callback
     */
    protected function addHandler(string $type, string $method, callable $callback) {
        $handlerKey = "{$type}.{$method}";
        if (!is_array($this->handlers[$handlerKey])) {
            $this->handlers[$handlerKey] = [];
        }

        $this->handlers[$handlerKey][] = $callback;
    }

    /**
     * Convenience method to call message handlers by MessageInterface
     *
     * @param MessageInterface $message
     */
    public function callMessageHandlers(MessageInterface $message) {
        $method = $message->getMethod();
        $this->callHandlers('message', $method, [
            'message' => $message
        ]);
        if ($message->has('subtype')) {
            $subMethod = "{$method}:".$message->get('subtype');
            $this->callHandlers('message', $subMethod, [
                'message' => $message
            ]);
        }
    }

    /**
     * Convenience method to call action handlers by method
     *
     * @param string $method
     */
    public function callActionHandlers(string $method) {
        $this->callHandlers('action', $method);
    }

    /**
     * Execute a handler stack
     *
     * @param string $method
     * @param array $arguments
     */
    public function callHandlers(string $type, string $method, array $arguments = []) {
        $handlerKey = "{$type}.{$method}";
        if (!array_key_exists($handlerKey, $this->handlers) || !count($this->handlers[$handlerKey])) {
            $this->tLog(LogLevel::INFO, "Ignored unhandled {type}: {method}", [
                'type' => $type,
                'method' => $method
            ]);
            return;
        }

        // Iterate and call handlers through the container
        foreach ($this->handlers[$handlerKey] as $callback) {
            $this->container->call($callback, $arguments);
        }
    }

    /**
     * Command: startup
     *
     * @param CommandInterface $command
     */
    public function command_startup(CommandInterface $command) {
        $this->tLog(LogLevel::INFO, "Ratjump startup");
    }

    /**
     * Command: newrescue
     *
     * @param CommandInterface $command
     */
    public function command_newrescue(CommandInterface $command) {
        $this->tLog(LogLevel::INFO, "New rescue");
    }

}