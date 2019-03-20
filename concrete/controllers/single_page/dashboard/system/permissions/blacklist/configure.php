<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist;

use Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Punic\Unit;

class Configure extends Blacklist
{
    const UNIT_SECONDS = 's';
    const UNIT_MINUTES = 'm';
    const UNIT_HOURS = 'h';
    const UNIT_DAYS = 'd';

    public function view($id = '')
    {
        $category = $this->getCategory($id);
        if ($category === null) {
            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/blacklist']),
                302
            );
        }
        $this->set('pageTitle', t('%s: Configure IP blocking', $category->getDisplayName()));
        $this->set('category', $category);
        $this->set('units', [
            self::UNIT_SECONDS => Unit::getName('duration/second', 'long'),
            self::UNIT_MINUTES => Unit::getName('duration/minute', 'long'),
            self::UNIT_HOURS => Unit::getName('duration/hour', 'long'),
            self::UNIT_DAYS => Unit::getName('duration/day', 'long'),
        ]);
    }

    public function update_ipblacklist($id = '')
    {
        $category = $this->getCategory($id);
        if ($category === null) {
            $this->flash('error', t('Unable to find the requested category'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/blacklist']),
                302
            );
        }
        try {
            if (!$this->token->validate('update_ipblacklist-' . $category->getIpAccessControlCategoryID())) {
                throw new UserMessageException($this->token->getErrorMessage());
            }
            $post = $this->request->request;
            $valn = $this->app->make('helper/validation/numbers');
            $category->setEnabled($post->get('banEnabled'));
            /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */

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
                $this->flash('success', t('IP Blacklist settings saved.'));

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

    /**
     * @param int|null $seconds
     *
     * @return array
     */
    public function splitSeconds($seconds)
    {
        if ((string) $seconds === '') {
            $selectedUnit = self::UNIT_SECONDS;
            $unitValue = '';
        } else {
            $seconds = (int) $seconds;
            if ($seconds < 60 || $seconds % 60 !== 0) {
                $selectedUnit = self::UNIT_SECONDS;
                $unitValue = (string) $seconds;
            } else {
                $minutes = round($seconds / 60);
                if ($minutes < 60 || $minutes % 60 !== 0) {
                    $selectedUnit = self::UNIT_MINUTES;
                    $unitValue = (string) $minutes;
                } else {
                    $hours = round($minutes / 60);
                    if ($hours < 24 || $hours % 24 !== 0) {
                        $selectedUnit = self::UNIT_HOURS;
                        $unitValue = (string) $hours;
                    } else {
                        $days = round($hours / 24);
                        $selectedUnit = self::UNIT_DAYS;
                        $unitValue = (string) $days;
                    }
                }
            }
        }

        return [$selectedUnit, $unitValue];
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    protected function getPostedSeconds($name)
    {
        $post = $this->request->request;
        $unit = $post->get("{$name}Unit");
        if ($unit === self::UNIT_SECONDS) {
            $multiplier = 1;
        } elseif ($unit === self::UNIT_MINUTES) {
            $multiplier = 1 * 60;
        } elseif ($unit === self::UNIT_HOURS) {
            $multiplier = 1 * 60 * 60;
        } elseif ($unit === self::UNIT_DAYS) {
            $multiplier = 1 * 60 * 60 * 24;
        } else {
            return null;
        }
        $value = $post->get("{$name}Value");
        $valn = $this->app->make('helper/validation/numbers');
        if (!$valn->integer($value, 0)) {
            return null;
        }

        return $multiplier * (int) $value;
    }
}
