<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

use Countable;
use Iterator;
use JsonSerializable;

/**
 * @template T
 * @implements Iterator<int, T>
 */
class Collection implements Iterator, Countable, JsonSerializable
{
    private array $items;
    private int $position = 0;

    protected function __construct(Item ...$items)
    {
        $this->items = $items;
    }

    public function current(): ?Item
    {
        return $this->steps[$this->position] ?? null;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->items);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /** @return array<T> */
    public function items(): array
    {
        return $this->items;
    }

    /** @param T $item */
    public function add($item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @return ?T $item */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /** @return ?T $item */
    public function last()
    {
        return $this->count() > 0 ? end($this->items) : null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn (JsonSerializable $item) => $item->jsonSerialize(),
            $this->items,
        );
    }
}
