<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\File\File;

class ReindexFileTaskCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var FileCategory
     */
    protected $attributeCategory;

    /**
     * @param FileCategory $attributeCategory
     */
    public function __construct(FileCategory $attributeCategory)
    {
        $this->attributeCategory = $attributeCategory;
    }

    /**
     * @param ReindexFileTaskCommand $command
     */
    public function __invoke(ReindexFileTaskCommand $command)
    {
        $this->output->write(t('Reindexing file ID: %s', $command->getFileID()));
        $file = File::getByID($command->getFileID());
        if ($file) {
            $indexer = $this->attributeCategory->getSearchIndexer();
            $values = $this->attributeCategory->getAttributeValues($file);
            foreach ($values as $value) {
                $indexer->indexEntry($this->attributeCategory, $value, $file);
            }
        } else {
            $this->output->write(t('File object for ID %s not found. Skipping...', $command->getFileID()));
        }
    }


}