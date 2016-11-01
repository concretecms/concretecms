<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Job\Job;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportJobsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'jobs';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->jobs)) {
            foreach ($sx->jobs->job as $jx) {
                $pkg = static::getPackageObject($jx['package']);
                $job = Job::getByHandle($jx['handle']);
                if (!is_object($job)) {
                    if (is_object($pkg)) {
                        Job::installByPackage($jx['handle'], $pkg);
                    } else {
                        Job::installByHandle($jx['handle']);
                    }
                }
            }
        }

    }

}
