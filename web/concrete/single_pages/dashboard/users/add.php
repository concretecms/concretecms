<?
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('text');

Loader::model('attribute/categories/user');
$attribs = UserAttributeKey::getRegistrationList();
$assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();

Loader::model("search/group");
$gl = new GroupSearch();
$gl->setItemsPerPage(10000);
$gArray = $gl->getPage();

$languages = Localization::getAvailableInterfaceLanguages();

?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add User'), false, false, false);?>

<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?=$this->url('/dashboard/users/add')?>">
	<?=$valt->output('create_account')?>
	
	<input type="hidden" name="_disableLogin" value="1">

	<div class="ccm-pane-body">
	
    	<table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2"><?=t('User Information')?></th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                    <td><?=t('Username')?> <span class="required">*</span></td>
                    <td><?=t('Password')?> <span class="required">*</span></td>
                </tr>
                <tr>
					<td><input type="text" name="uName" autocomplete="off" value="<?=$th->entities($_POST['uName'])?>" style="width: 95%"></td>
					<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 95%"></td>
				</tr>
                <tr>
                    <td><?=t('Email Address')?> <span class="required">*</span></td>
                    <td><? if ($assignment->allowEditAvatar()) { ?><?=t('User Avatar')?><? } ?></td>
                </tr>
                <tr>
					<td><input type="text" name="uEmail" autocomplete="off" value="<?=$th->entities($_POST['uEmail'])?>" style="width: 95%"></td>
					<td><? if ($assignment->allowEditAvatar()) { ?><input type="file" name="uAvatar" style="width: 95%"/><? } ?></td>
				</tr>
                
                
				<? if (count($languages) > 0) { ?>
			
				<tr>
					<td colspan="2"><?=t('Language')?></td>
				</tr>	
				<tr>
					<td colspan="2">
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
                
			</tbody>
		</table>

	<? if (count($attribs) > 0) { ?>
	
        <table class="table table-striped">
        	<thead>
	        	<tr>
            		<th><?=t('Registration Data')?></th>
	        	</tr>
			</thead>
            <tbody>
            
			<? foreach($attribs as $ak) { 
				if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { 
				?>
                <tr>
                    <td class="clearfix">
                    	<label><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?> <? if ($ak->isAttributeKeyRequiredOnRegister()) { ?><span class="required">*</span><? } ?></label>
                        <? $ak->render('form', $caValue, false)?>
                    </td>
                </tr>
                <? } ?>
            <? } // END Foreach ?>
        
			</tbody>
        </table>
	
	<? } ?>

		<table class="inputs-list table-striped table">
        	<thead>
				<tr>
					<th><?=t('Groups')?></th>
				</tr>
        	</thead>
            <tbody>
				<tr>
					<td>
                    
					<? 
					$gak = PermissionKey::getByHandle('assign_user_groups');
					foreach ($gArray as $g) { 
						if ($gak->validate($g['gID'])) {


						?>
						<label>
							<input type="checkbox" name="gID[]" value="<?=$g['gID']?>" <? 
                            if (is_array($_POST['gID'])) {
                                if (in_array($g['gID'], $_POST['gID'])) {
                                    echo(' checked ');
                                }
                            }
                        ?> />
							<span><?=$g['gName']?></span>
						</label>
                    <? }
                    
                    
                } ?>
			
					<div id="ccm-additional-groups"></div>
			
					</td>
				</tr>
			</tbody>
		</table>

	</div>

    <div class="ccm-pane-footer">
        <div class="ccm-buttons">
            <input type="hidden" name="create" value="1" />
            <? print $ih->submit(t('Add'), 'ccm-user-form', 'right', 'primary'); ?>
        </div>	
    </div>

</form>
    
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>