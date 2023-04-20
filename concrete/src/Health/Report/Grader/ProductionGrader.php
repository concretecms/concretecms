<?php
namespace Concrete\Core\Health\Report\Grader;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Health\Grade\GradeInterface;
use Concrete\Core\Health\Grade\PassFailGrade;

/**
 * A more severe version of the StandardGrader - all the behavior is the same
 * but warnings count for 10 and any alerts at all result in a zero-failure.
 */
class ProductionGrader implements ScoringGraderInterface
{

    public function getScoreFromResult(Result $result): int
    {
        $findings = $result->getFindings();
        $score = 100;
        $bonus = 0;
        foreach ($findings as $finding) {

            if ($finding instanceof AlertFinding) {
                $score = 0;
                break;
            }

            if ($finding instanceof WarningFinding) {
                $score -= 10;
            }

            if ($finding instanceof SuccessFinding) {
                $bonus += 10;
            }
        }

        if ($score == 100) {
            $score += $bonus;
        }

        return $score;
    }

    public function getGrade(int $score = null): GradeInterface
    {
        return new PassFailGrade($score);
    }

}
