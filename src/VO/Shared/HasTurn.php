<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

interface HasTurn
{
    public function turn(): Turn;
}
