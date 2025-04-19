<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\VO\AmberObtainedCollection;

final class Game implements \JsonSerializable
{
    public function __construct(
        public Player $player1,
        public Player $player2,
        public int $length,
        public array $rawLog,
    ) {}

    public function player(string $name): ?Player
    {
        if ($name === $this->player1->name) {
            return $this->player1;
        }

        if ($name === $this->player2->name) {
            return $this->player2;
        }

        return null;
    }

    public function winner(): ?Player
    {
        if ($this->player1->isWinner) {
            return $this->player1;
        }

        if ($this->player2->isWinner) {
            return $this->player2;
        }

        return null;
    }

    public function loser(): ?Player
    {
        if ($this->player1->isWinner) {
            return $this->player2;
        }

        if ($this->player2->isWinner) {
            return $this->player1;
        }

        return null;
    }

    public function first(): ?Player
    {
        if ($this->player1->isFirst) {
            return $this->player1;
        }

        if ($this->player2->isFirst) {
            return $this->player2;
        }

        return null;
    }

    public function amberObtained(): AmberObtainedCollection
    {
        $result = new AmberObtainedCollection();

        $result->add(
            ...$this->player1->amberObtained->items(),
            ...$this->player2->amberObtained->items(),
        );

        $firstPlayer = $this->first()?->name;

        if (null === $firstPlayer) {
            $firstPlayer = $this->player1->name;
        }

        $result->reorderByTurn($firstPlayer);

        return $result;
    }



    public function totalCardsPlayed(): int
    {
        return $this->player1->cardsPlayed->total() + $this->player2->cardsPlayed->total();
    }

    public function totalCardsDrawn(): int
    {
        return $this->player1->cardsDrawn->total() + $this->player2->cardsDrawn->total();
    }

    public function totalCardsDiscarded(): int
    {
        return $this->player1->cardsDiscarded->total() + $this->player2->cardsDiscarded->total();
    }

    public function totalKeysForged(): int
    {
        return $this->player1->keysForged->count() + $this->player2->keysForged->count();
    }

    public function updateLength(int $value): self
    {
        $this->length = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'player1' => $this->player1->jsonSerialize(),
            'player2' => $this->player2->jsonSerialize(),
            'winner' => $this->winner()?->name,
            'raw_log' => $this->rawLog,
        ];
    }
}
