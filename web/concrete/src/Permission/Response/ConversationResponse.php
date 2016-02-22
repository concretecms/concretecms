<?php
namespace Concrete\Core\Permission\Response;

class ConversationResponse extends Response
{
    /**
     * @todo Make this dependent on conversation-specific permissions.
     */
    public function canViewConversation()
    {
        $conversation = $this->getPermissionObject();
        if (is_object($conversation)) {
            $c = $conversation->getConversationPageObject();
            if (is_object($c) && !$c->isError()) {
                $cp = new \Permissions($c);

                return $cp->canViewPage();
            }
        }
    }
}
