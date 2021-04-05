<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\File\File;
use Concrete\Core\User\UserInfoRepository;

class ReindexUserTaskCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var UserCategory
     */
    protected $attributeCategory;

    /**
     * @var UserInfoRepository
     */
    protected $repository;

    /**
     * @param UserCategory $attributeCategory
     */
    public function __construct(UserCategory $attributeCategory, UserInfoRepository $repository)
    {
        $this->attributeCategory = $attributeCategory;
        $this->repository = $repository;
    }

    /**
     * @param ReindexUserTaskCommand $command
     */
    public function __invoke(ReindexUserTaskCommand $command)
    {
        $this->output->write(t('Reindexing user ID: %s', $command->getUserID()));
        $user = $this->repository->getByID($command->getUserID());
        if ($user) {
            $indexer = $this->attributeCategory->getSearchIndexer();
            $values = $this->attributeCategory->getAttributeValues($user);
            foreach ($values as $value) {
                $indexer->indexEntry($this->attributeCategory, $value, $user);
            }
        } else {
            $this->output->write(t('Userinfo object for ID %s not found. Skipping...', $command->getUserID()));
        }
    }


}