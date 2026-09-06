<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorKeysForged implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $this->forge($message, $index, $player1, $player2, $game);
        $this->unforge($message, $index, $player1, $player2, $game);

        return $game;
    }

    private function forge(string $message, int $index, string $player1, string $player2, Game $game): void
    {
        $pattern = "/^($player1|$player2)\s+forges the\s+([^\s]+)\s+key\s*,\s*paying\s+(\d+)\s+Æmber/";
        $matches = [];

        if (preg_match($pattern, $message, $matches)) {
            $currentAmber = $game->player($matches[1])?->timeline->filter(EventType::AMBER_OBTAINED)?->last()?->value() ?? 0;
            $cost = (int) $matches[3];
            $remaining = max(0, $currentAmber - $cost);

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::KEY_FORGED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $matches[2],
                    [
                        'amberCost' => (int) $matches[3],
                        'amberRemaining' => $remaining,
                    ],
                ),
            );

            $game->player($matches[1])?->addScore();
        }
    }

    private function unforge(string $message, int $index, string $player1, string $player2, Game $game): void
    {
        $pattern1 = "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+cause\s+($player1|$player2)\s+to\s+unforge\s+a\s+key\s*/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+unforge\s+an\s+opponent's\s+key/";
        $matches = [];
        $matches2 = [];

        if (preg_match($pattern1, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::KEY_UNFORGED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $matches[2],
                    [
                        'card' => $matches[2],
                    ],
                ),
            );

            $game->player($matches[1])?->subtractScore();
        }

        if (preg_match($pattern2, $message, $matches2)) {
            $game->player($matches2[1])?->timeline->add(
                new Event(
                    EventType::KEY_UNFORGED,
                    $matches2[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $matches2[2],
                    [
                        'card' => $matches2[2],
                    ],
                ),
            );

            $game->player($matches2[1])?->subtractScore();
        }
    }
}
