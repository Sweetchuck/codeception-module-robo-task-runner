<?php

namespace Sweetchuck\Codeception\Module\RoboTaskRunner;

use Codeception\Module as CodeceptionModule;
use Robo\Robo;
use Robo\Runner;
use Symfony\Component\Console\Output\OutputInterface;

class RoboTaskRunner extends CodeceptionModule
{

    /**
     * @var DummyOutput[]
     */
    protected $roboTaskStdOutput = [];

    /**
     * @var int[]
     */
    protected $roboTaskExitCode = [];

    public function getRoboTaskExitCode(string $id): int
    {
        return $this->roboTaskExitCode[$id];
    }

    public function getRoboTaskStdOutput(string $id): string
    {
        return $this->roboTaskStdOutput[$id]->output;
    }

    public function getRoboTaskStdError(string $id): string
    {
        /** @var \Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput $errorOutput */
        $errorOutput = $this->roboTaskStdOutput[$id]->getErrorOutput();

        return $errorOutput->output;
    }

    public function runRoboTask(
        string $id,
        string $class,
        string ...$args
    ): void {
        if (isset($this->roboTaskStdOutput[$id])) {
            throw new \InvalidArgumentException();
        }

        $config = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $this->roboTaskStdOutput[$id] = new DummyOutput($config);

        array_unshift($args, 'RoboTaskRunner.php', '--no-ansi');

        $containerBackup = Robo::hasContainer() ? Robo::getContainer() : null;
        if ($containerBackup) {
            Robo::unsetContainer();
        }

        $container = Robo::createDefaultContainer(null, $this->roboTaskStdOutput[$id]);
        $container->add('output', $this->roboTaskStdOutput[$id], false);
        Robo::setContainer($container);

        $this->roboTaskExitCode[$id] = (new Runner($class))
            ->setContainer($container)
            ->execute($args);

        if ($containerBackup) {
            Robo::setContainer($containerBackup);
        } else {
            Robo::unsetContainer();
        }
    }
}
