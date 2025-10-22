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
        $payload = [];
        $matches = [];

        $pattern1 = "/($player1|$player2) discards (.*)$/i";
        $pattern2 = "/($player1|$player2) discards (.+?)\s+due to\s+(.*)\s+bonus icon$/i";
        $pattern3 = "/($player1|$player2) uses .*? to discard(?!.*(?:the top|from their deck))(.*)(?!\s*hand)(?<!hand)$/i";

        if (preg_match($pattern1, $message, $matches)) {
            $player = $matches[1];
            $discardCount = 1;
        } elseif (preg_match($pattern2, $message, $matches)) {
            $player = $matches[1];
            $discardCount = 1;
        } elseif (preg_match($pattern3, $message, $matches)) {
            $player = $matches[1];
            $cards = $this->splitCardString($matches[2]);
            $discardCount = count($cards);
            $payload = ['cards' => $cards];
        }

        $payload['msg'] = $message;

        if ($player !== null && $discardCount > 0) {
            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_DISCARDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $discardCount,
                    $payload,
                ),
            );
        }

        return $game;
    }

    private function splitCardString(string $text): array
    {
        $cards = preg_split('/,\s*/', $text);

        if (false === $cards) {
            return [];
        }

        if (0 === count($cards)) {
            return [];
        }

        $lastCard = array_pop($cards);
        $lastCard = preg_replace('/^and /', '', $lastCard);
        $cards[] = $lastCard;

        return array_map(static fn (?string $s) => trim($s??''), $cards);
    }
}
