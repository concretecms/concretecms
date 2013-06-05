<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Environment_Logging extends DashboardBaseController{
	/**
	* Dasboard page view
	*
	* @param string $strStatus - Result of attempting to update logging settings
	* @return void
	*/		
	public function view($strStatus = false){
		$strStatus = (string) $strStatus;
		$intLogErrors = Config::get('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
		$intLogEmails = Config::get('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;
		
		$this->set('fh', Loader::helper('form'));
		$this->set('intLogErrors', $intLogErrors);
		$this->set('intLogEmails', $intLogEmails);
		
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
				$intLogErrorsCurrent = Config::get('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
				$intLogEmailsCurrent = Config::get('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;				
				
				$intLogErrorsPost = $this->post('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
				$intLogEmailsPost = $this->post('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;
				
				// If there was no change simply reload this page
				if($intLogErrorsCurrent == $intLogErrorsPost && $intLogEmailsCurrent == $intLogEmailsPost){
					$this->redirect('/dashboard/system/environment/logging');
				}
								
				Config::save('ENABLE_LOG_ERRORS', $intLogErrorsPost);
				Config::save('ENABLE_LOG_EMAILS', $intLogEmailsPost);
				
				$this->redirect('/dashboard/system/environment/logging', 'logging_saved');
			}
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}	
}
?>