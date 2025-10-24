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
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)$/";
        $patternActivate = "/^($player1|$player2)\s+activates their prophecy\s+(.+)$/";
        $patternFulfilled = "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy$/";
        $patternFlipped = "/^($player1|$player2)\s+uses\s+(Heads, I Win|Tails, You Lose)\s+to flip\s+(Heads, I Win|Tails, You Lose)\s+to\s+(.*)$/";

        if (preg_match($patternFate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FATE_RESOLVED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternActivate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternFlipped, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[4]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::END, $index),
                    Source::PLAYER,
                    $card,
                ),
            );
        }

        $patternAskAgainLater = "/^($player1|$player2)\s+uses Ask Again Later to make\s+($player1|$player2)\s+name house\s+(.*)$/";
        $patternRevealByAskAgainLater = "/^Ask Again Later reveals\s+(.*)$/";

        if (preg_match($patternAskAgainLater, $message, $matches)) {
            $nextMessage = $messages[$index + 1] ?? null;

            if (null !== $nextMessage && preg_match($patternRevealByAskAgainLater, $nextMessage, $matches)) {
                $nextMessage = $messages[$index + 2] ?? null;

                if (null !== $nextMessage && preg_match($patternFate, $nextMessage, $matches)) {
                    $player = $matches[1];

                    $game->player($player)?->timeline->add(
                        new Event(
                            EventType::PROPHECY_FULFILLED,
                            $player,
                            new Turn($game->length, Moment::BETWEEN, $index),
                            Source::PLAYER,
                            'Ask Again Later',
                        ),
                    );
                }
            }
        }

        return $game;
    }
}
