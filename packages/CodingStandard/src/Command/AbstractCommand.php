<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Command;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\CodingStandard\Contract\Runner\RunnerCollectionInterface;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var int
     */
    protected const EXIT_CODE_SUCCESS = 0;

    /**
     * @var int
     */
    protected const EXIT_CODE_ERROR = 1;

    /**
     * @var int
     */
    protected $exitCode = self::EXIT_CODE_SUCCESS;

    /**
     * @var RunnerCollectionInterface
     */
    protected $runnerCollection;

    /**
     * @var StyleInterface
     */
    protected $io;

    public function __construct(RunnerCollectionInterface $runnerCollection)
    {
        $this->runnerCollection = $runnerCollection;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->addArgument('path', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s)', null);
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function getRunnerName(RunnerInterface $runner) : string
    {
        return (new ReflectionClass($runner))->getShortName();
    }
}
