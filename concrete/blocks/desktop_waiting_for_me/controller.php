<?php

namespace Concrete\Block\DesktopWaitingForMe;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Notification\Alert\AlertList;
use Concrete\Core\Notification\Alert\Filter\FilterListFactory;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 450;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 560;

    /**
     * @return array|string[]
     */
    public function getRequiredFeatures(): array
    {
        return [Features::DESKTOP];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays workflow actions waiting for you.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Waiting for Me');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $filterValues = ['' => t('** Show All')];

        $factory = $this->app->make(FilterListFactory::class);
        $filterList = $factory->createList();
        $filters = $filterList->getFilters();
        foreach ($filters as $filter) {
            $filterValues[$filter->getKey()] = $filter->getName();
        }

        $u = $this->app->make(User::class);
        /** @var AlertList $list */
        $list = $this->app->make(AlertList::class, ['user' => $u]);
        $filter = (string) $this->request->query->get('filter');
        if ($filter !== '') {
            $filterObject = $filterList->getFilterByKey($filter);
            if ($filterObject) {
                $filterObject->filterAlertList($list);
            } else {
                $filter = '';
            }
        }
        $pagination = $list->getPagination();
        $alerts = $pagination->getCurrentPageResults();
        if (!$alerts) {
            $alerts = [];
        }

        $this->set('items', $alerts);
        $this->set('filterValues', $filterValues);
        $this->set('token', $this->app->make('token'));
        $this->set('pagination', $pagination);
        $this->set('filter', $filter);
    }

    /**
     * @return void
     */
    public function action_reload_results()
    {
        $b = $this->getBlockObject();
        $bv = new BlockView($b);
        $bv->render('view');
        exit;
    }
}
