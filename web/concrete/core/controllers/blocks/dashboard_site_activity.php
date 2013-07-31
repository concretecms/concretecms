<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the block that displays site activity, including statistics, in the dashboard.
 *
 * @package Blocks
 * @subpackage Dashboard Site Activity
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_DashboardSiteActivity extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Displays a summary of website activity.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Site Activity");
		}
		
		public function view() {
			$u = new User();
			$ui = UserInfo::getByID($u->getUserID());
			Loader::model("page_statistics");
			Loader::model("user_statistics");
			Loader::block('form');
			$dh = Loader::helper('date');
			if (is_object($ui)) { 
				$this->set('uLastLogin', $dh->date(DATE_APP_GENERIC_MDYT, $ui->getLastLogin('user')));
				$this->set('uName', $ui->getUserName());
				$this->set('lastEditSite', $dh->date(DATE_APP_GENERIC_MDYT, strtotime(PageStatistics::getSiteLastEdit('user'))));
				$llu = UserStatistics::getLastLoggedInUser();
				if ($llu->getUserID() == $u->getUserID()) {
					$this->set('lastLoginSite', t('Your login is the most recent.'));		
				} else { 
					$this->set('lastLoginSite', $dh->date(DATE_APP_GENERIC_MDYT, $llu->getLastLogin()));
				}
			}				
			$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
			$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		}
		
	}