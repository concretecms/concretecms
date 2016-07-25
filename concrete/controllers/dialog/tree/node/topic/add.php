<?php
namespace Concrete\Controller\Dialog\Tree\Node\Topic;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Tree\Node\Type\Topic;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/topic/add';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canAddTopicTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function add_topic_node()
    {
        $token = \Core::make('token');
        $error = \Core::make('error');
        $parent = $this->getNode();
        if (!$token->validate('add_topic_node')) {
            $error->add($token->getErrorMessage());
        }

        $title = $_POST['treeNodeTopicName'];
        if (!$title) {
            $error->add(t('Invalid title for topic.'));
        }

        if (!is_object($parent)) {
            $error->add(t('Invalid parent category'));
        }

        if (!$error->has()) {
            $topic = Topic::add($title, $parent);
            $r = $topic->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
