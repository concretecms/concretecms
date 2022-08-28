<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Grader\ScoringGraderInterface;
use Concrete\Core\Health\Report\ReportControllerInterface;
use Doctrine\ORM\EntityManager;

class GradeReportMessageHandler
{


    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(GradeReportMessage $message)
    {
        // We need this line in case this is being run by the tasks CLI - the result findings aren't
        // included without this.
        $this->entityManager->clear();

        /**
         * @var $result Result
         */
        $result = $this->entityManager->find(Result::class, $message->getResultId());
        $task = $result->getTask();

        /**
         * @var $controller ReportControllerInterface
         */
        $controller = $task->getController();
        $grader = $controller->getResultGrader();
        if ($grader) {
            if ($grader instanceof ScoringGraderInterface) {
                $score = $grader->getScoreFromResult($result);
                if ($score < 0) {
                    $score = 0;
                }
                $result->setScore($score);
                $grade = $grader->getGrade($score);
            } else {
                $grade = $grader->getGrade();
            }
            $result->setGrade($grade);
        }

        $this->entityManager->persist($result);
        $this->entityManager->flush();
    }

}