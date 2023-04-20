<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Finding\CsvWriter;
use Concrete\Core\Mail\Service;
use Doctrine\ORM\EntityManager;

class EmailReportMessageHandler
{

    /**
     * @var Service
     */
    protected $mailService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var WriterFactory
     */
    protected $writerFactory;

    /**
     * @var CsvWriter
     */
    protected $csvWriter;

    public function __construct(WriterFactory $writerFactory, CsvWriter $csvWriter, EntityManager $entityManager, Service $mailService)
    {
        $this->writerFactory = $writerFactory;
        $this->entityManager = $entityManager;
        $this->csvWriter = $csvWriter;
        $this->mailService = $mailService;
    }

    public function __invoke(EmailReportMessage $message)
    {
        // We need this line in case this is being run by the tasks CLI - the result findings aren't
        // included without this.
        $this->entityManager->clear();


        /**
         * @var $result Result
         */
        $result = $this->entityManager->find(Result::class, $message->getResultId());
        if ($result && $message->getEmail()) {
            $writer = $this->writerFactory->createFromStream(tmpfile());
            $writer = $this->csvWriter->populateWriter($writer, $result);

            $this->mailService->to($message->getEmail());
            $this->mailService->addParameter('result', $result);
            $this->mailService->addParameter('reportName', $result->getTask()->getController()->getName());
            $this->mailService->load('report_result_ready');

            if ($writer) {
                $csvContent = $writer->toString();
                $this->mailService->addRawAttachment($csvContent, $this->csvWriter->getFilenameForResult($result), 'text/csv');
            }

            $this->mailService->sendMail();
        }
    }

}