<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorProphecyAskAgainLater implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternAskAgainLater = "/^($player1|$player2)\s+uses Ask Again Later to make\s+($player1|$player2)\s+name house\s+(.*)$/";
        $patternRevealByAskAgainLater = "/^Ask Again Later reveals\s+(.*)$/";
        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)$/";
        $patternMainPhase = "/^Main phase - ($player1|$player2)$/";

        if (preg_match($patternAskAgainLater, $message, $matches)) {
            $nextMessage = $messages[$index + 1] ?? null;

            if (null !== $nextMessage && preg_match($patternRevealByAskAgainLater, $nextMessage, $matches)) {
                $forPlayer = null;

                for ($lookaheadIndex = 2; $lookaheadIndex <= 10; ++$lookaheadIndex) {
                    $nextMessage = $messages[$index + $lookaheadIndex] ?? null;

                    if (null === $nextMessage) {
                        break;
                    }

                    if (preg_match($patternFate, $nextMessage, $matches)) {
                        $forPlayer = $matches[1];

                        break;
                    }

                    if (preg_match($patternMainPhase, $nextMessage, $matches)) {
                        break;
                    }
                }

                if (null !== $forPlayer) {
                    $game->player($forPlayer)?->timeline->add(
                        new Event(
                            EventType::PROPHECY_FULFILLED,
                            $forPlayer,
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
