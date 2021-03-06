<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Classes;

use PEAR_Sniffs_Classes_ClassDeclarationSniff;
use PHP_CodeSniffer_File;

/**
 * Rules (new to parent class):
 * - Opening brace for the %s should be followed by %s empty line(s).
 * - Closing brace for the %s should be preceded by %s empty line(s).
 */
final class ClassDeclarationSniff extends PEAR_Sniffs_Classes_ClassDeclarationSniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Classes.ClassDeclaration';

    /**
     * @var int|string
     */
    public $emptyLinesAfterOpeningBrace = 0;

    /**
     * @var int|string
     */
    public $emptyLinesBeforeClosingBrace = 0;

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position) : void
    {
        parent::process($file, $position);
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        // Fix type
        $this->emptyLinesAfterOpeningBrace = (int) $this->emptyLinesAfterOpeningBrace;
        $this->emptyLinesBeforeClosingBrace = (int) $this->emptyLinesBeforeClosingBrace;

        $this->processOpen();
        $this->processClose();
    }

    private function processOpen() : void
    {
        $openingBracePosition = $this->tokens[$this->position]['scope_opener'];
        $emptyLinesCount = $this->getEmptyLinesAfterOpeningBrace($openingBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesAfterOpeningBrace) {
            $errorMessage = sprintf(
                'Opening brace for the %s should be followed by %s empty line(s); %s found.',
                $this->tokens[$this->position]['content'],
                $this->emptyLinesAfterOpeningBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $openingBracePosition);
            if ($fix) {
                $this->fixOpeningBraceSpaces($openingBracePosition, $emptyLinesCount);
            }
        }
    }

    private function processClose() : void
    {
        $closeBracePosition = $this->tokens[$this->position]['scope_closer'];
        $emptyLinesCount = $this->getEmptyLinesBeforeClosingBrace($closeBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesBeforeClosingBrace) {
            $errorMessage = sprintf(
                'Closing brace for the %s should be preceded by %s empty line(s); %s found.',
                $this->tokens[$this->position]['content'],
                $this->emptyLinesBeforeClosingBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $closeBracePosition);
            if ($fix) {
                $this->fixClosingBraceSpaces($closeBracePosition, $emptyLinesCount);
            }
        }
    }

    private function getEmptyLinesBeforeClosingBrace(int $position) : int
    {
        $prevContent = $this->file->findPrevious(T_WHITESPACE, ($position - 1), null, true);
        return $this->tokens[$position]['line'] - $this->tokens[$prevContent]['line'] - 1;
    }

    private function getEmptyLinesAfterOpeningBrace(int $position) : int
    {
        $nextContent = $this->file->findNext(T_WHITESPACE, ($position + 1), null, true);
        return $this->tokens[$nextContent]['line'] - $this->tokens[$position]['line'] - 1;
    }

    private function fixOpeningBraceSpaces(int $position, int $numberOfSpaces) : void
    {
        if ($numberOfSpaces < $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesAfterOpeningBrace; $i++) {
                $this->file->fixer->addContent($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesAfterOpeningBrace; $i--) {
                $this->file->fixer->replaceToken($position + $i, '');
            }
        }
    }

    private function fixClosingBraceSpaces(int $position, int $numberOfSpaces) : void
    {
        if ($numberOfSpaces < $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesBeforeClosingBrace; $i++) {
                $this->file->fixer->addContentBefore($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesBeforeClosingBrace; $i--) {
                $this->file->fixer->replaceToken($position - $i, '');
            }
        }
    }
}
