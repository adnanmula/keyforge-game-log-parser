<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorWinner implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) has won the game$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsWinner(true);
        }

        return $game;
    }
}
