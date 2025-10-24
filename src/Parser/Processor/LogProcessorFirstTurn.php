<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorFirstTurn implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2) won the flip and is first player/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsFirst(true);
        }

        return $game;
    }
}
