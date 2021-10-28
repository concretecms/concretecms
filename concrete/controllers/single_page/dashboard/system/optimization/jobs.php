<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Cache\Level\OverridesCache;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\Set as JobSet;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Jobs extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();
        // clear the environment overrides cache first
        $this->app->make(OverridesCache::class)->flush();
        $this->set('dh', $this->app->make('date'));
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        $this->set('availableJobs', Job::getAvailableList($this->canInstallCoreJobs()));
        $this->set('installedJobs', Job::getList());
        $this->set('defaultJobSet', JobSet::getDefault());
        $this->set('jobSets', JobSet::getList());
        $this->set('editingJobSet', null);
        $this->set('auth', Job::generateAuth());
        $this->addHeaderItem(
            <<<'EOT'
<style>
#ccm-jobs-list td {
    vertical-align: middle;
    -webkit-transition-property: color, background-color;
    -webkit-transition-duration: .9s, .9s;
    -moz-transition-property: color, background-color;
    -moz-transition-duration: .9s, .9s;
    -o-transition-property: color, background-color;
    -o-transition-duration: .9s, .9s;
    -ms-transition-property: color, background-color;
    -ms-transition-duration: .9s, .9s;
    transition-property: color, background-color;
    transition-duration: .9s, .9s;
}
</style>
EOT
        );
    }

    public function view()
    {
        $this->set('activeTab', 'jobs');
    }

    public function view_sets()
    {
        $this->set('activeTab', 'jobSets');
    }

    public function reset($token = '')
    {
        if (!$this->token->validate('reset_jobs', $token)) {
            $this->error->add($this->token->getErrorMessage());

            return $this->view();
        }

        $jobs = Job::getList();
        foreach ($jobs as $j) {
            $j->reset();
        }
        $this->flash('success', t('All running jobs have been reset.'));

        return $this->buildRedirect($this->action(''));
    }

    public function update_job_schedule($jID = '')
    {
        if (!$this->token->validate("update_job_schedule{$jID}")) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $post = $this->request->request;
            $J = Job::getByID($jID);
            if ($J === null) {
                $this->error->add(t('Job not found.'));
            } else {
                $isScheduled = (bool) $post->get("isScheduled{$J->getJobID()}");
                if ($isScheduled) {
                    $intervalValue = (string) $post->get("value{$J->getJobID()}");
                    if ($intervalValue !== (string) (int) $intervalValue || (($intervalValue = (int) $intervalValue)) < 0) {
                        $this->error->add(t('Please specify the interval value.'));
                    }
                    $intervalUnit = $post->get("unit{$J->getJobID()}");
                    if (!in_array($intervalUnit, ['minutes', 'hours', 'days', 'weeks', 'months'], true)) {
                        $this->error->add(t('Please specify the interval unit.'));
                    }
                } else {
                    $intervalValue = $J->scheduledValue;
                    $intervalUnit = $J->scheduledInterval;
                }
                if (!$this->error->has()) {
                    $J->setSchedule($isScheduled, $intervalUnit, $intervalValue);
                    $this->flash('success', t('Job schedule updated successfully.'));

                    return $this->buildRedirect($this->action(''));
                }
            }
        }

        return $this->view();
    }

    public function uninstall($job_id = null, $token = null)
    {
        if (!$this->token->validate('uninstall_job', $token)) {
            $this->error->add($this->token->getErrorMessage());
        } elseif (!$job_id) {
            $this->error->add(t('No job specified.'));
        } else {
            $job = Job::getByID((int) $job_id);
            if (!$job) {
                $this->error->add(t('Job not found.'));
            } else {
                if ($job->jNotUninstallable) {
                    $this->error->add(t('This job cannot be uninstalled.'));
                } else {
                    $job->uninstall();
                    $this->flash('success', t('Job successfully uninstalled.'));

                    return $this->buildRedirect($this->action(''));
                }
            }
        }

        return $this->view();
    }

    public function install($handle = null, $token = null)
    {
        if (!$this->token->validate('install_job', $token)) {
            $this->error->add($this->token->getErrorMessage());
        } elseif (!$handle) {
            $this->error->add(t('No job specified.'));
        } else {
            Job::installByHandle($handle);
            $this->flash('message', t('Job successfully installed.'));

            return $this->buildRedirect($this->action(''));
        }

        return $this->view();
    }

    public function edit_set($jsID = false)
    {
        if ($jsID === 'new') {
            $js = null;
        } else {
            $js = JobSet::getByID($jsID);
            if ($js === null) {
                $this->error->add(t('Invalid Job set.'));

                return $this->buildRedirect($this->action(''));
            }
        }
        $this->set('editingJobSet', $js ?: true);
        $this->set('pageTitle', $js ? $js->getJobSetName() : t('New Job Set'));

        return $this->view_sets();
    }

    public function update_set($jsID = '')
    {
        if ($jsID === 'new') {
            $js = null;
        } else {
            $js = JobSet::getByID($jsID);
            if ($js === null) {
                $this->error->add(t('Invalid Job set.'));

                return $this->view_sets();
            }
        }
        if (!$this->token->validate("update_set{$jsID}")) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $post = $this->request->request;
            $name = trim($post->get('jsName'));
            if ($name === '') {
                $this->error->add(t('Specify a name for your Job set.'));
            }
            $jobIDs = $post->get('jID');
            $jobIDs = is_array($jobIDs) ? array_unique(array_filter(array_map('intval', $jobIDs))) : [];

            $isScheduled = (bool) $post->get('isScheduled');
            if ($isScheduled) {
                $intervalValue = (string) $post->get('value');
                if ($intervalValue !== (string) (int) $intervalValue || (($intervalValue = (int) $intervalValue)) < 0) {
                    $this->error->add(t('Please specify the interval value.'));
                }
                $intervalUnit = $post->get('unit');
                if (!in_array($intervalUnit, ['hours', 'days', 'weeks', 'months'], true)) {
                    $this->error->add(t('Please specify the interval unit.'));
                }
            } else {
                $intervalValue = $js === null ? 0 : $js->scheduledValue;
                $intervalUnit = $js === null ? 'days' : $js->scheduledInterval;
            }
            if (!$this->error->has()) {
                if ($js === null) {
                    $js = JobSet::add($name);
                } else {
                    $js->updateJobSetName($name);
                    $js->clearJobs();
                }
                foreach ($jobIDs as $jobID) {
                    $j = Job::getByID($jobID);
                    if ($j !== null) {
                        $js->addJob($j);
                    }
                }
                $js->setSchedule($isScheduled, $intervalUnit, $intervalValue);

                $this->flash('success', $jsID === 'new' ? t('Job set added.') : t('Job Set updated successfully.'));

                return $this->buildRedirect($this->action('view_sets'));
            }
        }

        return $this->edit_set($jsID);
    }

    public function delete_set($jsID = '')
    {
        $js = null;
        if ($this->token->validate("delete_set{$jsID}")) {
            $js = JobSet::getByID($jsID);
            if ($js === null) {
                $this->error->add(t('Invalid Job set.'));
            } elseif (!$js->canDelete()) {
                $this->error->add(t('You cannot delete the default Job set.'));
            }
            if (!$this->error->has()) {
                $js->delete();
                $this->flash('success', t('Job set deleted successfully.'));

                return $this->buildRedirect($this->action('view_sets'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        return $js === null ? $this->view_sets() : $this->edit_set($js->getJobSetID());
    }

    protected function canInstallCoreJobs(): bool
    {
        return false;
    }
}
