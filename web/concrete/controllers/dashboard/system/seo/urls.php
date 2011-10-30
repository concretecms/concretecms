<?
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemSeoUrlsController extends DashboardBaseController{
	/**
	* Returns the mod_rewrite rules
	*
	* @return string
	*/
	public function getRewriteRules(){
		$strRules = '
		<IfModule mod_rewrite.c>
		RewriteEngine On
		RewriteBase ' . DIR_REL . '/
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^(.*)$ ' . DISPATCHER_FILENAME . '/$1 [L]
		</IfModule>';
		
		return preg_replace('/\t/', '', $strRules);
	}
	
	
	/**
	* Returns the .htaccess text to be copied/inserted
	*
	* @return string
	*/	
	public function getHtaccessText(){
		$strHt = '		
		# -- concrete5 urls start --'
		. $this->getRewriteRules() . '
		# -- concrete5 urls end --
		';
		
		return preg_replace('/\t/', '', $strHt);	
	}
	
	
	
	/**
	* Dasboard page view
	*
	* @param string $status - Result of attempting to update rewrite rules
	* @param boolean $blnHtu - Flag denoting if the .htaccess file was writable or not
	* @return void
	*/		
	public function view($strStatus = false, $blnHtu = false){
		$strStatus = (string) $strStatus;
		$blnHtu = (bool) $blnHtu;
		
		$this->set('fh', Loader::helper('form'));
		$this->set('strRules', $this->getRewriteRules());
		$this->set('intRewriting', (int) URL_REWRITING);
		
		if($strStatus == 'rewriting_saved'){		
			if(URL_REWRITING && !$blnHtu){	
				$this->set('message', t('URL rewriting enabled. Make sure you copy the lines below these URL Rewriting settings area and place them in your .htaccess or web server configuration file.'));					
			}elseif(URL_REWRITING && $blnHtu){
				$this->set('message', t('URL rewriting enabled. .htaccess file updated.'));
			}else{
				$this->set('message', t('URL rewriting disabled.'));
			}	
		}
	}
	
	
	/**
	* Updates the .htaccess file (if writable)
	*
	* @return void
	*/		
	public function update_rewriting(){
		$strHtText = $this->getHtaccessText();		
		$blnHtu = 0;
		
		if($this->isPost()){
			$intCurrent = (int) Config::get('URL_REWRITING');
			$intPosted = (int) $this->post('URL_REWRITING');
			
			// If there was no change we don't attempt to edit/create the .htaccess file
			if($intCurrent == $intPosted){
				$this->redirect('/dashboard/system/seo/urls');
			}
			
			Config::save('URL_REWRITING', $intPosted);
						
			if($this->post('URL_REWRITING') == 1){				
				if(file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')){		
					if(file_put_contents(DIR_BASE . '/.htaccess', $strHtText, FILE_APPEND)){
						$blnHtu = 1;
					}
				}elseif(!file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE)){		
					if(file_put_contents(DIR_BASE . '/.htaccess', $strHtText)){
						$blnHtu = 1;
					}
				}
			}else{
				if(file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')){
					$fh = Loader::helper('file');
					$contents = $fh->getContents(DIR_BASE . '/.htaccess');
					
					if(file_put_contents(DIR_BASE . '/.htaccess', str_replace($strHtText, '', $contents))){
						$blnHtu = 1;
					}
				}
			}
			
			$this->redirect('/dashboard/system/seo/urls', 'rewriting_saved', $blnHtu);
		}
	}	
}