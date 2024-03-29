<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\DateTimeFromFormat;
use Renamed\MutationOperator;

/**
 * This mutation is used by mutant, the ruby mutation testing tool.
 * A nice article explaining the usefulness of this mutation can be found here,
 * https://blog.blockscore.com/how-to-write-better-code-using-mutation-testing/
 */
class DateTimeFromFormatTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new DateTimeFromFormat();
    }

    /** @test */
    public function it_mutates_creation_of_date_time_objects_to_a_more_explicit_form()
    {
        // Should work for variables
        $this->mutates('new DateTime($date);')->to('DateTime::createFromFormat(DateTime::ISO8601, $date);');

        // Check if it works for yyyy-mm-dd
        $this->mutates('new DateTime("2016-07-26");')
            ->to('DateTime::createFromFormat(DateTime::ISO8601, "2016-07-26");');

        // Check if it works for dd-mm-yyyy
        $this->mutates('new DateTime("26-07-2016");')
            ->to('DateTime::createFromFormat(DateTime::ISO8601, "26-07-2016");');
    }

    /** @test */
    public function it_keeps_the_date_time_zone_when_mutating()
    {
        // Should work for variables
        $this->mutates('new DateTime($date, $timezone);')
            ->to('DateTime::createFromFormat(DateTime::ISO8601, $date, $timezone);');
    }

    /** @test */
    public function it_only_mutates_new_datetime_statements()
    {
        $this->doesNotMutate('new StdClass;');
        $this->doesNotMutate('$hello = "world";');
    }

    /** @test */
    public function it_does_not_mutate_expressions()
    {
        $this->doesNotMutate('new func();');
    }

    /** @test */
    public function it_does_not_mutate_instantiations_of_anonymous_classes()
    {
        $this->doesNotMutate('new class {};');
    }
}
