<?php

namespace Sweetchuck\Codeception\Module\RoboTaskRunner;

use Symfony\Component\Process\Process;

class DummyProcess extends Process
{
    /**
     * @var array
     */
    public static $prophecy = [];

    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * @var static[]
     */
    public static $instances = [];

    public static function reset(): void
    {
        static::$counter = 0;
        static::$prophecy = [];
        static::$instances = [];
    }

    /**
     * @var int
     */
    protected $index = 0;

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
    public function getExitCode()
    {
        return static::$prophecy[$this->index]['exitCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return static::$prophecy[$this->index]['stdOutput'];
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorOutput()
    {
        return static::$prophecy[$this->index]['stdError'];
    }
}
