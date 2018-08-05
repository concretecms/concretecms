<?php

namespace Concrete\Core\Foundation\Queue\Batch\Command;

interface BatchableCommandInterface
{

    static function getBatchHandle();

}