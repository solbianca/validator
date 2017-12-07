<?php


namespace SolBianca\Validator\Interfaces;


interface MessageBagInterface
{
    /**
     * Checks if the bag has messages for a given key.
     *
     * @param  string $key
     * @return boolean
     */
    public function has(string $key): bool;

    /**
     * Get the first message with a given key.
     * If the given key doesn't exist, it returns the first message of the bag.
     * Returns null if the bag is empty.
     *
     * @param  string $key
     * @return string|null
     */
    public function first(string $key): ?string;

    /**
     * Get all of the messages from a given key.
     * Returns null if the given key is empty, or if it doesn't exist.
     *
     * @param  string $key
     * @return array|null
     */
    public function get(string $key): ?array;

    /**
     * Get all of the messages in the bag.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Return all of the keys in the bag.
     *
     * @return array
     */
    public function keys(): array;

    /**
     * Checks if the bag is empty.
     *
     * @return boolean
     */
    public function isEmpty(): bool;

    /**
     * Get all of the messages in the bag as flat array.
     *
     * @return array
     */
    public function flat(): array;
}