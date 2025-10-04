<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorAmber implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)\s+($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)/";

        if (preg_match($pattern, $message, $matches)) {
            $currentPlayer1 = $game->player($matches[1]);
            $player1Last = $currentPlayer1?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment1 = $player1Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged1 = 0;

            if (null !== $currentPlayer1) {
                foreach ($currentPlayer1->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged1 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[1],
                    new Turn($game->length, $turnMoment1, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                    [
                        'keys' => (int) $matches[3],
                        'delta' => (int) $matches[2] - ($player1Last?->value() ?? 0) + $adjustKeyForged1,
                    ],
                ),
            );


            $currentPlayer2 = $game->player($matches[4]);
            $player2Last = $currentPlayer2?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment2 = $player2Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged2 = 0;

            if (null !== $currentPlayer2) {
                foreach ($currentPlayer2->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged2 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[4])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[4],
                    new Turn($game->length, $turnMoment2, $index),
                    Source::UNKNOWN,
                    (int) $matches[5],
                    [
                        'keys' => (int) $matches[6],
                        'delta' => (int) $matches[5] - ($player2Last?->value() ?? 0) + $adjustKeyForged2,
                    ],
                ),
            );
        }

        return $game;
    }
}
