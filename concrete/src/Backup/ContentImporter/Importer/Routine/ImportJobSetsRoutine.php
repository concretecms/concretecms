<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\Set;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportJobSetsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'job_sets';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->jobsets)) {
            foreach ($sx->jobsets->jobset as $js) {
                $jso = Set::getByName((string) $js['name']);
                if (!is_object($jso)) {
                    $pkg = static::getPackageObject($js['package']);
                    if (is_object($pkg)) {
                        $jso = Set::add((string) $js['name'], $pkg);
                    } else {
                        $jso = Set::add((string) $js['name']);
                    }
                }
                foreach ($js->children() as $jsk) {
                    $j = Job::getByHandle((string) $jsk['handle']);
                    if (is_object($j)) {
                        $jso->addJob($j);
                    }
                }
            }
        }

    }

}
