<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\VO\AmberObtainedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDiscardedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDrawnCollection;
use AdnanMula\KeyforgeGameLogParser\VO\CardsPlayedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\ExtraTurnCollection;
use AdnanMula\KeyforgeGameLogParser\VO\FightCollection;
use AdnanMula\KeyforgeGameLogParser\VO\HouseChosenCollection;
use AdnanMula\KeyforgeGameLogParser\VO\KeyForgedCollection;
use AdnanMula\KeyforgeGameLogParser\VO\ReapCollection;
use AdnanMula\KeyforgeGameLogParser\VO\AmberStolenCollection;
use AdnanMula\KeyforgeGameLogParser\VO\Timeline;

final class Player implements \JsonSerializable
{
    public function __construct(
        private(set) string $name,
        private(set) string $deck,
        private(set) bool $isFirst = false,
        private(set) bool $isWinner = false,
        private(set) bool $hasConceded = false,
        private(set) AmberObtainedCollection $amberObtained = new AmberObtainedCollection(),
        private(set) KeyForgedCollection $keysForged = new KeyForgedCollection(),
        private(set) CardsDrawnCollection $cardsDrawn = new CardsDrawnCollection(),
        private(set) CardsDiscardedCollection $cardsDiscarded = new CardsDiscardedCollection(),
        private(set) CardsPlayedCollection $cardsPlayed = new CardsPlayedCollection(),
        private(set) HouseChosenCollection $housesChosen = new HouseChosenCollection(),
        private(set) ReapCollection $reaps = new ReapCollection(),
        private(set) FightCollection $fights = new FightCollection(),
        private(set) AmberStolenCollection $amberStolen = new AmberStolenCollection(),
        private(set) ExtraTurnCollection $extraTurns = new ExtraTurnCollection(),
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

    public function timeline(): Timeline
    {
        $timeline = new Timeline();

        $timeline->add(
            ...$this->keysForged->items(),
            ...$this->housesChosen->items(),
            ...$this->amberObtained->items(),
            ...$this->cardsDrawn->items(),
            ...$this->cardsPlayed->items(),
            ...$this->cardsDiscarded->items(),
            ...$this->reaps->items(),
            ...$this->fights->items(),
            ...$this->amberStolen->items(),
            ...$this->extraTurns->items(),
        );

        $timeline->reorder();

        return $timeline;
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
            'house_chosen' => $this->housesChosen->jsonSerialize(),
            'reaps' => $this->reaps->jsonSerialize(),
            'fights' => $this->fights->jsonSerialize(),
            'amber_stolen' => $this->amberStolen->jsonSerialize(),
            'extra_turns' => $this->extraTurns->jsonSerialize(),
        ];
    }
}
