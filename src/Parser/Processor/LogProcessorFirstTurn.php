<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorFirstTurn implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern1 = "/^($player1|$player2) won the flip and is first player/";
        $pattern2 = "/^($player1|$player2) chooses to go first/";
        $pattern3 = "/^($player1|$player2) chooses to go second/";
        $patternMulligan = "/^($player1|$player2) mulligans their starting hand/";

        if (preg_match($pattern1, $message, $matches)) {
            $game->player($matches[1])->updateIsFirst(true);

            return $game;
        }

        if (preg_match($pattern2, $message, $matches)) {
            $game->player($matches[1])->updateIsFirst(true);

            return $game;
        }

        if (preg_match($pattern3, $message, $matches)) {
            $player = $game->player($matches[1]);
            $opponent = $game->opponentOf($player);

            $player->updateIsFirst(false);
            $opponent->updateIsFirst(true);

            return $game;
        }

        if (preg_match($patternMulligan, $message, $matches)) {
            $game->player($matches[1])->updateMulligan(true);

            return $game;
        }

        return $game;
    }
}
