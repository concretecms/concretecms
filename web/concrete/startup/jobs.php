<?php
defined('C5_EXECUTE') or die('Access Denied.');

if(ENABLE_JOB_SCHEDULING) {
	$c = Page::getCurrentPage();
	if($c instanceof Page && !$c->isAdminArea()) {
		// check for non dashboard page
		$jobs = Job::getList(true);
		$auth = Job::generateAuth();
		$url = "";
		// jobs
		if(count($jobs)) {
			foreach($jobs as $j) {
				if($j->isScheduledForNow()) {
					$url = BASE_URL . View::url('/tools/required/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID());
					break;
				}
			}
		}
	
		// job sets
		if(!strlen($url)) {
			$jSets = JobSet::getList();
			if(is_array($jSets) && count($jSets)) {
				foreach($jSets as $set) {
					if($set->isScheduledForNow()) {
						$url = BASE_URL . View::url('/tools/required/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID());
						break;
					}
				}
			}
		}
	
		if(strlen($url)) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
			curl_setopt($ch,CURLOPT_TIMEOUT,1);
			$res = curl_exec($ch);
		}
	}
}
