<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorTurnOne implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        if ($game->length > 0 || null === $game->first()) {
            return $game;
        }

        $playerGoingFirst = $game->first()->escapedName();

        $pattern = "/^Key phase - $playerGoingFirst/";

        if (preg_match($pattern, $message)) {
            $game->updateLength(1);
        }

        return $game;
    }
}
