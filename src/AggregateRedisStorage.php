<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;
use Redis;

readonly class AggregateRedisStorage implements AggregateStorage
{
    public function __construct(private Redis $redis)
    {
    }
    public function store(Id $id, array $events): void
    {
        $key = "events:" . $id;
        $serializedEvents = array_map('json_encode', $events);
        $this->redis->rPush($key, ...$serializedEvents);
    }

    public function load(Id $id): array
    {
        $key = "events:" . $id;
        $serializedEvents = $this->redis->lrange($key, 0, -1);
        return array_map(static function ($item) {
            if (!is_string($item)) {
                throw new \UnexpectedValueException('Expected Redis to return a string for event serialization, got: ' . gettype($item));
            }
            return json_decode($item, true);
        }, $serializedEvents);
    }
}
