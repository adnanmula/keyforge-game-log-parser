<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorProphecies implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternActivate = "/^($player1|$player2)\s+activates their prophecy\s+(.+)$/";
        $patternFlipped = "/^($player1|$player2)\s+uses\s+(Heads, I Win|Tails, You Lose)\s+to flip\s+(Heads, I Win|Tails, You Lose)\s+to\s+(.*)$/";
        $patternFulfilled = "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy$/";
        $patternFulfilled2 = "/^($player1|$player2)\s+fulfills\s+(.+)\'s\s+prophecy$/";

        if (preg_match($patternActivate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $card,
                ),
            );
        }

        if (preg_match($patternFlipped, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[4]);

            $game->player($player)->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::END, $index),
                    Source::PLAYER,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $source = Source::UNKNOWN;

            if ($card === 'Trust Your Feelings') {
                $player = $game->opponentOf($game->player($player))->name;
                $source = Source::OPPONENT;
            }

            $game->player($player)->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled2, $message, $matches)) {
            $card = trim($matches[2]);
            $source = Source::UNKNOWN;

            if ($card === 'Trust Your Feelings') {
                $source = Source::OPPONENT;
            }

            $player = $game->player($matches[1]);
            $opponent = $game->opponentOf($player);

            $opponent->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $opponent->name,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $card,
                ),
            );
        }

        return $game;
    }
}
