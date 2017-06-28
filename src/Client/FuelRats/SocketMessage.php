<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\ChatBot\Client\FuelRats;

use Kaecyra\AppCommon\Store;

use Kaecyra\ChatBot\Socket\AbstractSocketMessage;
use Kaecyra\ChatBot\Socket\MessageInterface;

use \Exception;

/**
 * FuelRats API socket message
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package ratjumps
 */
class SocketMessage extends AbstractSocketMessage {

    /**
     *
     * @var Store
     */
    protected $meta;

    /**
     *
     * @var Store
     */
    protected $attributes;

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        $this->meta = new Store;
        $this->attributes = new Store;
    }

    /**
     * Parse JSON encoded wire formatted message
     *
     * @param string $message
     * @return MessageInterface
     * @throws Exception
     */
    public function ingest(string $message): MessageInterface {
        $messageData = json_decode(trim($message), true);
        if (!is_array($messageData)) {
            throw new \Exception('Unable to decode incoming message');
        }

        $method = $messageData['action'] ?? $messageData['meta']['action'] ?? null;
        if (!$method) {

        }
        unset($messageData['action']);

        $this->populate($method, $messageData);
        return $this;
    }

    /**
     * Return JSON encoded wire formatted message
     *
     * @return string
     */
    public function compile(): string {
        return json_encode(array_merge([
            'action'    => $this->method,
            'meta'      => $this->meta->dump(),
            'data'      => $this->data
        ], $this->attributes->dump()));
    }

    /**
     * Overwrite meta
     *
     * @param array $meta
     */
    public function setMeta(array $meta) {
        $this->meta->prepare($meta);
    }

    /**
     * Add a meta key
     *
     * @param string $key
     * @param type $value
     */
    public function addMeta(string $key, $value) {
        $this->meta->set($key, $value);
    }

    /**
     * Delete a meta key
     *
     * @param string $key
     */
    public function deleteMeta(string $key) {
        $this->meta->delete($key);
    }

    /**
     * Overwrite attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes) {
        $this->attributes->prepare($attributes);
    }

    /**
     * Add an attribute
     *
     * @param string $key
     * @param mixed $value
     */
    public function addAttribute(string $key, $value) {
        $this->attributes->set($key, $value);
    }

    /**
     * Delete an attribute
     *
     * @param string $key
     */
    public function deleteAttribute(string $key) {
        $this->attributes->delete($key);
    }

}