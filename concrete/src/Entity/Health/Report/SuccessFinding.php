<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Health\Report\Finding\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Formatter\SuccessFormatter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="HealthReportResultFinding")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class SuccessFinding extends Finding
{

    public function getFormatter(): FormatterInterface
    {
        return new SuccessFormatter();
    }


}
