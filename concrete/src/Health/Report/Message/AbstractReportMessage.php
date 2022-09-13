<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Foundation\Command\Command;

abstract class AbstractReportMessage extends Command
{

    /**
     * @var string
     */
    protected $resultId;

    /**
     * FinishReportMessage constructor.
     * @param string $resultId
     */
    public function __construct(string $resultId)
    {
        $this->resultId = $resultId;
    }

    /**
     * @return string
     */
    public function getResultId(): string
    {
        return $this->resultId;
    }



}