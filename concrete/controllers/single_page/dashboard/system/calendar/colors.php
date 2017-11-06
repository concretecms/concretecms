<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Node\Type\Topic;
use Concrete\Core\Calendar\Utility\Preferences;

class Colors extends DashboardPageController
{
    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $config->save('concrete.calendar.colors.background', $this->request->request->get('defaultBackgroundColor'));
            $config->save('concrete.calendar.colors.text', $this->request->request->get('defaultTextColor'));

            $data = $this->request->request->all();
            $overrides = array();
            if (isset($data['override'])) {
                foreach ($data['override'] as $treeNodeID) {
                    $node = Node::getByID($treeNodeID);
                    if ($node instanceof Topic) {
                        $overrides[$node->getTreeNodeName()] = array(
                            'background' => $data['backgroundColor'][$node->getTreeNodeID()],
                            'text' => $data['textColor'][$node->getTreeNodeID()],
                        );
                    }
                }
            }
            $config->save('concrete.calendar.colors.categories', $overrides);
            $this->flash('success', t('Colors saved successfully.'));
            $this->redirect('/dashboard/system/calendar/colors');
        }
    }
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('defaultBackgroundColor', $config->get('concrete.calendar.colors.background'));
        $this->set('defaultTextColor', $config->get('concrete.calendar.colors.text'));
        $this->set('categories', (array) $config->get('concrete.calendar.colors.categories'));

        /**
         * @var $preferences Preferences
         */
        $preferences = $this->app->make(Preferences::class);
        $ak = $preferences->getCalendarTopicsAttributeKey();
        if (is_object($ak)) {
            $node = Node::getByID($ak->getController()->getTopicParentNode());
            $topics = array();
            if ($node instanceof Category) {
                $node->populateChildren();
                $topics = $node->getChildNodes();
            }
            $this->set('topics', $topics);
        }
    }
}
