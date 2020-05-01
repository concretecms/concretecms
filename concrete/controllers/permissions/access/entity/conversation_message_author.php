<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\ConversationMessageAuthorEntity;

class ConversationMessageAuthor extends AccessEntity
{

    public function deliverEntity()
    {
        return ConversationMessageAuthorEntity::getOrCreate();
    }
}
