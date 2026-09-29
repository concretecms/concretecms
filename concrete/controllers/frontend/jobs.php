<?php
namespace Concrete\Controller\Frontend;

use Controller;
use stdClass;
use Job;
use JobSet;
use Response;

/**
 * @Deprecated. Use tasks instead.
 */
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

        if (Job::authenticateRequest($this->request->request->get('auth', $this->request->query->get('auth')))) {
            $js = null;
            $jID = $this->request->request->get('jID', $this->request->query->get('jID'));
            if ($jID) {
                $j = Job::getByID($jID);
                $r->results[] = $j->executeJob();
            } else {
                $jHandle = $this->request->request->get('jHandle', $this->request->query->get('jHandle'));
                if ($jHandle) {
                    $j = Job::getByHandle($jHandle);
                    $r->results[] = $j->executeJob();
                } else {
                    $jsID = $this->request->request->get('jsID', $this->request->query->get('jsID'));
                    if ($jsID) {
                        $js = JobSet::getByID($jsID);
                    } else {
                        // default set legacy support
                        $js = JobSet::getDefault();
                    }
                }
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
        if (Job::authenticateRequest($this->request->request->get('auth', $this->request->query->get('auth')))) {
            $jHandle = $this->request->request->get('jHandle', $this->request->query->get('jHandle'));
            if ($jHandle) {
                $job = Job::getByHandle($jHandle);
            } else {
                $jID = (int) $this->request->request->get('jID', $this->request->query->get('jID'));
                if ($jID !== 0) {
                    $job = Job::getByID($jID);
                }
            }

            if (is_object($job)) {
                // Note - we used to have queue logic in here, but queueing with jobs is no longer supported.
                $r = $job->executeJob();
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
}
