<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use Symplify\CodingStandard\Helper\Commenting\FunctionHelper;

/**
 * Rules:
 * - CreateComponent* method should have a doc comment.
 * - CreateComponent* method should have a return tag.
 * - Return tag should contain type.
 */
final class ComponentFactoryCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Commenting.ComponentFactoryComment';

    /**
     * @var int
     */
    private $position;

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var array
     */
    private $tokens;

    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        if (! $this->isComponentFactoryMethod()) {
            return;
        }

        $returnTypeHint = FunctionHelper::findReturnTypeHint($file, $position);
        if ($returnTypeHint) {
            return;
        }

        $commentEnd = $this->getCommentEnd();
        if (! $this->hasMethodComment($commentEnd)) {
            $file->addError('createComponent<name> method should have a doc comment or return type.', $position);
            return;
        }

        $commentStart = $this->tokens[$commentEnd]['comment_opener'];
        $this->processReturnTag($commentStart);
    }

    private function isComponentFactoryMethod() : bool
    {
        $functionName = $this->file->getDeclarationName($this->position);
        return (strpos($functionName, 'createComponent') === 0);
    }

    /**
     * @return bool|int
     */
    private function getCommentEnd()
    {
        return $this->file->findPrevious(T_WHITESPACE, ($this->position - 3), null, true);
    }

    private function hasMethodComment(int $position) : bool
    {
        if ($this->tokens[$position]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            return true;
        }
        return false;
    }

    private function processReturnTag(int $commentStartPosition) : void
    {
        $return = null;
        foreach ($this->tokens[$commentStartPosition]['comment_tags'] as $tag) {
            if ($this->tokens[$tag]['content'] === '@return') {
                $return = $tag;
            }
        }
        if ($return !== null) {
            $content = $this->tokens[($return + 2)]['content'];
            if (empty($content) === true || $this->tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Return tag should contain type';
                $this->file->addError($error, $return);
            }
        } else {
            $error = 'CreateComponent* method should have a @return tag';
            $this->file->addError($error, $this->tokens[$commentStartPosition]['comment_closer']);
        }
    }
}
