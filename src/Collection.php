<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use Countable;
use Iterator;
use JsonSerializable;

/** @implements Iterator<int, Event> */
class Collection implements Iterator, Countable, JsonSerializable
{
    private array $items;
    private int $position = 0;

    protected function __construct(Event ...$items)
    {
        $this->items = $items;
    }

    public function current(): ?Event
    {
        return $this->items[$this->position] ?? null;
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

    /** @return array<Event> */
    public function items(): array
    {
        return $this->items;
    }

    public function add(Event ...$items): static
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function empty(): static
    {
        $this->items = [];
        $this->rewind();

        return $this;
    }

    /** @return ?Event $item */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /** @return ?Event $item */
    public function at(int $index)
    {
        return $this->items[$index] ?? null;
    }

    /** @return ?Event $item */
    public function last()
    {
        return $this->count() > 0 ? end($this->items) : null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function reorder(): static
    {
        if (0 === $this->count()) {
            return $this;
        }

        $events = $this->items();

        usort(
            $events,
            static fn (Event $a, Event $b): int => $a->turn()->occurredOn() <=> $b->turn()->occurredOn(),
        );

        $this->empty();

        $this->add(...$events);

        return $this;
    }

    public function filter(EventType ...$events): static
    {
        $items = array_filter(
            $this->items,
            static fn (Event $item): bool => in_array($item->type(), $events, true),
        );

        return new static(...$items);
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn (JsonSerializable $item) => $item->jsonSerialize(),
            $this->items,
        );
    }
}
