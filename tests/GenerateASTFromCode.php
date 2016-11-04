<?php

declare(strict_types=1);

namespace Renamed\tests;

use PhpParser\Lexer;
use PhpParser\ParserFactory;

trait GenerateASTFromCode
{
    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }
}
