<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Game;

use AdnanMula\KeyforgeGameLogParser\Parser\Exception\PlayerNotInGameException;

final class Game implements \JsonSerializable
{
    public function __construct(
        private(set) Player $player1,
        private(set) Player $player2,
        private(set) int $length,
        private(set) array $rawLog,
    ) {}

    public function player(string $name): Player
    {
        if ($name === $this->player1->name) {
            return $this->player1;
        }

        if ($name === $this->player2->name) {
            return $this->player2;
        }

        throw new PlayerNotInGameException($name);
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

    public function opponentOf(Player|string $player): Player
    {
        if (is_string($player)) {
            $player = $this->player($player);
        }

        if (false === in_array($player->name, [$this->player1->name, $this->player2->name], true)) {
            throw new PlayerNotInGameException($player->name);
        }

        return $player->name === $this->player1->name
            ? $this->player2
            : $this->player1;
    }

    public function updateLength(int $value): self
    {
        $this->length = $value;

        return $this;
    }

    public function timeline(): Timeline
    {
        $timeline = new Timeline();

        $timeline->merge(
            $this->player1->timeline,
            $this->player2->timeline,
        );

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
