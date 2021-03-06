<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:
 * - Non-abstract class that implements interface should be final.
 * - Except for Doctrine entities, they cannot be final.
 *
 * Inspiration:
 * - http://ocramius.github.io/blog/when-to-declare-classes-final/
 */
final class FinalInterfaceSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Classes.FinalInterface';

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLASS];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->implementsInterface() === false) {
            return;
        }

        if ($this->isFinalOrAbstractClass()) {
            return;
        }

        if ($this->isDoctrineEntity()) {
            return;
        }

        $fix = $file->addFixableError('Non-abstract class that implements interface should be final.', $position);

        if ($fix) {
            $this->addFinalToClass($position);
        }
    }

    private function implementsInterface() : bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position);
    }

    private function isFinalOrAbstractClass() : bool
    {
        $classProperties = $this->file->getClassProperties($this->position);
        return ($classProperties['is_abstract'] || $classProperties['is_final']);
    }

    private function isDoctrineEntity() : bool
    {
        $docCommentPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position);
        if ($docCommentPosition === false) {
            return false;
        }

        $seekPosition = $docCommentPosition;

        do {
            $docCommentTokenContent = $this->file->getTokens()[$docCommentPosition]['content'];
            if (strpos($docCommentTokenContent, 'Entity') !== false) {
                return true;
            }
            $seekPosition++;
        } while ($docCommentPosition = $this->file->findNext(T_DOC_COMMENT_TAG, $seekPosition, $this->position));

        return false;
    }

    public function addFinalToClass(int $position) : void
    {
        $this->file->fixer->addContentBefore($position, 'final ');
    }
}
