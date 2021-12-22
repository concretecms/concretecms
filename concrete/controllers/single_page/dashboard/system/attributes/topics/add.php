<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes\Topics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Type\Topic as TopicTree;

class Add extends DashboardPageController
{
    /**
     * @var string[]
     */
    protected $helpers = ['form', 'validation/token'];

    /**
     * @return void
     */
    public function view()
    {
        $this->set('pageTitle', t('Add Topic Tree'));
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function submit()
    {
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $vs */
        $vs = $this->app->make('helper/validation/strings');
        /** @var \Concrete\Core\Validation\SanitizeService $sec */
        $sec = $this->app->make('helper/security');
        $name = $sec->sanitizeString($this->post('topicTreeName'));
        if (!$this->token->validate('submit')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your tree.'));
        }
        if (!Key::getByHandle('add_topic_tree')->validate()) {
            $this->error->add(t('You do not have permission to add this tree.'));
        }
        if (!$this->error->has()) {
            $tree = TopicTree::add($name);

            return $this->buildRedirect(['/dashboard/system/attributes/topics', 'tree_added', $tree->getTreeID()]);
        }
    }
}
