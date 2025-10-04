<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorConcede implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) concedes\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];

            $game->player($player)?->updateHasConceded(true);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PLAYER_CONCEDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    '',
                ),
            );
        }

        return $game;
    }
}
