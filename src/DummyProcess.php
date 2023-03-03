<?php

declare(strict_types = 1);

namespace Sweetchuck\Codeception\Module\RoboTaskRunner;

use Symfony\Component\Process\Process;

class DummyProcess extends Process
{
    public static array $prophecy = [];

    protected static int $counter = 0;

    /**
     * @var static[]
     */
    public static array $instances = [];

    public static function reset(): void
    {
        static::$counter = 0;
        static::$prophecy = [];
        static::$instances = [];
    }

    protected int $index = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $command,
        string $cwd = null,
        array $env = null,
        $input = null,
        ?float $timeout = 60
    ) {
        parent::__construct($command, $cwd, $env, $input, $timeout);

        $this->index = static::$counter++;
        static::$instances[$this->index] = $this;

        if (!array_key_exists($this->index, static::$prophecy)) {
            throw new \LogicException(sprintf(
                'DummyProcess: there is no prepared prophecy for instance %d; command: %s',
                $this->index,
                $this->getCommandLine(),
            ));
        }
    }

    public function __destruct()
    {
        parent::__destruct();

        unset(static::$instances[$this->index]);
        unset(static::$prophecy[$this->index]);
    }

    /**
     * {@inheritdoc}
     */
    public function run(callable $callback = null, array $env = array()): int
    {
        if ($callback) {
            if (static::$prophecy[$this->index]['stdOutput']) {
                $callback(static::OUT, static::$prophecy[$this->index]['stdOutput']);
            }

            if (static::$prophecy[$this->index]['stdError']) {
                $callback(static::ERR, static::$prophecy[$this->index]['stdError']);
            }
        }

        return static::$prophecy[$this->index]['exitCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode(): ?int
    {
        return static::$prophecy[$this->index]['exitCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(): string
    {
        return static::$prophecy[$this->index]['stdOutput'];
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorOutput(): string
    {
        return static::$prophecy[$this->index]['stdError'];
    }
}
