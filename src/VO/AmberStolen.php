<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Event;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Source;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\TurnMoment;

final class AmberStolen implements Item
{
    public function __construct(
        private string $player,
        private Turn $turn,
        private Source $source,
        private string $trigger,
        private int $value,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            $array['player'],
            new Turn(
                $array['turn']['value'],
                TurnMoment::from($array['turn']['moment']),
                $array['turn']['occurredOn'],
            ),
            Source::from($array['source']),
            $array['trigger'],
            $array['value'],
        );
    }

    public function type(): Event
    {
        return Event::AMBER_STOLEN;
    }

    public function player(): string
    {
        return $this->player;
    }

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function trigger(): string
    {
        return $this->trigger;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'type' => $this->type()->name,
            'turn' => $this->turn,
            'source' => $this->source->name,
            'trigger' => $this->trigger,
            'value' => $this->value,
        ];
    }
}
