<?php
namespace Concrete\Core\Health\Report\Command;

use Concrete\Core\Foundation\Command\Command;


class DeleteReportResultCommand extends Command
{

    /**
     * @var string
     */
    protected $resultId;

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

    /**
     * @param string $resultId
     */
    public function setResultId(string $resultId): void
    {
        $this->resultId = $resultId;
    }





}