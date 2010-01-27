<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div style="width: 760px">

<?
$ih = Loader::helper('concrete/interface');
$cs = Loader::helper('concrete/dashboard/sitemap');

$gl = new GroupList($cs);
$ul = new UserInfoList($cs);
$uArray = $ul->getUserInfoList();

?>

<script type="text/javascript">

ccm_triggerSelectUser = function(uID, uName) {
	ccm_sitemapSelectPermissionsEntity('uID', uID, uName);
}

ccm_triggerSelectGroup = function (gID, gName) {
	ccm_sitemapSelectPermissionsEntity('gID', gID, gName);
}

$(function() {	
	$("#ug-selector").dialog();	
	ccm_sitemapActivatePermissionsSelector();	
});

ccm_sitemapSelectPermissionsEntity = function(selector, id, name) {
	var html = $('#ccm-sitemap-permissions-entity-base').html();
	$('#ccm-sitemap-permissions-entities-wrapper').append('<div class="ccm-sitemap-permissions-entity">' + html + '<\/div>');
	var p = $('.ccm-sitemap-permissions-entity');
	var ap = p[p.length - 1];
	$(ap).find('h2 span').html(name);
	$(ap).find('input[type=hidden]').val(selector + '_' + id);
	$(ap).find('input[type=radio]').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id);
	});
	$(ap).find('div.ccm-file-access-extensions input[type=checkbox]').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id + '[]');
	});
	
	ccm_sitemapActivatePermissionsSelector();	
}

ccm_sitemapActivatePermissionsSelector = function() {
	$("tr.ccm-file-access-add input").unbind();
	$("tr.ccm-file-access-add input").click(function() {
		var p = $(this).parents('div.ccm-sitemap-permissions-entity')[0];
	});
	$("tr.ccm-file-access-file-manager input").click(function() {
		var p = $(this).parents('div.ccm-sitemap-permissions-entity')[0];
		if ($(this).val() != ccmi18n_filemanager.PTYPE_NONE) {
			$(p).find('tr.ccm-file-access-add').show();				
			$(p).find('tr.ccm-file-access-edit').show();				
			$(p).find('tr.ccm-file-access-admin').show();
			//$(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			$(p).find('tr.ccm-file-access-add').hide();				
			$(p).find('tr.ccm-file-access-edit').hide();				
			$(p).find('tr.ccm-file-access-admin').hide();				
			$(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});


	$("a.ccm-sitemap-permissions-remove").click(function() {
		$(this).parent().parent().fadeOut(100, function() {
			$(this).remove();
		});
	});
}


</script>

<h1><span><?=t('Sitemap Permissions')?></span></h1>
<div class="ccm-dashboard-inner">
	<form method="post" id="sitemap-permissions" action="<?=$this->url('/dashboard/sitemap/access', 'save_global_permissions')?>">
		<?=$validation_token->output('sitemap_permissions');?>

		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Choose User/Group')?>"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em><?=t('Add Group or User')?></em></span></a>

		<p>
		<?=t('Add users or groups to determine access to the file manager. <strong>Note:</strong> If you want users to have access to the dashboard sitemap, they must be entered here and in the dashboard sitemap page permissions area.');?>
		</p>
		
		<div class="ccm-spacer">&nbsp;</div><br/>
		
			<div id="ccm-sitemap-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">			
			<div id="ccm-sitemap-permissions-entity-base" class="ccm-permissions-entity-base">
		
			<? print $this->controller->getAccessRow('GLOBAL'); ?>
			
			
		</div>
		
		
		<? 
	
		$gArray = $gl->getGroupList();
		
		foreach($gArray as $g) { ?>
			
			<? print $this->controller->getAccessRow('GLOBAL', 'gID_' . $g->getGroupID(), $g->getGroupName(), $g->canRead()); ?>
		
		<? } ?>
		<? foreach($uArray as $ui) { ?>
			
			<? print $this->controller->getAccessRow('GLOBAL', 'uID_' . $ui->getUserID(), $ui->getUserName(), $ui->canRead()); ?>
		
		<? } ?>
		</div>
		
		
		<div class="ccm-spacer">&nbsp;</div>
		
		
		<? print $ih->submit(t('Save'), 'sitemap-permissions'); ?>

		<div class="ccm-spacer">&nbsp;</div>
	</form>
</div>
</div>
