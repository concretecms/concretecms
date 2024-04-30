<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class ValidateResult
{

    const VALIDATE_RESULT_SUCCESS = 0;
    const VALIDATE_RESULT_ERROR = 460;
    CONST VALIDATE_RESULT_ERROR_URL_MISMATCH = 461;

    /** @var bool */
    public $valid;
    /** @var string */
    public $site;
    /** @var string */
    public $error;
    /** @var int */
    public $code;

    public function __construct(bool $valid, string $site, string $error = '', $code = 0)
    {
        $this->valid = $valid;
        $this->site = $site;
        $this->error = $error;
        $this->code = $code;
    }
}
