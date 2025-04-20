<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\VO\AmberObtainedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDiscardedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDrawnCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsPlayedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\KeyForgedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\Timeline;

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

        $result->reorder();

        return $result;
    }

    public function cardsPlayed(): CardsPlayedCollection
    {
        $result = new CardsPlayedCollection();

        $result->add(
            ...$this->player1->cardsPlayed->items(),
            ...$this->player2->cardsPlayed->items(),
        );

        $result->reorder();

        return $result;
    }

    public function cardsDrawn(): CardsDrawnCollection
    {
        $result = new CardsDrawnCollection();

        $result->add(
            ...$this->player1->cardsDrawn->items(),
            ...$this->player2->cardsDrawn->items(),
        );

        $result->reorder();

        return $result;
    }

    public function cardsDiscarded(): CardsDiscardedCollection
    {
        $result = new CardsDiscardedCollection();

        $result->add(
            ...$this->player1->cardsDiscarded->items(),
            ...$this->player2->cardsDiscarded->items(),
        );

        $result->reorder();

        return $result;
    }

    public function keysForged(): KeyForgedCollection
    {
        $result = new KeyForgedCollection();

        $result->add(
            ...$this->player1->keysForged->items(),
            ...$this->player2->keysForged->items(),
        );

        $result->reorder();

        return $result;
    }

    public function updateLength(int $value): self
    {
        $this->length = $value;

        return $this;
    }

    public function timeline(): Timeline
    {
        $timeline = new Timeline();

        $timeline->add(
            ...$this->player1->timeline()->items(),
            ...$this->player2->timeline()->items(),
        );

        $timeline->reorder();

        return $timeline;
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
