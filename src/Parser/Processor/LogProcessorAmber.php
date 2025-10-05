<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\Game\Player;

final class LogProcessorAmber implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)\s+($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)/";

        if (preg_match($pattern, $message, $matches)) {
            $this->processPlayer($game, $index, $matches[1], (int) $matches[2], (int) $matches[3]);
            $this->processPlayer($game, $index, $matches[4], (int) $matches[5], (int) $matches[6]);
        }

        return $game;
    }

    private function processPlayer(Game $game, int $index, string $playerName, int $amber, int $keys): void
    {
        $player = $game->player($playerName);

        if (null === $player) {
            return;
        }

        $lastEvent = $player->timeline->filter(EventType::AMBER_OBTAINED)->last();

        $player->timeline->add(
            new Event(
                EventType::AMBER_OBTAINED,
                $player->name,
                new Turn(
                    $game->length,
                    $this->calculateTurn($game, $player, $lastEvent),
                    $index,
                ),
                Source::UNKNOWN,
                $amber,
                [
                    'keys' => $keys,
                    'delta' => $amber - ($lastEvent?->value() ?? 0) + $this->totalSpentOnKeys($game, $player),
                ],
            ),
        );
    }

    private function totalSpentOnKeys(Game $game, Player $player): int
    {
        $adjustKeyForged = 0;

        foreach ($player->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
            if ($keyForged->turn()->value() === $game->length) {
                $adjustKeyForged += $keyForged->payload['amberCost'];
            }
        }

        return $adjustKeyForged;
    }

    private function calculateTurn(Game $game, Player $player, ?Event $lastEvent): Moment
    {
        if (null === $lastEvent) {
            if ($player->isFirst) {
                return Moment::END;
            }

            return Moment::START;
        }

        if ($game->length === 1) {
            if ($player->isFirst) {
                return Moment::START;
            }

            return Moment::END;
        }

        return $lastEvent->turn()->moment() === Moment::START ? Moment::END : Moment::START;
    }
}
