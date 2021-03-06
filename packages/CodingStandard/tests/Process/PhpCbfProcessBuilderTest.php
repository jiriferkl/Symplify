<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Process;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Process\PhpCbfProcessBuilder;

final class PhpCbfProcessBuilderTest extends TestCase
{
    public function test()
    {
        $builder = new PhpCbfProcessBuilder('directory');
        $this->assertSame(
            "'./vendor/bin/phpcbf' 'directory'",
            $builder->getProcess()->getCommandLine()
        );

        $builder->setExtensions('php5');
        $this->assertSame(
            "'./vendor/bin/phpcbf' 'directory' '--extensions=php5'",
            $builder->getProcess()->getCommandLine()
        );

        $builder->setStandard('standard');
        $this->assertSame(
            "'./vendor/bin/phpcbf' 'directory' '--extensions=php5' '--standard=standard'",
            $builder->getProcess()->getCommandLine()
        );
    }
}
