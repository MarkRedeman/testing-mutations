<?php

declare(strict_types=1);

namespace Renamed\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Renamed\Utility\Performance;
use DateTimeImmutable;
use Renamed\Utility\Clock;

class PerformanceTest extends TestCase
{
    /**
     * @dataProvider timeProvider
     * @test
     */
    function it_keeps_track_of_and_formats_time(string $start, string $end, string $formatted)
    {
        $clock = $this->prophesize(Clock::class);
        $clock->time()->willReturn(
            DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:s.u',
                $start
            ),
            DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:s.u',
                $end
            )
        );

        $performance = new Performance($clock->reveal());
        $performance->start();
        $performance->stop();
        $this->assertEquals(
            $formatted,
            $performance->formatTime()
        );
    }

    /** @test */
    function it_formats_the_memory_usage_into_mega_bytes()
    {
        $clock = $this->prophesize(Clock::class);
        $performance = new Performance($clock->reveal());

        // I haven't yet found a way to correctly test the memory usage
        $format = $performance->formatMemoryUsage();
        $this->assertStringMatchesFormat("%fMB", $format);
    }

    function timeProvider()
    {
        return [
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:57:32.000100', '0 milliseconds'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:57:32.111000', '111 milliseconds'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:57:33.000000', '1 second'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:57:35.000000', '3 seconds'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:57:45.000000', '13 seconds'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T11:58:32.000000', '1 minute'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T12:00:32.000000', '3 minutes'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T12:57:32.000000', '1 hour'],
            ['2016-11-06T11:57:32.000000', '2016-11-06T15:57:32.000000', '4 hours'],
        ];
    }
}
