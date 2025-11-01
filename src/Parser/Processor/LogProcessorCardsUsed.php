<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorCardsUsed implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+?)(?=\s+to\s+.*$)\s+to\s+(.*)$/";

        $otherUses = [
            "/^($player1|$player2)\s+uses\s+(.+)\s+to reap with\s+(.+)$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(.+)fight(.+)$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*from\s*($player1|$player2)$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+(.*)\s+and steal an Æmber$/",
            "/^($player1|$player2)\s+uses\s+(.*)\s+to increase their chains by\s+(\d+)$/",
            "/^($player1|$player2)\s+uses\s+(.*)\s+to give\s+($player1|$player2)\s+(\d+)\s*chains$/",
            "/($player1|$player2)\s+uses\s+(.+)\s+to take another turn after this one\s*(.*)$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to make a token creature\s*.*$/",
            "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(\d+)\s+token creatures\s*.*$/",
        ];

        if (preg_match($pattern, $message, $matches)) {
            if (array_any($otherUses, static fn (string $otherUse): bool => (bool) preg_match($otherUse, $message))) {
                return $game;
            }

            $player = $matches[1];
            $card = trim($matches[2]);
            $effect = ucfirst(trim($matches[3]));

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARD_USED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $card,
                    ['effect' => $effect],
                ),
            );
        }

        return $game;
    }
}
