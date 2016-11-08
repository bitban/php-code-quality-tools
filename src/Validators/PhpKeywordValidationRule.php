<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;

class PhpKeywordValidationRule
{
    /** @var int */
    protected $type;
    /** @var string[]  */
    protected $keywords;
    /** @var string */
    protected $message;
    /** @var int */
    protected $severity;

    /**
     * @param int $type
     * @param string[] $keywords
     * @param string $message
     * @param int $severity
     */
    public function __construct($type, array $keywords, $message, $severity)
    {
        $this->type = $type;
        $this->keywords = $keywords;
        $this->message = $message;
        $this->severity = $severity;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param mixed[] $phpToken
     * @return bool
     */
    public function matchToken($phpToken)
    {
        return $phpToken[0] === $this->type
            && in_array($phpToken[1], $this->keywords);
    }

    /**
     * @param string $file
     * @param mixed[] $phpToken
     * @throws ErrorException
     * @throws WarningException
     */
    public function validateToken($file, $phpToken)
    {
        if ($this->matchToken($phpToken)) {
            $message = $file . ': ' . $this->message . "\n";
            if ($this->severity === Constants::RETURN_CODE_WARNING) {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $message));
            } else {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $message));
            }
            throw $exception;
        }
    }
}
