<?php
namespace Concrete\Core\Health\Report\Grader;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Grade\GradeInterface;

interface ScoringGraderInterface extends GraderInterface
{

    public function getScoreFromResult(Result $result): int;

    /**
     * Note: I'm actually kind of shocked I'm allowed to do this (override the parent methods with a semi-different
     * signature. Let's keep an eye on whether this continues to work.)
     *
     * @param int|null $score
     * @return GradeInterface
     */
    public function getGrade(int $score = null): GradeInterface;

}
