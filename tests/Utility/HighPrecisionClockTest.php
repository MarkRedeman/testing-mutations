<?php

declare(strict_types=1);

namespace Renamed\Tests\Utility;

use PHPUnit_Framework_TestCase as TestCase;
use Renamed\Utility\HighPrecisionClock;

class HighPrecisionClockTest extends TestCase
{
    /** @test */
    function it_gives_a_time_in_microseconds()
    {
        $time = new HighPrecisionClock();
        $past = $time->time();

        // Wait a while so that our next clock will be different
        usleep(10);

        $this->assertNotEquals($past, $time->time(), 'The clock should have progessed a few milliseconds');
    }

}
