<?php

namespace Concrete\Core\Entity\Health\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="HealthReportResultFinding")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class InfoFinding extends Finding
{





}
