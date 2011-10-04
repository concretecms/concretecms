<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/dashboard/base');
class DashboardSitemapFullController extends DashboardBaseController {

	public function getQuickNavigationLinkHTML() {
		return '<a href="javascript:void(0)" onclick="jQuery.fn.dialog.open({modal:false, title: \'' . t('Navigate to Page') . '\', href: \'' . REL_DIR_FILES_TOOLS_REQUIRED . '/sitemap_search_selector?callback=ccm_goToSitemapNode&sitemap_select_mode=select_page\', width: \'90%\', height: \'70%\'})">' . t('Sitemap and Page Search') . '</a>';
	}
	
}