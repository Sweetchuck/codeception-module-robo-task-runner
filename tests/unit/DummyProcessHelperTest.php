<?php

declare(strict_types = 1);

namespace Sweetchuck\Codeception\Module\RoboTaskRunner\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcessHelper;

/**
 * @covers \Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess
 */
class DummyProcessHelperTest extends Unit
{

    public function testRun(): void
    {
        $outputConfig = [];
        $output = new DummyOutput($outputConfig);

        DummyProcess::$prophecy[] = [
            'stdOutput' => '',
            'stdError' => '',
            'exitCode' => 0,
        ];
        $processHelper = new DummyProcessHelper();
        $process = $processHelper->run(
            $output,
            ['true'],
        );
        static::assertInstanceOf(DummyProcess::class, $process);
    }
}
