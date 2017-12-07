<?php


namespace SolBianca\Validator;

use SolBianca\Validator\Interfaces\MessageBagInterface;

class MessageBag implements MessageBagInterface
{
    /**
     * The registered messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Creates a new MessageBag instance.
     *
     * @param array $messages
     */
    public function __construct(array $messages)
    {
        foreach ($messages as $key => $value) {
            $this->messages[$key] = (array)$value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return !is_null($this->first($key));
    }

    /**
     * {@inheritdoc}
     */
    public function first(string $key = null): ?string
    {
        $messages = is_null($key) ? $this->flat() : $this->get($key);
        return (count($messages) > 0) ? $messages[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?array
    {
        if (array_key_exists($key, $this->messages)) {
            return !empty($this->messages[$key]) ? $this->messages[$key] : null;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function keys(): array
    {
        return array_keys($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function flat(): array
    {
        return iterator_to_array(new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($this->messages)
        ), false);
    }
}