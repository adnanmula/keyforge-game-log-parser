<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class GameLogParser
{
    public function execute(string|array $log, ParseType $parseType = ParseType::PLAIN): Game
    {
        $messages = new LogPreprocessor()->execute($log, $parseType);
        [$player1, $player2] = new LogPlayerExtractor()->execute(...$messages);
        $game = new Game($player1, $player2, 0, $messages);

        foreach ($messages as $index => $message) {
            $this->checkFirstTurn($game, $message);
            $this->checkTurnOne($game, $message);
            $this->checkLength($game, $message);
            $this->checkAmber($game, $index, $message);
            $this->checkKeysForged($game, $index, $message);
            $this->checkHouses($game, $index, $message);
            $this->checkCardsDrawn($game, $index, $message);
            $this->checkChains($game, $index, $message);
            $this->checkCardsDiscarded($game, $index, $message);
            $this->checkCardsPlayed($game, $index, $message);
            $this->checkAmberStolen($game, $index, $message);
            $this->checkReap($game, $index, $message);
            $this->checkFight($game, $index, $message);
            $this->checkTide($game, $index, $message);
            $this->checkExtraTurn($game, $index, $message);
            $this->checkTokens($game, $index, $message);
            $this->checkProphecies($game, $index, $message);
            $this->checkWinner($game, $message);
            $this->checkConcede($game, $index, $message);
            $this->checkCheck($game, $index, $message);
        }

        return $game;
    }

    private function checkTurnOne(Game $game, string $message): void
    {
        if ($game->length > 0 || null === $game->first()) {
            return;
        }

        $playerGoingFirst = $game->first()->escapedName();

        $pattern = "/^Key phase - $playerGoingFirst/";

        if (preg_match($pattern, $message)) {
            $game->updateLength(1);
        }
    }

    private function checkLength(Game $game, string $message): void
    {
        $matches = [];

        if (preg_match('/Turn (\d+) -/i', $message, $matches)) {
            $game->updateLength(max($game->length, (int) $matches[1]));
        }
    }

    private function checkFirstTurn(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2) won the flip and is first player/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsFirst(true);
        }
    }

    private function checkKeysForged(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+forges the\s+([^\s]+)\s+key\s*,\s*paying\s+(\d+)\s+Æmber/";

        if (preg_match($pattern, $message, $matches)) {
            $currentAmber = $game->player($matches[1])?->timeline->filter(EventType::AMBER_OBTAINED)?->last()?->value() ?? 0;
            $cost = (int) $matches[3];
            $remaining = max(0, $currentAmber - $cost);

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::KEY_FORGED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $matches[2],
                    [
                        'amberCost' => (int) $matches[3],
                        'amberRemaining' => $remaining,
                    ],
                ),
            );
        }
    }

    private function checkAmber(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)\s+($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)/";

        if (preg_match($pattern, $message, $matches)) {
            $currentPlayer1 = $game->player($matches[1]);
            $player1Last = $currentPlayer1?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment1 = $player1Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged1 = 0;

            if (null !== $currentPlayer1) {
                foreach ($currentPlayer1->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged1 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[1],
                    new Turn($game->length, $turnMoment1, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                    [
                        'keys' => (int) $matches[3],
                        'delta' => (int) $matches[2] - ($player1Last?->value() ?? 0) + $adjustKeyForged1,
                    ],
                ),
            );


            $currentPlayer2 = $game->player($matches[4]);
            $player2Last = $currentPlayer2?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment2 = $player2Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged2 = 0;

            if (null !== $currentPlayer2) {
                foreach ($currentPlayer2->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged2 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[4])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[4],
                    new Turn($game->length, $turnMoment2, $index),
                    Source::UNKNOWN,
                    (int) $matches[5],
                    [
                        'keys' => (int) $matches[6],
                        'delta' => (int) $matches[5] - ($player2Last?->value() ?? 0) + $adjustKeyForged2,
                    ],
                ),
            );
        }
    }

    private function checkHouses(Game $game, int $index, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+chooses\s+(\w*)\s+as their active house this turn\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::HOUSE_CHOSEN,
                    $matches[1],
                    new Turn($game->length, Moment::START, $index),
                    Source::UNKNOWN,
                    $matches[2],
                ),
            );
        }
    }

    private function checkCardsDrawn(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+draws\s+(\d+)\s+card/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CARDS_DRAWN,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                ),
            );
        }
    }

    private function checkChains(Game $game, int $index, string $message): void
    {
        $matches = [];
        $matches2 = [];
        $matches3 = [];
        $matches4 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)'s\s+chains are reduced by\s+(\d+)\s+to\s+(\d+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CHAINS_REDUCED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    (int) $matches[2],
                    ['currentChains' => (int) $matches[3]],
                ),
            );
        }

        $pattern2 = "/^($player1|$player2)\s+uses\s+(.*)\s+to increase their chains by\s+(\d+)\s*$/";

        if (preg_match($pattern2, $message, $matches2)) {
            $game->player($matches2[1])?->timeline->add(
                new Event(
                    EventType::CHAINS_ADDED,
                    $matches2[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    (int) $matches2[3],
                    ['trigger' => $matches2[2]],
                ),
            );
        }

        $pattern3 = "/^($player1|$player2)\s+uses\s+(.*)\s+to give\s+($player1|$player2)\s+(\d+)\s*chains\s*$/";

        if (preg_match($pattern3, $message, $matches3)) {
            $game->player($matches3[3])?->timeline->add(
                new Event(
                    EventType::CHAINS_ADDED,
                    $matches3[3],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::OPPONENT,
                    (int) $matches3[4],
                    ['trigger' => $matches3[2]],
                ),
            );
        }

        $chainData = [
            'Ballcano' => 2,
            "Coward’s End" => 3,
            'Power of Fire' => 1,
            'Arise!' => 1,
            'Grim Reminder' => 1,
            'Gateway to Dis' => 3,
            'Market Crash' => 2,
            'Shell of a Ghost' => 2,
            'Effervescent Principle' => 1,
            'Extinction' => 1,
            'Kaboom!' => 3,
            'Phosphorus Stars' => 2,
            'Crushing Charge' => 1,
            'Catch and Release' => 2,
            'Mælstrom' => 2,
            'Save the Pack' => 1,
            'Quintrino Warp' => 1,
            'Axiom of Grisk' => 2,
            'Krrrzzzaaap!!!' => 1,
            'They Tell No Tales' => 2,
        ];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern4 = "/^($player1|$player2)\s+plays\s+(.+)\s*$/";

        if (preg_match($pattern4, $message, $matches4)) {
            $player = $matches4[1];
            $card = trim($matches4[2]);
            $chains = $chainData[$card] ?? 0;

            if ($chains > 0) {
                $game->player($player)?->timeline->add(
                    new Event(
                        EventType::CHAINS_ADDED,
                        $player,
                        new Turn($game->length, Moment::BETWEEN, $index),
                        Source::PLAYER,
                        $chains,
                        ['trigger' => $card],
                    ),
                );
            }
        }
    }

    private function checkTide(Game $game, int $index, string $message): void
    {
        $matches = [];
        $matches2 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern1 = "/^($player1|$player2)\s+changed tide\s+to High\s*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.*)\s+to raise the tide\s*$/";

        if (preg_match($pattern1, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::TIDE_RAISED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    'manual',
                ),
            );
        }

        if (preg_match($pattern2, $message, $matches2)) {
            $game->player($matches2[1])?->timeline->add(
                new Event(
                    EventType::TIDE_RAISED,
                    $matches2[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $matches2[2],
                ),
            );
        }
    }

    private function checkCardsDiscarded(Game $game, int $index, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $player = null;
        $discardCount = 0;
        $source = Source::PLAYER;
        $matches = [];

        $pattern1 = "/($player1|$player2) uses .*? to discard the top (\d+) cards/i";
        $pattern2 = "/($player1|$player2) uses .*? to discard (.+?)(?:$| and| to| at| from)/i";
        $pattern3 = "/($player1|$player2) discards (.+?)(?:$| and| to| due| at)/i";
        $pattern4 = "/($player1|$player2) uses .*? to discard a card.*?from ($player1|$player2)'s hand/i";

        if (preg_match($pattern1, $message, $matches)) {
            $player = $matches[1];
            $discardCount = (int) $matches[2];
        } elseif (preg_match($pattern2, $message, $matches)) {
            $player = $matches[1];
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern3, $message, $matches)) {
            $player = $matches[1];
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern4, $message, $matches)) {
            $player = $matches[2];
            $discardCount = 1;
            $source = Source::OPPONENT;
        }

        if ($player !== null && $discardCount > 0) {
            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_DISCARDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $discardCount,
                ),
            );
        }
    }

    private function checkCardsPlayed(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+plays\s+(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_PLAYED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    [$card],
                ),
            );
        }
    }

    private function checkAmberStolen(Game $game, int $index, string $message): void
    {
        $matches = [];
        $matches2 = [];
        $matches3 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*from\s*($player1|$player2)\s*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*$/";
        $pattern3 = "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+(.*)\s+and steal an Æmber\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $value = (int) $matches[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern2, $message, $matches2)) {
            $player = $matches2[1];
            $card = trim($matches2[2]);
            $value = (int) $matches2[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern3, $message, $matches3)) {
            $player = $matches3[1];
            $card = trim($matches3[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    1,
                    ['trigger' => $card],
                ),
            );
        }
    }

    private function checkFight(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(.+)fight(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $trigger = trim($matches[2]);
            $value = trim($matches[3]);
            $target = trim($matches[4]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FIGHT,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $trigger, 'target' => $target],
                ),
            );
        }
    }

    private function checkExtraTurn(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+uses\s+(.+)\s+to take another turn after this one\s*(.*)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $trigger = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::EXTRA_TURN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    1,
                    ['trigger' => $trigger],
                ),
            );
        }
    }

    private function checkReap(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to reap with\s+(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $card2 = trim($matches[3]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::REAP,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $card2,
                    ['trigger' => $card],
                ),
            );
        }
    }

    private function checkTokens(Game $game, int $index, string $message): void
    {
        $matches = [];
        $matches2 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make a token creature\s*.*\s*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(\d+)\s+token creatures\s*.*\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::TOKEN_CREATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($pattern2, $message, $matches2)) {
            $player = $matches2[1];
            $card = trim($matches2[2]);
            $amount = (int) $matches2[3];

            for ($i = 0; $i < $amount; ++$i) {
                $game->player($player)?->timeline->add(
                    new Event(
                        EventType::TOKEN_CREATED,
                        $player,
                        new Turn($game->length, Moment::BETWEEN, $index),
                        Source::UNKNOWN,
                        $card,
                    ),
                );
            }
        }
    }

    private function checkProphecies(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)\s*$/";
        $patternActivate = "/^($player1|$player2)\s+activates their prophecy\s+(.+)\s*$/";
        $patternFulfilled = "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy\s*$/";

        if (preg_match($patternFate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FATE_RESOLVED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternActivate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }
    }

    private function checkWinner(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) has won the game\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsWinner(true);
        }
    }

    private function checkConcede(Game $game, int $index, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) concedes\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];

            $game->player($player)?->updateHasConceded(true);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PLAYER_CONCEDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    '',
                ),
            );
        }
    }

    private function checkCheck(Game $game, int $index, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+declares\s+Check!\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CHECK_DECLARED,
                    $player,
                    new Turn($game->length, Moment::END, $index),
                    Source::PLAYER,
                    '',
                ),
            );
        }
    }
}
