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
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+forges the\s+([^\s]+)\s+key\s*,\s*paying\s+(\d+)\s+Æmber/";

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
        }

        return $game;
    }
}
