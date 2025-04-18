<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\VO\AmberObtainedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDiscardedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDrawnCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsPlayedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\HouseChosenCollection;
use AdnanMula\KeyforgeGameLogParser\VO\KeyForgedCollection;

final class Player implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $deck,
        public bool $isFirst = false,
        public bool $isWinner = false,
        public bool $hasConceded = false,
        public AmberObtainedCollection $amberObtained = new AmberObtainedCollection(),
        public KeyForgedCollection $keysForged = new KeyForgedCollection(),
        public CardsDrawnCollection $cardsDrawn = new CardsDrawnCollection(),
        public CardsDiscardedCollection $cardsDiscarded = new CardsDiscardedCollection(),
        public CardsPlayedCollection $cardsPlayed = new CardsPlayedCollection(),
        public HouseChosenCollection $housesPlayed = new HouseChosenCollection(),
    ) {}

    public function escapedName(): string
    {
        return preg_quote($this->name, '/');
    }
    
    public function updateIsFirst(bool $value): self
    {
        $this->isFirst = $value;

        return $this;
    }

    public function updateIsWinner(bool $value): self
    {
        $this->isWinner = $value;

        return $this;
    }

    public function updateHasConceded(bool $value): self
    {
        $this->hasConceded = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'escaped_name' => $this->escapedName(),
            'deck' => $this->deck,
            'is_first' => $this->isFirst,
            'is_winner' => $this->isWinner,
            'has_conceded' => $this->hasConceded,
            'amber_obtained' => $this->amberObtained->jsonSerialize(),
            'keys_forged' => $this->keysForged->jsonSerialize(),
            'cards_played' => $this->cardsPlayed->jsonSerialize(),
            'cards_drawn' => $this->cardsDrawn->jsonSerialize(),
            'cards_discarded' => $this->cardsDiscarded->jsonSerialize(),
        ];
    }
}
