<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Config;
class Urls extends DashboardPageController {
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
		RewriteCond %{REQUEST_FILENAME}/index.html !-f
		RewriteCond %{REQUEST_FILENAME}/index.php !-f
		RewriteRule . ' . DISPATCHER_FILENAME .' [L]
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
	* @param string $strStatus - Result of attempting to update rewrite rules
	* @param boolean $blnHtu - Flag denoting if the .htaccess file was writable or not
	* @return void
	*/
	public function view($strStatus = false, $blnHtu = false){
		$strStatus = (string) $strStatus;
		$blnHtu = (bool) $blnHtu;
		$intRewriting = Config::get('concrete.seo.url_rewriting') == 1 ? 1 : 0;

		$this->set('fh', Loader::helper('form'));
		$this->set('strRules', $this->getRewriteRules());
		$this->set('intRewriting', $intRewriting);

		if($strStatus == 'rewriting_saved'){
			if(Config::get('concrete.seo.url_rewriting') && !$blnHtu){
				$this->set('message', t('URL rewriting enabled. You need to update .htaccess by hand.'));
			}elseif(Config::get('concrete.seo.url_rewriting') && $blnHtu){
				$this->set('message', t('URL rewriting enabled. We were able to automatically update .htaccess file.'));
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
		if($this->token->validate('update_rewriting')){
			$strHtText = (string) $this->getHtaccessText();
			$blnHtu = 0;

			if($this->isPost()){
				$intCurrent = Config::get('concrete.seo.url_rewriting') == 1 ? 1 : 0;
				$intPosted = $this->post('URL_REWRITING') == 1 ? 1 : 0;

				// If there was no change we don't attempt to edit/create the .htaccess file
				if($intCurrent == $intPosted){
					$this->redirect('/dashboard/system/seo/urls');
				}

				Config::save('concrete.seo.url_rewriting', $intPosted);

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
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
}
?>
