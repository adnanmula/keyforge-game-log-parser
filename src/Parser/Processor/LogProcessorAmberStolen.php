<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorAmberStolen implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];
        $matches2 = [];
        $matches3 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*from\s*($player1|$player2)$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber$/";
        $pattern3 = "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+(.*)\s+and steal an Æmber$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $value = (int) $matches[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern2, $message, $matches2)) {
            $player = $matches2[1];
            $card = trim($matches2[2]);
            $value = (int) $matches2[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern3, $message, $matches3)) {
            $player = $matches3[1];
            $card = trim($matches3[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    1,
                    ['trigger' => $card],
                ),
            );
        }

        return $game;
    }
}
