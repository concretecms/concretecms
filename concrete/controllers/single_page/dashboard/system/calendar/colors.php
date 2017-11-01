<?php
namespace Concrete\Core\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Node\Type\Topic;

class Colors extends DashboardPageController
{
    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = \Package::getByHandle('calendar')->getFileConfig();
            $config->save('calendar.colors.background', $this->request->request->get('defaultBackgroundColor'));
            $config->save('calendar.colors.text', $this->request->request->get('defaultTextColor'));

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
            $config->save('calendar.colors.categories', $overrides);
            $this->flash('success', t('Colors saved successfully.'));
            $this->redirect('/dashboard/system/calendar/colors');
        }
    }
    public function view()
    {
        $package = \Package::getByHandle('calendar');
        $config = $package->getFileConfig();
        $this->set('defaultBackgroundColor', $config->get('calendar.colors.background'));
        $this->set('defaultTextColor', $config->get('calendar.colors.text'));
        $this->set('categories', (array) $config->get('calendar.colors.categories'));

        $ak = $package->getCalendarTopicsAttributeKey();
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
