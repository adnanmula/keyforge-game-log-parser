<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorLength implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        if (preg_match('/Turn (\d+) -/i', $message, $matches)) {
            $game->updateLength(max($game->length, (int) $matches[1]));
        }

        return $game;
    }
}
