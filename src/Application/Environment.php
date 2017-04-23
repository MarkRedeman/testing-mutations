<?php

declare(strict_types=1);

namespace Renamed\Application;

use Closure;

final class Environment
{
    private $inputs = [];
    private $env = [];
    private $config = [];

    public function __construct(array $inputs, array $env, array $config)
    {
        $this->inputs = $inputs;
        $this->env = $env;

        // TODO only accept FQCN in the extensions option
        $this->config = $config;
    }

    public function loadExtensions(Context $context, Closure $loaded)
    {
        // The extensiosn configuration contains a list of FQCN which we instantiate
        // with a reference to the current context
        $extensions = $this->config['extensions'] ?? [];

        foreach ($extensions as $extension) {
            // TODO we bluntly assume that the extension has a conforming constructor
            $loaded(new $extension($context));
        }
    }

    public function config() : array
    {
        return array_merge($this->env, $this->config);
    }

    private function projectPath()
    {
        // find project path based on composer?
    }
}
