<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

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
