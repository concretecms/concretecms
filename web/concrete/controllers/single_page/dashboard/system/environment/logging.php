<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Logging extends DashboardPageController{
	/**
	* Dasboard page view
	*
	* @param string $strStatus - Result of attempting to update logging settings
	* @return void
	*/
	public function view($strStatus = false){
		$strStatus = (string) $strStatus;
		$intLogErrors = Config::get('concrete.log.errors') == 1 ? 1 : 0;
		$intLogEmails = Config::get('concrete.log.errors') == 1 ? 1 : 0;
		$intLogQueriesClear = Config::get('concrete.log.queries.clear_on_reload') == 1 ? 1 : 0;
		$ingLogQueries = Config::get('concrete.log.queries.log') == 1 ? 1 : 0;

		$this->set('fh', Loader::helper('form'));
		$this->set('intLogErrors', $intLogErrors);
		$this->set('intLogEmails', $intLogEmails);
		$this->set('intLogQueries', $ingLogQueries);
		$this->set('intLogQueriesClear', $intLogQueriesClear);

		if($strStatus == 'logging_saved'){
			$this->set('message', t('Logging configuration saved.'));
		}
	}


	/**
	* Updates logging settings
	*
	* @return void
	*/
	public function update_logging(){
		if($this->token->validate('update_logging')){
			if($this->isPost()){
				$intLogErrorsCurrent = Config::get('concrete.log.errors') == 1 ? 1 : 0;
				$intLogEmailsCurrent = Config::get('concrete.log.emails') == 1 ? 1 : 0;

				$intLogErrorsPost = $this->post('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
				$intLogEmailsPost = $this->post('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;

				$intLogQueries = $this->post('ENABLE_LOG_QUERIES') == 1 ? 1 : 0;
				$intLogQueriesClearOnReload = $this->post('ENABLE_LOG_QUERIES_CLEAR') == 1 ? 1 : 0;

				Config::save('concrete.log.errors', $intLogErrorsPost);
				Config::save('concrete.log.emails', $intLogEmailsPost);
				Config::save('concrete.log.queries.log', $intLogQueries);
				Config::save('concrete.log.queries.clear_on_reload', $intLogQueriesClearOnReload);

				$this->redirect('/dashboard/system/environment/logging', 'logging_saved');
			}
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
}
?>
