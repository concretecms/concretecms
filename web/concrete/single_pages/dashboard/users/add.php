<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Create Account'), t('Create new User accounts.'), false, false);?>
<div class="ccm-pane-body"> 
	
	<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?=$this->url('/dashboard/users/add')?>">
	<?=$valt->output('create_account')?>
	
	<input type="hidden" name="_disableLogin" value="1">

	<h2><?=t('Required Information')?></h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" width="50%"><?=t('Username')?> <span class="required">*</span></td>
		<td class="subheader" width="50%"><?=t('Password')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td><input type="text" name="uName" autocomplete="off" value="<?=$_POST['uName']?>" style="width: 95%"></td>
		<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 95%"></td>
	</tr>
	<tr>
		<td class="subheader"><?=t('Email Address')?> <span class="required">*</span></td>
		<td class="subheader"><?=t('User Avatar')?></td>
	</tr>	
	<tr>
		<td><input type="text" name="uEmail" autocomplete="off" value="<?=$_POST['uEmail']?>" style="width: 95%"></td>
		<td><input type="file" name="uAvatar" style="width: 95%"/></td>
	</tr>
	<?
	$languages = Localization::getAvailableInterfaceLanguages();
	if (count($languages) > 0) { ?>

	<tr>
		<td class="subheader" colspan="2"><?=t('Language')?></td>
	</tr>	
	<tr>
		<Td colspan="2">
		<?
			array_unshift($languages, 'en_US');
			$locales = array();
			$locales[''] = t('** Default');
			Loader::library('3rdparty/Zend/Locale');
			Loader::library('3rdparty/Zend/Locale/Data');
			Zend_Locale_Data::setCache(Cache::getLibrary());
			foreach($languages as $lang) {
				$loc = new Zend_Locale($lang);
				$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
			}
			print $form->select('uDefaultLanguage', $locales);
		?>
		</td>
	</tr>	
	<? } ?>

	</table>
	</div>

	<?
	Loader::model('attribute/categories/user');
	$attribs = UserAttributeKey::getRegistrationList();
	if (count($attribs) > 0) { ?>
	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?=t('Registration Data')?></td>
	</tr>
	<? foreach($attribs as $ak) { ?>
	<tr>
		<td class="subheader"><?=$ak->getAttributeKeyName()?> <? if ($ak->isAttributeKeyRequiredOnRegister()) { ?><span class="ccm-required">*</span><? } ?></td>
	</tr>
	<tr>
		<td width="100%"><? $ak->render('form', $caValue, false)?></td>
	</tr>
	<? } ?>
	</table>
	
	
	<? } ?>
<?
	Loader::model("search/group");
	$gl = new GroupSearch();
	if ($gl->getTotal() < 1000) { 
		$gl->setItemsPerPage(1000);
		?>
		<h2><?=t('Groups')?></h2>
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td class="header">
				<?=t('Groups')?>
			</td>
		</tr>
		<? 
		$gArray = $gl->getPage(); ?>
		<tr>
			<td>
			<? foreach ($gArray as $g) { ?>
				<input type="checkbox" name="gID[]" value="<?=$g['gID']?>" style="vertical-align: middle" <? 
					if (is_array($_POST['gID'])) {
						if (in_array($g['gID'], $_POST['gID'])) {
							echo(' checked ');
						}
					}
				?> /> <?=$g['gName']?><br>
			<? } ?>
			
			<div id="ccm-additional-groups"></div>
			
			</td>
		</tr>
		</table>
	<? } ?>	
</div>
<div class="ccm-pane-footer">
	<div class="ccm-buttons">
		<input type="hidden" name="create" value="1" />
		<? print $ih->submit(t('Create User'), 'ccm-user-form', 'right', 'primary'); ?>

	</div>	
</div>

	</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>