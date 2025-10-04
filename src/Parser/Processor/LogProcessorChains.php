<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorChains implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];
        $matches2 = [];
        $matches3 = [];
        $matches4 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)'s\s+chains are reduced by\s+(\d+)\s+to\s+(\d+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CHAINS_REDUCED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    (int) $matches[2],
                    ['currentChains' => (int) $matches[3]],
                ),
            );
        }

        $pattern2 = "/^($player1|$player2)\s+uses\s+(.*)\s+to increase their chains by\s+(\d+)\s*$/";

        if (preg_match($pattern2, $message, $matches2)) {
            $game->player($matches2[1])?->timeline->add(
                new Event(
                    EventType::CHAINS_ADDED,
                    $matches2[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    (int) $matches2[3],
                    ['trigger' => $matches2[2]],
                ),
            );
        }

        $pattern3 = "/^($player1|$player2)\s+uses\s+(.*)\s+to give\s+($player1|$player2)\s+(\d+)\s*chains\s*$/";

        if (preg_match($pattern3, $message, $matches3)) {
            $game->player($matches3[3])?->timeline->add(
                new Event(
                    EventType::CHAINS_ADDED,
                    $matches3[3],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::OPPONENT,
                    (int) $matches3[4],
                    ['trigger' => $matches3[2]],
                ),
            );
        }

        $chainData = [
            'Ballcano' => 2,
            "Coward’s End" => 3,
            'Power of Fire' => 1,
            'Arise!' => 1,
            'Grim Reminder' => 1,
            'Gateway to Dis' => 3,
            'Market Crash' => 2,
            'Shell of a Ghost' => 2,
            'Effervescent Principle' => 1,
            'Extinction' => 1,
            'Kaboom!' => 3,
            'Phosphorus Stars' => 2,
            'Crushing Charge' => 1,
            'Catch and Release' => 2,
            'Mælstrom' => 2,
            'Save the Pack' => 1,
            'Quintrino Warp' => 1,
            'Axiom of Grisk' => 2,
            'Krrrzzzaaap!!!' => 1,
            'They Tell No Tales' => 2,
        ];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern4 = "/^($player1|$player2)\s+plays\s+(.+)\s*$/";

        if (preg_match($pattern4, $message, $matches4)) {
            $player = $matches4[1];
            $card = trim($matches4[2]);
            $chains = $chainData[$card] ?? 0;

            if ($chains > 0) {
                $game->player($player)?->timeline->add(
                    new Event(
                        EventType::CHAINS_ADDED,
                        $player,
                        new Turn($game->length, Moment::BETWEEN, $index),
                        Source::PLAYER,
                        $chains,
                        ['trigger' => $card],
                    ),
                );
            }
        }

        return $game;
    }
}
