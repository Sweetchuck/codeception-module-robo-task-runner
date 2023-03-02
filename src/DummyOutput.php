<?php

declare(strict_types = 1);

namespace Sweetchuck\Codeception\Module\RoboTaskRunner;

use Codeception\Lib\Console\Output as ConsoleOutput;

class DummyOutput extends ConsoleOutput
{

    protected static int $instanceCounter = 0;

    public string $output = '';

    public int $instanceId = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->instanceId = static::$instanceCounter++;

        if (empty($config['stdErr'])) {
            $config['stdErr'] = true;
            $this->setErrorOutput(new static($config));
        } else {
            $this->setErrorOutput($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite(string $message, bool $newline)
    {
        $this->output .= $message . ($newline ? "\n" : '');
    }
}
