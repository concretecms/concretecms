<?php
namespace Concrete\Core\Health\Report\Message;

class EmailReportMessage extends AbstractReportMessage
{

    protected $email;

    public function __construct(string $email, string $resultId)
    {
        $this->email = $email;
        parent::__construct($resultId);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }



}