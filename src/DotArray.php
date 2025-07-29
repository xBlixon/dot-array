<?php

namespace Blixon\DotArray;

class DotArray implements \ArrayAccess
{
    private array $array;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    public function offsetExists(mixed $offset): bool
    {
    }

    public function offsetGet(mixed $offset): mixed
    {
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public static function splitKey($key): array
    {
        $split = explode(".", $key); // Splits keys by . into an array
        $filtered = array_filter($split, function ($item) {
            return $item != "";
        }); // Gets rid of empty keys ("")
        $reindexed = array_values($filtered); //In case where filtering makes array start from >0
        return $reindexed;
    }
}