<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorFates implements LogProcessor
{
    private const array FATE_CARDS = [
        'Grave Bounty',
        'Heed the Horde',
        'Evasive Maneuvers',
        'Driving Courage',
        'Paleogene Society',
        'Tormented Badge',
        'Genetic Strain',
        'Embellish Imp',
        'Ruthless Avenger',
        'Thunk',
        'Publius Scipio',
        'Sedimentary Nap',
        'Brutal Consequences',
        'Mogg',
        'Reiteration',
        'Prince Bufo',
        'Lambent Mycelium',
        'Gleaming the Cube',
        'Relegated Relics',
        'Brass Klein',
        'Chonk Evermore',
        'Knockback',
        'Sir Jaune',
        'Sleight',
        'Gone Pear Shaped',
        'Kasheek Fall',
        'Parasitic Arachnoid',
        'Divine Seal',
        'Agamignus',
        'Coup de Grâce',
        'Bondsman Belvan',
        'Too Low',
        'Gracchan Reform',
        'Corrosive Monk',
        'Professor Scruples',
        'Unstable Dale',
        'Oh, You Shouldn’t Have!',
        'Bit Byte',
        'Ensign Clark',
        'Tealnar',
        'DAL-33-T3R',
        'Reassembly Required',
        'Oblivion Knight',
        'Benevolent Charity',
        'Spendthrift',
        'Envious Venomite',
        'Charitable Herald',
        'Neotechnic Gopher',
        'Plancina, Hidden Agent',
        'Crystalline Harvest',
        'Eddy of Dis',
        'Chummy',
        'Chasm Vespid',
        'Strug',
        'Dissonant Chord',
        'Windrow Composting',
        'Greedy Reprisal',
        'Solo',
        'Refit',
        'Poised Strike',
        'Predatory Lending',
        'Strategic Feint',
        'Sample 42-C',
        'Salvatorem',
        'Drosera Relic',
        'Exotic Pivot',
        'Rage Reset',
        'Fallen Sovereign',
        'Omicron Callen',
        'Cosmic Recompense',
    ];
    
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)$/";

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
                    ['has_fate' => in_array($card, self::FATE_CARDS, true)],
                ),
            );
        }

        return $game;
    }
}
