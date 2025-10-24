<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorTokens implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $matches = [];
        $matches2 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make a token creature\s*.*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(\d+)\s+token creatures\s*.*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::TOKEN_CREATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($pattern2, $message, $matches2)) {
            $player = $matches2[1];
            $card = trim($matches2[2]);
            $amount = (int) $matches2[3];

            for ($i = 0; $i < $amount; ++$i) {
                $game->player($player)?->timeline->add(
                    new Event(
                        EventType::TOKEN_CREATED,
                        $player,
                        new Turn($game->length, Moment::BETWEEN, $index),
                        Source::UNKNOWN,
                        $card,
                    ),
                );
            }
        }

        return $game;
    }
}
