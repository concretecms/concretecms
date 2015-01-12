<?php
namespace Concrete\Controller\Frontend;
use Controller;
use stdClass;
use Job;
use JobSet;
use Response;

class Jobs extends Controller {

    public function view() {

        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $r = new stdClass;
        $r->error = false;
        $r->results = array();

        if (Job::authenticateRequest($_REQUEST['auth'])) {

            $js = null;
            if ($_REQUEST['jID']) {
                $j = Job::getByID($_REQUEST['jID']);
                $r->results[] = $j->executeJob();
            } else if ($_REQUEST['jHandle']) {
                $j = Job::getByHandle($_REQUEST['jHandle']);
                $r->results[] = $j->executeJob();
            } else if ($_REQUEST['jsID']) {
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
            if(count($r->results)) {
                $response->setStatusCode(Response::HTTP_OK);
                $response->setContent(json_encode($r));
                $response->send();
                exit;
            } else {
                $r->error = t('Unknown Job');
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent(json_encode($r));
                $response->send();
                exit;
            }

        } else {
            $r->error = t('Access Denied');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->setContent(json_encode($r));
            $response->send();
            exit;
        }
    }

}

