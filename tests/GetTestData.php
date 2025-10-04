<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;

trait GetTestData
{
    private function getLog(string $file): Game
    {
        $log = file_get_contents('tests/data/' . $file . '.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();

        return $parser->execute($log);
    }
}
