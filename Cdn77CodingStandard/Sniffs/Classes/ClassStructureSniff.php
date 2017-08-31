<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * This sniff ensures that the class/interface/trait has consistent order of its members in this exact order:
 *  - use statements
 *  - constants
 *  - properties
 *  - constructor
 *  - destructor
 *  - methods
 *  - magic methods
 */
class ClassStructureSniff implements Sniff
{
    public const CODE_INVALID_MEMBER_PLACEMENT = 'InvalidMemberOrder';

    private const STAGE_NONE = 0b0;
    private const STAGE_USES = 0b1;
    private const STAGE_CONSTANTS = 0b10;
    private const STAGE_PROPERTIES = 0b100;
    private const STAGE_CONSTRUCTOR = 0b1000;
    private const STAGE_DESTRUCTOR = 0b10000;
    private const STAGE_METHODS = 0b100000;
    private const STAGE_MAGIC_METHODS = 0b1000000;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return TokenHelper::$typeKeywordTokenCodes;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(File $file, $pointer) : void
    {
        $tokens = $file->getTokens();
        $rootScopeToken = $tokens[$pointer];
        $rootScopeOpenerPointer = $rootScopeToken['scope_opener'];
        $rootScopeCloserPointer = $rootScopeToken['scope_closer'];

        $currentTokenPointer = $rootScopeOpenerPointer + 1;
        $activeStage = self::STAGE_NONE;

        do {
            $currentTokenPointer = $this->findNextElement($file, $currentTokenPointer, $rootScopeCloserPointer);

            if ($currentTokenPointer === false) {
                return;
            }

            $currentToken = $tokens[$currentTokenPointer];

            if ($currentToken['level'] - $rootScopeToken['level'] !== 1) {
                continue;
            }

            $currentTokenStage = $this->getStageForToken($file, $currentTokenPointer);

            if ($currentTokenStage !== null) {
                // stage violation
                if ($currentTokenStage < $activeStage) {
                    $file->addError(
                        sprintf(
                            'The placement of %s is invalid.',
                            $this->getTokenCodeName($file, $currentTokenPointer)
                        ),
                        $currentTokenPointer,
                        self::CODE_INVALID_MEMBER_PLACEMENT
                    );
                }

                // stage transition
                if ($currentTokenStage > $activeStage) {
                    $activeStage = $currentTokenStage;
                }
            }

            // advance pointer
            $currentTokenPointer = $currentToken['scope_closer'] ?? $currentTokenPointer + 1;
        } while ($currentTokenPointer !== false);
    }

    /**
     * @return int|bool
     */
    private function findNextElement(File $file, int $pointer, int $boundary)
    {
        return $file->findNext(
            [T_USE, T_CONST, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FUNCTION],
            $pointer,
            $boundary
        );
    }

    private function getStageForToken(File $file, int $pointer) : ?int
    {
        $tokens = $file->getTokens();

        switch ($tokens[$pointer]['code']) {
            case T_USE:
                return self::STAGE_USES;
            case T_CONST:
                return self::STAGE_CONSTANTS;
            case T_FUNCTION:
                $name = strtolower($tokens[$file->findNext(T_STRING, $pointer + 1)]['content']);
                return [
                    '__construct' => self::STAGE_CONSTRUCTOR,
                    '__destruct' => self::STAGE_DESTRUCTOR,
                    '__get' => self::STAGE_MAGIC_METHODS,
                    '__set' => self::STAGE_MAGIC_METHODS,
                    '__isset' => self::STAGE_MAGIC_METHODS,
                    '__unset' => self::STAGE_MAGIC_METHODS,
                    '__call' => self::STAGE_MAGIC_METHODS,
                    '__callStatic' => self::STAGE_MAGIC_METHODS,
                    '__sleep' => self::STAGE_MAGIC_METHODS,
                    '__wakeup' => self::STAGE_MAGIC_METHODS,
                ][$name] ?? self::STAGE_METHODS;
            case T_PUBLIC:
            case T_PROTECTED:
            case T_PRIVATE:
            case T_VAR:
                $nextToken = $file->findNext(Tokens::$emptyTokens, $pointer + 1, null, true);
                if ($tokens[$nextToken]['code'] === T_VARIABLE) {
                    return self::STAGE_PROPERTIES;
                }
                return null;
            default:
                return null;
        }
    }

    private function getTokenCodeName(File $file, int $pointer) : string
    {
        return [
            self::STAGE_USES => 'use',
            self::STAGE_CONSTANTS => 'constant',
            self::STAGE_PROPERTIES => 'property',
            self::STAGE_CONSTRUCTOR => 'constructor',
            self::STAGE_DESTRUCTOR => 'destructor',
            self::STAGE_METHODS => 'method',
            self::STAGE_MAGIC_METHODS => 'magic method',

        ][$this->getStageForToken($file, $pointer)];
    }
}
