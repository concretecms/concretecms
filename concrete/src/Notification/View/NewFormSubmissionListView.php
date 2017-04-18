<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Entity\Notification\NewFormSubmissionNotification;
use Concrete\Core\Entity\Notification\UserSignupNotification;
use HtmlObject\Element;

class NewFormSubmissionListView extends StandardListView
{

    /**
     * @var NewFormSubmissionNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('New Form Submission');
    }

    public function getIconClass()
    {
        return 'fa fa-pencil-square-o';
    }

    public function getActionDescription()
    {
        $entry = $this->notification->getEntry();
        $entity = $entry->getEntity();
        return t('New form submission: <a href="%s"><strong>%s</strong></a>.',
            \URL::to('/dashboard/reports/forms', 'view_entry', $entry->getID()), $entity->getEntityDisplayName());
    }


}
