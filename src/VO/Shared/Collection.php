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

    /** @param array<T> $items */
    public function add(...$items): static
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

    public function reorderByTurn(string $firstPlayer): static
    {
        if (0 === $this->count()) {
            return $this;
        }

        $grouped = [];

        foreach ($this->jsonSerialize() as $item) {
            $turn = $item['turn']['value'];
            $grouped[$turn][] = $item;
        }

        ksort($grouped);

        $resultItems = [];

        foreach ($grouped as $items) {
            $player1 = [];
            $player2 = [];

            $gameLength = 1;
            foreach ($items as $item) {
                $gameLength = max($gameLength, $item['turn']['value']);

                if ($item['player'] === $firstPlayer) {
                    $player1[$item['turn']['value']][$item['turn']['moment']][] = $item;
                } else {
                    $player2[$item['turn']['value']][$item['turn']['moment']][] = $item;
                }
            }

            $moments = [TurnMoment::START->name, TurnMoment::BETWEEN->name, TurnMoment::END->name];
            $players = [$player1, $player2];

            for ($i = 1; $i <= $gameLength; $i++) {
                foreach ($players as $player) {
                    foreach ($moments as $moment) {
                        if (false === array_key_exists($i, $player)
                            || false === array_key_exists($moment, $player[$i])) {
                            continue;
                        }

                        foreach ($player[$i][$moment] as $item) {
                            $resultItems[] = $item;
                        }
                    }
                }
            }
        }

        /** @var class-string<T> $itemClass */
        $itemClass = $this->items[0]::class;

        $this->empty();

        foreach ($resultItems as $item) {
            $this->add($itemClass::fromArray($item));
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_map(
            static fn (JsonSerializable $item) => $item->jsonSerialize(),
            $this->items,
        );
    }
}
