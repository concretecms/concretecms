<?php
namespace Concrete\Controller\Frontend;

use Concrete\Core\Job\QueueableJob;
use Controller;
use stdClass;
use Job;
use JobSet;
use Response;

class Jobs extends Controller
{
    public function view()
    {
        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
        }

        //Disable job scheduling so we don't end up in a loop
        \Config::set('concrete.jobs.enable_scheduling', false);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $r = new stdClass();
        $r->error = false;
        $r->results = array();

        if (Job::authenticateRequest($_REQUEST['auth'])) {
            $js = null;
            if ($_REQUEST['jID']) {
                $j = Job::getByID($_REQUEST['jID']);
                $r->results[] = $j->executeJob();
            } elseif ($_REQUEST['jHandle']) {
                $j = Job::getByHandle($_REQUEST['jHandle']);
                $r->results[] = $j->executeJob();
            } elseif ($_REQUEST['jsID']) {
                $js = JobSet::getByID($_REQUEST['jsID']);
            } else {
                // default set legacy support
                $js = JobSet::getDefault();
            }

            if (is_object($js)) {
                $jobs = $js->getJobs();
                $js->markStarted();
                foreach ($jobs as $j) {
                    $obj = $j->executeJob();
                    $r->results[] = $obj;
                }
            }
            if (count($r->results)) {
                $response->setStatusCode(Response::HTTP_OK);
                $response->setContent(json_encode($r));
                $response->send();
                \Core::shutdown();
            } else {
                $r->error = t('Unknown Job');
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent(json_encode($r));
                $response->send();
                \Core::shutdown();
            }
        } else {
            $r->error = t('Access Denied');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->setContent(json_encode($r));
            $response->send();
            \Core::shutdown();
        }
    }

    public function run_single()
    {
        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
        }

        //Disable job scheduling so we don't end up in a loop
        \Config::set('concrete.jobs.enable_scheduling', false);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $r = new stdClass();
        $r->error = false;

        $job = null;
        if (Job::authenticateRequest($_REQUEST['auth'])) {
            if (strlen($_REQUEST['jHandle']) > 0 || intval($_REQUEST['jID']) > 0) {
                if ($_REQUEST['jHandle']) {
                    $job = Job::getByHandle($_REQUEST['jHandle']);
                } else {
                    $job = Job::getByID(intval($_REQUEST['jID']));
                }
            }

            if (is_object($job)) {
                if ($job instanceof QueueableJob && $job->supportsQueue()) {
                    $q = $job->getQueueObject();

                    if ($_POST['process']) {
                        $obj = new stdClass();
                        $obj->error = false;
                        try {
                            $messages = $q->receive($job->getJobQueueBatchSize());
                            $job->executeBatch($messages, $q);

                            $totalItems = $q->count();
                            $obj->totalItems = $totalItems;
                            if ($q->count() == 0) {
                                $result = $job->finish($q);
                                $obj = $job->markCompleted(0, $result);
                                $obj->error = false;
                                $obj->totalItems = $totalItems;
                            }
                        } catch (\Exception $e) {
                            $obj = $job->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
                            $obj->error = true;
                            $obj->message = $obj->result; // needed for progressive library.
                        }
                        $response->setStatusCode(Response::HTTP_OK);
                        $response->setContent(json_encode($obj));
                        $response->send();
                        \Core::shutdown();
                    } else {
                        if ($q->count() == 0) {
                            $q = $job->markStarted();
                            $job->start($q);
                        }
                    }

                    $totalItems = $q->count();
                    \View::element('progress_bar', array(
                        'totalItems' => $totalItems,
                        'totalItemsSummary' => t2("%d item", "%d items", $totalItems),
                    ));
                    \Core::shutdown();
                } else {
                    $r = $job->executeJob();
                    $response->setStatusCode(Response::HTTP_OK);
                    $response->setContent(json_encode($r));
                    $response->send();
                    \Core::shutdown();
                }
            } else {
                $r->error = t('Unknown Job');
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent(json_encode($r));
                $response->send();
                \Core::shutdown();
            }
        } else {
            $r->error = t('Access Denied');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->setContent(json_encode($r));
            $response->send();
            \Core::shutdown();
        }
    }

    public function check_queue()
    {
        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
        }
        //Disable job scheduling so we don't end up in a loop
        \Config::set('concrete.jobs.enable_scheduling', false);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $r = new stdClass();
        $r->error = false;
        $r->results = array();

        if (Job::authenticateRequest($_REQUEST['auth'])) {
            $list = Job::getList();
            foreach ($list as $job) {
                if ($job->supportsQueue() && $job instanceof QueueableJob) {
                    $q = $job->getQueueObject();
                    // don't process queues that are empty
                    if ($q->count() < 1) {
                        continue;
                    }
                    $obj = new stdClass();
                    try {
                        $messages = $q->receive($job->getJobQueueBatchSize());
                        $job->executeBatch($messages, $q);

                        $totalItems = $q->count();
                        $obj->totalItems = $totalItems;
                        $obj->jHandle = $job->getJobHandle();
                        $obj->jID = $job->getJobID();

                        if ($q->count() == 0) {
                            $result = $job->finish($q);
                            $obj = $job->markCompleted(0, $result);
                            $obj->totalItems = $totalItems;
                        }
                    } catch (\Exception $e) {
                        $obj = $job->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
                        $obj->message = $obj->result; // needed for progressive library.
                    }

                    $r->results[] = $obj;
                    // End when one queue has processed a batch step
                    break;
                }
            }
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode($r));
            $response->send();
            \Core::shutdown();
        } else {
            $r->error = t('Access Denied');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->setContent(json_encode($r));
            $response->send();
            \Core::shutdown();
        }
    }
}
