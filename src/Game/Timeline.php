<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Game;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;

final class Timeline extends Collection
{
    public function merge(self ...$timelines): self
    {
        foreach ($timelines as $timeline) {
            $this->add(...$timeline->items());
        }

        $this->reorder();

        return $this;
    }

    public function totalCardsDrawn(): int
    {
        return $this->totalByValue(EventType::CARDS_DRAWN);
    }

    public function totalCardsDiscarded(): int
    {
        return $this->totalByValue(EventType::CARDS_DISCARDED);
    }

    public function totalExtraTurns(): int
    {
        return $this->filter(EventType::EXTRA_TURN)->count();
    }

    public function totalAmberObtained(): int
    {
        return $this->totalByPayloadValue('delta', EventType::AMBER_OBTAINED);
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
        return $this->totalByValue(EventType::AMBER_STOLEN);
    }

    public function totalCardsPlayed(): int
    {
        return array_reduce(
            $this->filter(EventType::CARDS_PLAYED)->items(),
            static fn (int $c, Event $s): int => $c + count((array) $s->value()),
            0,
        );
    }

    public function propheciesSummary(): array
    {
        $prophecies = [];
        $fates = [];

        $emptyProphecy = ['activated' => 0, 'fulfilled' => 0, 'percent' => 0];

        foreach ($this->filter(EventType::PROPHECY_ACTIVATED)->items() as $event) {
            /** @var string $card */
            $card = $event->value;
            $prophecies[$card] ??= $emptyProphecy;
            ++$prophecies[$card]['activated'];
        }

        $fulfilled = $this->filter(EventType::PROPHECY_FULFILLED);
        foreach ($fulfilled->items() as $event) {
            /** @var string $card */
            $card = $event->value;
            $prophecies[$card] ??= $emptyProphecy;
            ++$prophecies[$card]['fulfilled'];
            $prophecies[$card]['percent'] = round($prophecies[$card]['fulfilled'] * 100 / $fulfilled->count(), 2);
        }

        $fatesResolved = $this->filter(EventType::FATE_RESOLVED);
        foreach ($fatesResolved->items() as $event) {
            /** @var string $card */
            $card = $event->value;
            $fates[$card]['resolved'] = ($fates[$card]['resolved'] ?? 0) + 1;
            $fates[$card]['percent'] = round($fates[$card]['resolved'] * 100 / $fatesResolved->count(), 2);
        }

        uasort($prophecies, static fn ($a, $b) => $b['fulfilled'] <=> $a['fulfilled']);
        uasort($fates, static fn ($a, $b) => $b['resolved'] <=> $a['resolved']);

        return compact('prophecies', 'fates');
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
