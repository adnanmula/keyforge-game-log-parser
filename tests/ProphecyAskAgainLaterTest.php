<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use PHPUnit\Framework\TestCase;

final class ProphecyAskAgainLaterTest extends TestCase
{
    use GetTestData;

    public function testAskAgainLater1(): void
    {
        $game = $this->getLog('prophecy_ask_again_later');

        $summary = $game->timeline()->propheciesSummary();

        self::assertArrayHasKey('prophecies', $summary);
        self::assertArrayHasKey('fates', $summary);

        self::assertCount(1, $summary['prophecies']);
        self::assertCount(10, $summary['fates']);

        self::assertEquals(13, $summary['prophecies']['Ask Again Later']['activated']);
        self::assertEquals(13, $summary['prophecies']['Ask Again Later']['fulfilled']);
        self::assertEquals(100, $summary['prophecies']['Ask Again Later']['percent']);
    }
}
