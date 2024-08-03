<?php

declare(strict_types = 1);

namespace Sweetchuck\Codeception\Module\RoboTaskRunner\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;

/**
 * @covers \Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput
 */
class DummyOutputTest extends Unit
{
    public function testWrite(): void
    {
        $config = [];
        $output = new DummyOutput($config);
        $output->write('my message 01');
        static::assertSame(
            'my message 01',
            $output->output,
        );
    }
}
