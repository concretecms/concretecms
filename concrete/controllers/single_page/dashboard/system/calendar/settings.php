<?php
namespace Concrete\Core\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Attribute\Key\EventKey;

class Settings extends DashboardPageController
{
    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = \Package::getByHandle('calendar')->getFileConfig();
            $config->save('calendar.topic_attribute', $this->request->request->get('topicAttribute'));
            $this->flash('success', t('Settings saved successfully.'));
            $this->redirect('/dashboard/system/calendar/settings');
        }
    }
    public function view()
    {
        $config = \Package::getByHandle('calendar')->getFileConfig();
        $keys = EventKey::getList(array('atHandle' => 'topics'));
        $keys = array_filter($keys, function ($ak) {
            return $ak->getAttributeTypeHandle() == 'topics';
        });

        $topicAttributes = array();
        foreach($keys as $key) {
            $topicAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName();
        }

        $topicAttribute = $config->get('calendar.topic_attribute');
        if (!$topicAttribute) {
            $topicAttribute = 'calendar_topics';
        }

        $this->set('topicAttributes', $topicAttributes);
        $this->set('topicAttribute', $topicAttribute);
    }
}
