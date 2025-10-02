<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Game;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;

final class Timeline extends Collection
{
    public function totalCardsDrawn(): int
    {
        return array_reduce(
            $this->filter(EventType::CARDS_DRAWN)->items(),
            static fn (int $c, Event $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalCardsDiscarded(): int
    {
        return array_reduce(
            $this->filter(EventType::CARDS_DISCARDED)->items(),
            static fn (int $c, Event $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalExtraTurns(): int
    {
        return $this->filter(EventType::EXTRA_TURN)->count();
    }

    public function totalAmberObtained(): int
    {
        return array_reduce(
            $this->filter(EventType::AMBER_OBTAINED)->items(),
            static fn (int $c, Event $s): int => $c + $s->payload['delta'],
            0,
        );
    }

    public function totalAmberObtainedPositive(): int
    {
        return array_reduce(
            $this->filter(EventType::AMBER_OBTAINED)->items(),
            static fn (int $c, Event $s): int => $s->payload['delta'] > 0 ? $c + $s->payload['delta'] : $c,
            0,
        );
    }

    public function totalAmberObtainedNegative(): int
    {
        return array_reduce(
            $this->filter(EventType::AMBER_OBTAINED)->items(),
            static fn (int $c, Event $s): int => $s->payload['delta'] < 0 ? $c + $s->payload['delta'] : $c,
            0,
        );
    }

    public function totalAmberStolen(): int
    {
        return array_reduce(
            $this->filter(EventType::AMBER_STOLEN)->items(),
            static fn (int $c, Event $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalCardsPlayed(): int
    {
        return array_reduce(
            $this->filter(EventType::CARDS_PLAYED)->items(),
            static fn (int $c, Event $s): int => $c + count((array) $s->value()),
            0,
        );
    }

    public function totalByValue(EventType ...$types): int
    {
        return array_reduce(
            $this->filter(...$types)->items(),
            static fn (int $c, Event $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalByPayloadValue(string $key, EventType ...$types): int
    {
        return array_reduce(
            $this->filter(...$types)->items(),
            static fn (int $c, Event $s): int => $c + ($s->payload()[$key] ?? 0),
            0,
        );
    }

    public function eventsByTurn(?int $turns = null, EventType ...$types): array
    {
        $result = [];

        foreach ($this->filter(...$types)->items() as $item) {
            $turn = $item->turn()->value();
            $result[$turn] = ($result[$turn] ?? 0) + 1;
        }

        if (null !== $turns) {
            $result = $this->fillMissingTurns($result, $turns);
        }

        return $result;
    }
    public function aggEventsByTurn(?int $turns = null, EventType ...$types): array
    {
        $result = [];

        foreach ($this->filter(...$types)->items() as $item) {
            $turn = $item->turn()->value();
            $result[$turn] = ($result[$turn] ?? 0) + $item->value();
        }

        if (null !== $turns) {
            $result = $this->fillMissingTurns($result, $turns);
        }

        return $result;
    }

    private function fillMissingTurns(array $data, int $turns): array
    {
        $filled = [];

        for ($i = 1; $i <= $turns; ++$i) {
            $filled[$i] = $data[$i] ?? 0;
        }

        return $filled;
    }
}
