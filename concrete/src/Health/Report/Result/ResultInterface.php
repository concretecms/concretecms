<?php

namespace Concrete\Core\Health\Report\Result;

use Concrete\Core\Health\Report\Result\Formatter\FormatterInterface;

interface ResultInterface
{

    public function getFormatter(): FormatterInterface;


}