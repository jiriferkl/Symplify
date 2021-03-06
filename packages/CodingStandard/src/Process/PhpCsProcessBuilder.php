<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symplify\CodingStandard\Contract\Process\ProcessBuilderInterface;

final class PhpCsProcessBuilder implements ProcessBuilderInterface
{
    /**
     * @var ProcessBuilder
     */
    private $builder;

    public function __construct(string $directory)
    {
        $this->builder = new ProcessBuilder;
        $this->builder->setPrefix('./vendor/bin/phpcs');
        $this->builder->add($directory);
        $this->builder->add('--colors');
        $this->builder->add('-p');
        $this->builder->add('-s');
    }

    public function getProcess() : Process
    {
        return $this->builder->getProcess();
    }

    public function setStandard(string $standard) : void
    {
        $this->builder->add('--standard=' . $standard);
    }

    public function setExtensions(string $extensions) : void
    {
        $this->builder->add('--extensions=' . $extensions);
    }
}
