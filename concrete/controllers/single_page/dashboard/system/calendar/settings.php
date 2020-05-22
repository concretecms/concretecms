<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Calendar;

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
            $config = $this->app->make('config');
            $config->save('concrete.calendar.topic_attribute', $this->request->request->get('topicAttribute'));
            $config->save('concrete.calendar.summary_thumbnail_attribute', $this->request->request->get('summaryThumbnailAttribute'));
            $this->flash('success', t('Settings saved successfully.'));
            $this->redirect('/dashboard/system/calendar/settings');
        }
    }
    public function view()
    {
        $config = $this->app->make('config');
        $keys = EventKey::getList();
        $keys = array_filter($keys, function ($ak) {
            return $ak->getAttributeTypeHandle() == 'image_file';
        });

        $summaryThumbnailAttributes = array('' => t('** Select Thumbnail Attribute'));
        foreach($keys as $key) {
            $summaryThumbnailAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName();
        }

        $summaryThumbnailAttribute = $config->get('concrete.calendar.summary_thumbnail_attribute');
        if (!$summaryThumbnailAttribute) {
            $summaryThumbnailAttribute = 'event_thumbnail';
        }

        $keys = EventKey::getList();
        $keys = array_filter($keys, function ($ak) {
            return $ak->getAttributeTypeHandle() == 'topics';
        });

        $topicAttributes = array('' => t('** Select Topic Attribute'));
        foreach($keys as $key) {
            $topicAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName();
        }

        $topicAttribute = $config->get('concrete.calendar.topic_attribute');
        if (!$topicAttribute) {
            $topicAttribute = 'calendar_topics';
        }

        $this->set('topicAttributes', $topicAttributes);
        $this->set('topicAttribute', $topicAttribute);
        $this->set('summaryThumbnailAttributes', $summaryThumbnailAttributes);
        $this->set('summaryThumbnailAttribute', $summaryThumbnailAttribute);
    }
}
