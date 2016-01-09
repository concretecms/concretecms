<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;

class Points extends DashboardPageController
{
    public function view()
    {
        $ratingTypes = array_reverse(ConversationRatingType::getList());
        $this->set('ratingTypes', $ratingTypes);
    }
    public function success() {
        $this->view();
        $this->set('message', t('Rating types updated.'));
    }
    public function save() {
        $db = Loader::db();
        foreach (ConversationRatingType::getList() as $crt) {
            $rtID = $crt->getConversationRatingTypeID();
            $rtPoints = $this->post('rtPoints_' . $rtID);
            if (is_string($rtPoints) && is_numeric($rtPoints)) {
                $db->Execute('UPDATE ConversationRatingTypes SET cnvRatingTypeCommunityPoints = ? WHERE cnvRatingTypeID = ? LIMIT 1', array($rtPoints, $rtID));
            }
        }
        $this->redirect('/dashboard/system/conversations/points', 'success');
    }
}
