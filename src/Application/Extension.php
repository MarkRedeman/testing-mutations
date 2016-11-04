<?php

<?php

declare(strict_types=1);

namespace WhiteWolf;

interface Extension
{
    /**
     * @param ServiceContainer $container
     * @param array            $params
     */
    public function load(ServiceContainer $container, array $params);
}
