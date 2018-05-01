<?php

declare(strict_types = 1);

namespace Sweetchuck\Codeception\Module\RoboTaskRunner\Tests\Unit;

use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;

class DummyProcessTest extends \Codeception\Test\Unit
{
    public function casesRun(): array
    {
        return [
            'one' => [
                [
                    'exitCode' => 42,
                    'stdOutput' => 'my-std-output-one',
                    'stdError' => 'my-std-error-one',
                ],
            ],
            'two' => [
                [
                    'exitCode' => 84,
                    'stdOutput' => 'my-std-output-two',
                    'stdError' => 'my-std-error-two',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesRun
     */
    public function testRun(array $expected): void
    {
        DummyProcess::$prophecy[] = $expected;
        $dummyProcess = new DummyProcess('false');
        $actualExitCode = $dummyProcess->run();
        $actualStdOutput = $dummyProcess->getOutput();
        $actualStdError = $dummyProcess->getErrorOutput();

        if (array_key_exists('exitCode', $expected)) {
            $this->assertSame($expected['exitCode'], $actualExitCode);
            $this->assertSame($expected['exitCode'], $dummyProcess->getExitCode());
        }

        if (array_key_exists('stdOutput', $expected)) {
            $this->assertSame($expected['stdOutput'], $actualStdOutput);
        }

        if (array_key_exists('stdError', $expected)) {
            $this->assertSame($expected['stdError'], $actualStdError);
        }

        DummyProcess::reset();
        $this->assertSame([], DummyProcess::$prophecy);
        $this->assertSame([], DummyProcess::$instances);
    }
}
