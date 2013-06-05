<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Environment_Info extends DashboardBaseController {	
	
	public function get_environment_info() {
		$maxExecutionTime = ini_get('max_execution_time');
		set_time_limit(5);
		
		$environmentMessage = '# ' . t('concrete5 Version') . "\n" . APP_VERSION . "\n\n";
		$environmentMessage .= '# ' . t('concrete5 Packages') . "\n";
		$pla = PackageList::get();
		$pl = $pla->getPackages();
		$packages = array();
		foreach($pl as $p) {
			if ($p->isPackageInstalled()) {
				$packages[] =$p->getPackageName() . ' (' . $p->getPackageVersion() . ')';
			}			
		}
		if (count($packages) > 0) {
			natcasesort($packages);
			$environmentMessage .= implode(', ', $packages);
			$environmentMessage .= ".\n";
		} else {
			$environmentMessage .= t('None') . "\n";
		}
		$environmentMessage .= "\n";
		
		// overrides
		$environmentMessage .= '# ' . t('concrete5 Overrides') . "\n";
		$fh = Loader::helper('file');
		$overrides = array();
		$ovBlocks = $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES);
		$ovControllers = $fh->getDirectoryContents(DIR_FILES_CONTROLLERS);
		$ovElements = $fh->getDirectoryContents(DIR_FILES_ELEMENTS);
		$ovHelpers = $fh->getDirectoryContents(DIR_HELPERS);
		$ovJobs = $fh->getDirectoryContents(DIR_FILES_JOBS);
		$ovCSS = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_CSS);
		$ovJS = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_JAVASCRIPT);
		$ovLng = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_LANGUAGES);
		$ovLibs = $fh->getDirectoryContents(DIR_LIBRARIES);
		$ovMail = $fh->getDirectoryContents(DIR_FILES_EMAIL_TEMPLATES);
		$ovModels = $fh->getDirectoryContents(DIR_MODELS);
		$ovSingle = $fh->getDirectoryContents(DIR_FILES_CONTENT);
		$ovThemes = $fh->getDirectoryContents(DIR_FILES_THEMES);
		$ovTools = $fh->getDirectoryContents(DIR_FILES_TOOLS);

		foreach($ovBlocks as $ovb) {
			$overrides[] = DIRNAME_BLOCKS . '/' . $ovb;
		}
		foreach($ovControllers as $ovb) {
			$overrides[] = DIRNAME_CONTROLLERS . '/' . $ovb;
		}
		foreach($ovElements as $ovb) {
			$overrides[] = DIRNAME_ELEMENTS . '/' . $ovb;
		}
		foreach($ovHelpers as $ovb) {
			$overrides[] = DIRNAME_HELPERS . '/' . $ovb;
		}
		foreach($ovJobs as $ovb) {
			$overrides[] = DIRNAME_JOBS . '/' . $ovb;
		}
		foreach($ovJS as $ovb) {
			$overrides[] = DIRNAME_JAVASCRIPT . '/' . $ovb;
		}
		foreach($ovCSS as $ovb) {
			$overrides[] = DIRNAME_CSS . '/' . $ovb;
		}
		foreach($ovLng as $ovb) {
			$overrides[] = DIRNAME_LANGUAGES . '/' . $ovb;
		}
		foreach($ovLibs as $ovb) {
			$overrides[] = DIRNAME_LIBRARIES . '/' . $ovb;
		}
		foreach($ovMail as $ovb) {
			$overrides[] = DIRNAME_MAIL_TEMPLATES . '/' . $ovb;
		}
		foreach($ovModels as $ovb) {
			$overrides[] = DIRNAME_MODELS . '/' . $ovb;
		}
		foreach($ovSingle as $ovb) {
			$overrides[] = DIRNAME_PAGES . '/' . $ovb;
		}
		foreach($ovThemes as $ovb) {
			$overrides[] = DIRNAME_THEMES . '/' . $ovb;
		}
		foreach($ovTools as $ovb) {
			$overrides[] = DIRNAME_TOOLS . '/' . $ovb;
		}

		if (count($overrides) > 0) {
			$environmentMessage .= implode(', ', $overrides);
			$environmentMessage .= "\n";
		} else {
			$environmentMessage .= t('None') . "\n";
		}
		$environmentMessage .= "\n";

		print $environmentMessage;
		
		$environmentMessage = '# ' . t('Server Software') . "\n" . $_SERVER['SERVER_SOFTWARE'] . "\n\n";
		$environmentMessage .= '# ' . t('Server API') . "\n" . php_sapi_name() . "\n\n";
		$environmentMessage .= '# ' . t('PHP Version') . "\n" . PHP_VERSION . "\n\n";
		$environmentMessage .= '# ' . t('PHP Extensions') . "\n";
		if (function_exists('get_loaded_extensions')) {
			$gle = @get_loaded_extensions();
			natcasesort($gle);
			$environmentMessage .= implode(', ', $gle);
			$environmentMessage .= ".\n";
		} else {
			$environmentMessage .= t('Unable to determine.') . "\n";
		}

		print $environmentMessage;

		ob_start();
		phpinfo();
		$phpinfo = array('phpinfo' => array());
		if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
		foreach($matches as $match) {
			if(strlen($match[1])) {
				$phpinfo[$match[1]] = array();
			} else if(isset($match[3])) {
				$phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			} else {
				$phpinfo[end(array_keys($phpinfo))][] = $match[2];
			}
		}		
		$environmentMessage = "\n# " . t('PHP Settings') . "\n";
		$environmentMessage .= "max_execution_time - " . $maxExecutionTime . "\n";
		foreach($phpinfo as $name => $section) {
			foreach($section as $key => $val) {
				if (preg_match('/.*max_execution_time*/', $key)) {
					continue;
				}
				if (!preg_match('/.*limit.*/', $key) && !preg_match('/.*safe.*/', $key) && !preg_match('/.*max.*/', $key)) {
					continue;
				}
				if(is_array($val)) {
					$environmentMessage .= "$key - $val[0]\n";
				} else if(is_string($key)) {
					$environmentMessage .= "$key - $val\n";
				} else {
					$environmentMessage .= "$val\n";
				}
			}
		}
		
		print $environmentMessage;
		exit;
	}
}