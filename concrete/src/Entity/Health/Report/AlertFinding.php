<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Health\Report\Finding\Formatter\AlertFormatter;
use Concrete\Core\Health\Report\Finding\Formatter\FormatterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="HealthReportResultFinding")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class AlertFinding extends Finding
{

    public function getFormatter(): FormatterInterface
    {
        return new AlertFormatter();
    }


}
