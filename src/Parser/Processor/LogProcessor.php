<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Game;

interface LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game;
}
