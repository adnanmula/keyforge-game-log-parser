<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Game;

final class Player implements \JsonSerializable
{
    public function __construct(
        private(set) string $name,
        private(set) string $deck,
        private(set) bool $isFirst = false,
        private(set) bool $mulligan = false,
        private(set) bool $isWinner = false,
        private(set) bool $hasConceded = false,
        private(set) int $score = 0,
        private(set) Timeline $timeline = new Timeline(),
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

    public function updateMulligan(bool $value): self
    {
        $this->mulligan = $value;

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

    public function updateScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function addScore(): self
    {
        if ($this->score < 3) {
            ++$this->score;
        }

        return $this;
    }

    public function subtractScore(): self
    {
        if ($this->score > 0) {
            --$this->score;
        }

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
            'mulligan' => $this->mulligan,
            'has_conceded' => $this->hasConceded,
            'timeline' => $this->timeline->jsonSerialize(),
        ];
    }
}
