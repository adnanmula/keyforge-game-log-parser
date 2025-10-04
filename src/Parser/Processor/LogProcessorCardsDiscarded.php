<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorCardsDiscarded implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $player = null;
        $discardCount = 0;
        $source = Source::PLAYER;
        $matches = [];

        $pattern1 = "/($player1|$player2) uses .*? to discard the top (\d+) cards/i";
        $pattern2 = "/($player1|$player2) uses .*? to discard (.+?)(?:$| and| to| at| from)/i";
        $pattern3 = "/($player1|$player2) discards (.+?)(?:$| and| to| due| at)/i";
        $pattern4 = "/($player1|$player2) uses .*? to discard a card.*?from ($player1|$player2)'s hand/i";

        if (preg_match($pattern1, $message, $matches)) {
            $player = $matches[1];
            $discardCount = (int) $matches[2];
        } elseif (preg_match($pattern2, $message, $matches)) {
            $player = $matches[1];
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern3, $message, $matches)) {
            $player = $matches[1];
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern4, $message, $matches)) {
            $player = $matches[2];
            $discardCount = 1;
            $source = Source::OPPONENT;
        }

        if ($player !== null && $discardCount > 0) {
            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_DISCARDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $discardCount,
                ),
            );
        }

        return $game;
    }
}
