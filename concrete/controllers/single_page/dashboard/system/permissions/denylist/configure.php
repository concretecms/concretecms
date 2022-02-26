<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions\Denylist;

use Concrete\Controller\SinglePage\Dashboard\System\Permissions\Denylist;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Punic\Unit;

class Configure extends Denylist
{
    public function view($id = '')
    {
        $category = $this->getCategory($id);
        if ($category === null) {
            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/denylist']),
                302
            );
        }
        $this->set('pageTitle', t('%s: Configure IP blocking', $category->getDisplayName()));
        $this->set('category', $category);
        $units = [];
        foreach (IpAccessControlCategory::TIMEWINDOW_UNITS as $unitName) {
            $units[$unitName] = Unit::getName($unitName, 'long');
        }
        $this->set('units', $units);
    }

    public function update_ipdenylist($id = '')
    {
        $category = $this->getCategory($id);
        if ($category === null) {
            $this->flash('error', t('Unable to find the requested category'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/denylist']),
                302
            );
        }
        try {
            if (!$this->token->validate('update_ipdenylist-' . $category->getIpAccessControlCategoryID())) {
                throw new UserMessageException($this->token->getErrorMessage());
            }
            $post = $this->request->request;
            $valn = $this->app->make('helper/validation/numbers');
            $category->setEnabled($post->get('banEnabled'));
            $maxEvents = $post->get('maxEvents');
            if ($valn->integer($maxEvents, 1)) {
                $category->setMaxEvents($maxEvents);
            } else {
                $this->error->add(t('Please specify a number greater than zero for the maximum number of events'), 'maxEvents');
            }

            $timeWindow = $this->getPostedSeconds('timeWindow');
            if ($timeWindow === null || $timeWindow > 0) {
                $category->setTimeWindow($timeWindow);
            } else {
                $this->error->add(t('Please specify a number greater than zero for the time window'));
            }

            if ($post->get('banDurationUnlimited')) {
                $category->setBanDuration(null);
            } else {
                $banDuration = $this->getPostedSeconds('banDuration');
                if ($banDuration !== null && $banDuration > 0) {
                    $category->setBanDuration($banDuration);
                } else {
                    $this->error->add(t('Please specify a number greater than zero for the ban duration'));
                }
            }

            if (!$this->error->has()) {
                $this->entityManager->flush($category);
                $this->flash('success', t('IP Denylist settings saved.'));

                return $this->app->make(ResponseFactoryInterface::class)->redirect(
                    $this->action('view', $category->getIpAccessControlCategoryID()),
                    302
                );
            }
        } catch (UserMessageException $x) {
            $this->error->add($x->getMessage());
        }
        $this->view($category->getIpAccessControlCategoryID());
    }

    protected function getPostedSeconds(string $name): ?int
    {
        $post = $this->request->request;
        $unitName = $post->get("{$name}Unit");
        $multiplier = array_search($unitName, IpAccessControlCategory::TIMEWINDOW_UNITS, true);
        if (!$multiplier) {
            return null;
        }
        $multiplier = (int) $multiplier;
        $value = $post->get("{$name}Value");
        $valn = $this->app->make('helper/validation/numbers');
        if (!$valn->integer($value, 0)) {
            return null;
        }

        return $multiplier * (int) $value;
    }
}
