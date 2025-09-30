<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\Event\AmberObtained;
use AdnanMula\KeyforgeGameLogParser\Event\AmberStolen;
use AdnanMula\KeyforgeGameLogParser\Event\CardsDiscarded;
use AdnanMula\KeyforgeGameLogParser\Event\CardsDrawn;
use AdnanMula\KeyforgeGameLogParser\Event\CardsPlayed;

final class Timeline extends Collection
{
    public function __construct(Event ...$item)
    {
        parent::__construct(...$item);
    }

    public function totalCardsDrawn(): int
    {
        /** @var array<CardsDrawn> $events */
        $events = $this->filter(EventType::CARDS_DRAWN)->items();

        return array_reduce(
            $events,
            static fn (int $c, CardsDrawn $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalCardsDiscarded(): int
    {
        /** @var array<CardsDiscarded> $events */
        $events = $this->filter(EventType::CARDS_DISCARDED)->items();

        return array_reduce(
            $events,
            static fn (int $c, CardsDiscarded $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalExtraTurns(): int
    {
        return $this->filter(EventType::EXTRA_TURN)->count();
    }

    public function totalAmberObtained(): int
    {
        /** @var array<AmberObtained> $events */
        $events = $this->filter(EventType::AMBER_OBTAINED)->items();

        return array_reduce(
            $events,
            static fn (int $c, AmberObtained $s): int => $c + $s->delta(),
            0,
        );
    }

    public function totalAmberObtainedPositive(): int
    {
        /** @var array<AmberObtained> $events */
        $events = $this->filter(EventType::AMBER_OBTAINED)->items();

        return array_reduce(
            $events,
            static fn (int $c, AmberObtained $s): int => $s->delta() > 0 ? $c + $s->delta() : $c,
            0,
        );
    }

    public function totalAmberObtainedNegative(): int
    {
        /** @var array<AmberObtained> $events */
        $events = $this->filter(EventType::AMBER_OBTAINED)->items();

        return array_reduce(
            $events,
            static fn (int $c, AmberObtained $s): int => $s->delta() < 0 ? $c + $s->delta() : $c,
            0,
        );
    }

    public function totalAmberStolen(): int
    {
        /** @var array<AmberStolen> $events */
        $events = $this->filter(EventType::AMBER_STOLEN)->items();

        return array_reduce(
            $events,
            static fn (int $c, AmberStolen $s): int => $c + $s->value(),
            0,
        );
    }

    public function totalCardsPlayed(): int
    {
        /** @var array<CardsPlayed> $events */
        $events = $this->filter(EventType::CARDS_PLAYED)->items();

        return array_reduce(
            $events,
            static fn (int $c, CardsPlayed $s): int => $c + count($s->value()),
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

        for ($i = 1; $i <= $turns; $i++) {
            $filled[$i] = $data[$i] ?? 0;
        }

        return $filled;
    }
}
