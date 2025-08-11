<?php
namespace Concrete\Core\Health\Report\Grader;

use Concrete\Core\Health\Grade\GradeInterface;

interface GraderInterface
{

    public function getGrade(): GradeInterface;

}
