<?php

namespace Blixon\DotArray;

use function PHPUnit\Framework\isString;

class DotArray implements \ArrayAccess
{
    private array $array;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    public function getRawArray(): array
    {
        return $this->array;
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->checkOffset($offset);
        $keys = self::splitKey($offset);
        $array = &$this->array;

        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
            $array = &$array[$key];
        }
        return true;
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->checkOffset($offset);
        $keys = self::splitKey($offset);
        $array = &$this->array;
        foreach ($keys as $key) {
            if (end($keys) === $key) {
                return $array[$key];
            }
            elseif (is_array($array[$key]))
            {
                $array = &$array[$key];
            }
            else {
                throw new IllegalAccessException(
                    "Trying to access sub-array on 
                    key '$key' whose value is of type " . gettype($array[$key])
                );
            }
        }
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkOffset($offset);
        $array = &$this->array;
        $keys = self::splitKey($offset);
        foreach ($keys as $key) {
            $array = &$array[$key];
        }
        $array = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->checkOffset($offset);
        $keys = self::splitKey($offset);
        $array = &$this->array;

        // Traverse to the parent of the last key
        for ($i = 0; $i < count($keys) - 1; $i++) {
            $key = $keys[$i];
            if (!isset($array[$key])) {
                return; // Key path doesn't exist
            }
            $array = &$array[$key];
        }

        // Unset the final key
        $lastKey = end($keys);
        unset($array[$lastKey]);
    }

    private function checkOffset(mixed $offset): void
    {
        if (gettype($offset) != 'string') {
            throw new IllegalAccessException("Key must be a string");
        }
        if ($offset == '') {
            throw new IllegalAccessException('Key cannot be an empty string ("")');
        }
    }

    public static function splitKey($key): array
    {
        $split = explode(".", $key); // Splits keys by . into an array
        $filtered = array_filter($split, function ($item) {
            return $item != "";
        }); // Gets rid of empty keys ("")
        $reindexed = array_values($filtered); //In case where filtering makes array start from >0

        if (count($reindexed) == 0)
        {
            throw new IllegalAccessException(
                "Illegal key. Parsing original key has been reduced to
                0 sub-keys. Example: '.' Passed: '$key'"
            );
        }

        return $reindexed;
    }
}