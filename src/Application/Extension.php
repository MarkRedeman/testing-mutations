<?php

declare(strict_types=1);

namespace Renamed\Application;

interface Extension
{
    /**
     * @param ServiceContainer $container
     * @param array            $params
     */
    public function load(ServiceContainer $container, array $params);
}
