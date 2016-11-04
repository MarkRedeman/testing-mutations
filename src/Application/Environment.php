<?php

declare(strict_types=1);

namespace Renamed\Application;

final class Environment
{
    private $inputs = [];
    private $env = [];
    private $config = [];

    public function __construct(array $inputs, array $env, array $config)
    {
        var_dump($env);
        $this->inputs = $inputs;
        $this->env = $env;
        $this->config = $config;
    }

    public function loadExtensions(Extendable $context)
    {
        $extensions = $this->config['extensions'] ?? [];
        foreach ($extensions as $extension) {
            $context->load(new $extension($this));
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
