<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorFight implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(.+)fight(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $trigger = trim($matches[2]);
            $value = trim($matches[3]);
            $target = trim($matches[4]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FIGHT,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $trigger, 'target' => $target],
                ),
            );
        }

        return $game;
    }
}
