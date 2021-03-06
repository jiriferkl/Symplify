<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:
 * - Class name after new/instanceof should not start with slash.
 */
final class ClassNamesWithoutPreSlashSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Namespaces.ClassNamesWithoutPreSlash';

    /**
     * @var string[]
     */
    private $excludedClassNames = [
        'DateTime', 'stdClass', 'splFileInfo', 'Exception',
    ];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NEW, T_INSTANCEOF];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position) : void
    {
        $tokens = $file->getTokens();
        $classNameStart = $tokens[$position + 2]['content'];

        if ($classNameStart === '\\') {
            if ($this->isExcludedClassName($tokens[$position + 3]['content'])) {
                return;
            }
            $file->addError('Class name after new/instanceof should not start with slash.', $position);
        }
    }

    private function isExcludedClassName(string $className) : bool
    {
        if (in_array($className, $this->excludedClassNames)) {
            return true;
        }

        return false;
    }
}
