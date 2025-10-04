<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorTide implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];
        $matches2 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern1 = "/^($player1|$player2)\s+changed tide\s+to High\s*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.*)\s+to raise the tide\s*$/";

        if (preg_match($pattern1, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::TIDE_RAISED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    'manual',
                ),
            );

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CHAINS_ADDED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    3,
                    ['trigger' => 'Tide'],
                ),
            );
        }

        if (preg_match($pattern2, $message, $matches2)) {
            $game->player($matches2[1])?->timeline->add(
                new Event(
                    EventType::TIDE_RAISED,
                    $matches2[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $matches2[2],
                ),
            );
        }

        return $game;
    }
}
