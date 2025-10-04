<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser;

use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogPlayerExtractor;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogPreprocessor;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorAmber;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorAmberStolen;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorCardsDiscarded;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorCardsDrawn;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorCardsPlayed;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorChains;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorCheck;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorConcede;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorExtraTurn;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorFight;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorFirstTurn;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorHouses;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorKeysForged;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorLength;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorProphecies;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorReap;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorTide;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorTokens;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorTurnOne;
use AdnanMula\KeyforgeGameLogParser\Parser\Processor\LogProcessorWinner;

final class GameLogParser
{
    public function execute(string|array $log, ParseType $parseType = ParseType::PLAIN): Game
    {
        $messages = new LogPreprocessor()->execute($log, $parseType);
        [$player1, $player2] = new LogPlayerExtractor()->execute(...$messages);
        $game = new Game($player1, $player2, 0, $messages);

        $processors = [
            new LogProcessorAmber(),
            new LogProcessorAmberStolen(),
            new LogProcessorCardsDiscarded(),
            new LogProcessorCardsDrawn(),
            new LogProcessorCardsPlayed(),
            new LogProcessorChains(),
            new LogProcessorCheck(),
            new LogProcessorConcede(),
            new LogProcessorExtraTurn(),
            new LogProcessorFight(),
            new LogProcessorFirstTurn(),
            new LogProcessorHouses(),
            new LogProcessorKeysForged(),
            new LogProcessorLength(),
            new LogProcessorProphecies(),
            new LogProcessorReap(),
            new LogProcessorTide(),
            new LogProcessorTokens(),
            new LogProcessorTurnOne(),
            new LogProcessorWinner(),
        ];

        foreach ($messages as $index => $message) {
            foreach ($processors as $processor) {
                $processor->execute($game, $index, $message);
            }
        }

        return $game;
    }
}
