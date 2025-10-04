<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Game\Player;

final class LogPlayerExtractor
{
    /** @return array<Player> */
    public function execute(string ...$messages): array
    {
        $players = [];

        $pattern = '/^\s*(.+?)\s+brings\s+(.+?)\s+to The Crucible\s*$/u';

        foreach ($messages as $message) {
            $matches = [];

            if (preg_match($pattern, $message, $matches)) {
                $name = trim($matches[1]);
                $deck = trim($matches[2]);

                if ('' !== $name && false === array_key_exists($name, $players)) {
                    $players[$name] = $deck;
                }
            }

            if (count($players) >= 2) {
                break;
            }
        }

        if (count($players) < 2) {
            foreach ($messages as $message) {
                $matches = [];

                if (preg_match('/^\s*(.+?)\s+has connected to the game server\s*$/u', $message, $matches)) {
                    $name = trim($matches[1]);

                    if ('' !== $name && false === array_key_exists($name, $players)) {
                        $players[$name] = 'Unknown';
                    }
                }

                if (count($players) >= 2) {
                    break;
                }
            }
        }

        $names = array_keys($players);

        if (count($names) < 2) {
            throw new \Exception('Malformed or incomplete log');
        }

        return [
            new Player(name: $names[0], deck: $players[$names[0]] ?? 'Unknown'),
            new Player(name: $names[1], deck: $players[$names[1]] ?? 'Unknown'),
        ];
    }
}
