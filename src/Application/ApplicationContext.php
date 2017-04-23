<?php

declare(strict_types=1);

namespace Renamed\Application;

use Renamed\Mutations;
use Renamed\Application\Events\EventEmitter;

final class ApplicationContext implements Context
{
    private $env;
    private $config;
    private $extensions = [];

    private $emitter = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;

        // get configuration file from

        // $this->config = require($env->configurationFile());

        $this->emitter = new EventEmitter();


        // Setup extensions
        $this->env->loadExtensions($this, function($extension) {
            $this->extensions[] = $extension;
        });
    }

    public function eventEmitter() : EventEmitter
    {
        return $this->emitter;
    }

    /**
     * We will have a couple of operators by default and some others that
     * can be configured by extensions
     */
    public function operators() : array
    {
        $default = [
                // new Mutations\BinaryOperatorReplacement(),
                new Mutations\DateTimeFromFormat(),
                new Mutations\ReturnNull(),
                new Mutations\Arithmetic\Addition(),
                new Mutations\Arithmetic\BitwiseAnd(),
                new Mutations\Arithmetic\BitwiseOr(),
                new Mutations\Arithmetic\BitwiseXor(),
                new Mutations\Arithmetic\DivEqual(),
                new Mutations\Arithmetic\Division(),
                new Mutations\Arithmetic\Exponentiation(),
                new Mutations\Arithmetic\MinusEqual(),
                new Mutations\Arithmetic\ModEqual(),
                new Mutations\Arithmetic\Modulus(),
                new Mutations\Arithmetic\MulEqual(),
                new Mutations\Arithmetic\Multiplication(),
                new Mutations\Arithmetic\Not(),
                new Mutations\Arithmetic\PlusEqual(),
                new Mutations\Arithmetic\PowEqual(),
                new Mutations\Arithmetic\ShiftLeft(),
                new Mutations\Arithmetic\ShiftRight(),
                new Mutations\Arithmetic\Subtraction(),
                new Mutations\Boolean\FalseValue(),
                new Mutations\Boolean\LogicalAnd(),
                new Mutations\Boolean\LogicalLowerAnd(),
                new Mutations\Boolean\LogicalLowerOr(),
                new Mutations\Boolean\LogicalNot(),
                new Mutations\Boolean\LogicalOr(),
                new Mutations\Boolean\TrueValue(),
                new Mutations\ConditionalBoundary\GreaterThan(),
                new Mutations\ConditionalBoundary\GreaterThanOrEqual(),
                new Mutations\ConditionalBoundary\LessThan(),
                new Mutations\ConditionalBoundary\LessThanOrEqual(),
                new Mutations\ConditionalNegation\Equal(),
                new Mutations\ConditionalNegation\GreaterThan(),
                new Mutations\ConditionalNegation\GreaterThanOrEqual(),
                new Mutations\ConditionalNegation\Identical(),
                new Mutations\ConditionalNegation\LessThan(),
                new Mutations\ConditionalNegation\LessThanOrEqual(),
                new Mutations\ConditionalNegation\NotEqual(),
                new Mutations\ConditionalNegation\NotIdentical(),
                new Mutations\Increment\Decrement(),
                new Mutations\Increment\Increment(),
                new Mutations\Number\FloatValue(),
                new Mutations\Number\IntegerValue
        ];

        // Load from environment
        // ...

        // Load additional operators from extensions
        $extended = array_map(function (Context $extension) {
            return $extension->operators();
        }, $this->extensions);

        return array_merge($default, ...$extended);
    }

    public function config() : array
    {
        return $this->env->config();
    }
}
