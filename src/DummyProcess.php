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
        $commandline,
        $cwd = null,
        array $env = null,
        $input = null,
        $timeout = 60,
        array $options = null
    ) {
        parent::__construct($commandline, $cwd, $env, $input, $timeout, $options);

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
    public function run($callback = null)
    {
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
