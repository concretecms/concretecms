<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardSitemapAccessController extends Controller {

	var $helpers = array('form', 'validation/token');
	
	public function getAccessRow($type, $identifier = '', $name = '', $canRead = 1) {

		$form = Loader::helper('form');
		
		$html = '<div class="ccm-sitemap-permissions-entity">';
		
		$html .= $form->hidden('selectedEntity[]', $identifier);
		
		$ida = '';
		$id = '';
		
		if ($identifier != '') {
			$id = '_'. $identifier;
			$ida = '_' . $identifier . '[]';
		}
		$html .= '<h2>';
		if (($identifier != 'gID_1' && $identifier != 'gID_2')) {
			$html .= '<a href="javascript:void(0)" class="ccm-sitemap-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . $name . '</span></h2>';

		$viewExtended = (FilePermissions::PTYPE_NONE == $canSearch) ? 'style="display: none"' : '';
		
		$html .= '<table border="0" cellspacing="0" cellpadding="0" id="ccm-sitemap-permissions-grid">
		<tr class="ccm-sitemap-access-view">
			<th>' . t('Access Sitemap') . '</th>
			<td>' . $form->radio('canRead' . $id, '1', $canRead) . ' ' . t('Yes') . '</td>
			<td>' . $form->radio('canRead' . $id, 0, $canRead) . ' ' . t('No') . '</td>
		</tr></table></div><br/>';
		return $html;
	}

	public function save_global_permissions() {
		$vt = Loader::helper('validation/token');
		
		if (!$vt->validate("sitemap_permissions")) {
			$this->set('error', array($vt->getErrorMessage()));
			return;
		}	
		
		$p = $this->post();
		
		$ch = Loader::helper('concrete/dashboard/sitemap');
		
		$this->setPermission($ch, $p);
		$this->redirect('/dashboard/sitemap/access', 'global_permissions_saved');
	}
	
	protected function setPermission($ch, $post) {
		$ch->resetPermissions();
		foreach($post['selectedEntity'] as $e) {
			if ($e != '') {
				$id = substr($e, 4);
				if (strpos($e, 'uID') === 0) {
					$obj = UserInfo::getByID($id);
				} else {
					$obj = Group::getByID($id);					
				}
			
				$canRead = $post['canRead_' . $e];
				$ch->setPermissions($obj, $canRead);
			}
		}	
	}
	
	public function global_permissions_saved() {
		$this->set('message', t('Sitemap Permissions saved.'));
	}
}