<?php

namespace Renamed\Utility;

use Renamed\Utility\Clock;
use DateTimeImmutable;

class Performance
{
    private $clock;
    private $startTime;
    private $endTime;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function start()
    {
        $this->startTime = $this->clock->time();
        $this->endTime = null;
    }

    public function stop()
    {
        $this->endTime = $this->clock->time();
    }

    private function time() : string
    {
        return $this->toMicroseconds($this->endTime) - $this->toMicroseconds($this->startTime);
    }

    public function formatTime() : string
    {
        $horizons = [
            'hour'   => 3600000,
            'minute' => 60000,
            'second' => 1000,
        ];
        $milliseconds = round($this->time());

        foreach ($horizons as $unit => $value) {
            if ($milliseconds >= $value) {
                $time = floor($milliseconds / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }
        return $milliseconds . ' milliseconds';
    }

    // Mb
    private function memoryUsage() : float
    {
        return (memory_get_peak_usage(true) / (1024 * 1024));
    }

    public function formatMemoryUsage() : string
    {
        return sprintf('%4.2fMB', $this->memoryUsage());
    }

    private function toMicroseconds(DateTimeImmutable $time) : string
    {
        return ($time->format('U') * 1000) + ($time->format('u') / 1000);
    }
}
