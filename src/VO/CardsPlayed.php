<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Event;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\TurnMoment;

final readonly class CardsPlayed implements Item
{
    public function __construct(
        private string $player,
        private Turn $turn,
        private array $value,
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
            $array['value'],
        );
    }

    public function type(): Event
    {
        return Event::CARDS_PLAYED;
    }

    public function player(): string
    {
        return $this->player;
    }

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function value(): array
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'type' => $this->type()->name,
            'turn' => $this->turn->jsonSerialize(),
            'value' => $this->value,
        ];
    }
}
