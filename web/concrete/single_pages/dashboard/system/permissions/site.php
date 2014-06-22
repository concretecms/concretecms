<?php defined('C5_EXECUTE') or die("Access Denied.");

/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/ui');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<?=$h->getDashboardPaneHeaderWrapper(t('Site Access'), false, false, false);?>
<form id="site-permissions-form" action="<?=$view->action('')?>" method="post" role="form">
	<?php echo $this->controller->token->output('update_maintenance')?>
	
    <? if(PERMISSIONS_MODEL != 'simple'):?>
    <div>
        <p>
            <?=t('Your concrete5 site does not use the simple permissions model. You must change your permissions for each specific page and content area.')?>
        </p>
    </div>
    <?else:?>
    
    <fieldset>
	<legend style="margin-bottom: 0px"><?=t('Viewing Permissions')?></legend>
	<div class="form-group">
        <label class="radio">
		    <?=$form->radio('view', 'ANYONE', $guestCanRead)?> 
		    <span><?=t('Public')?> - <?=t('Anyone may view the website.')?></span>
        </label>
		 
        <label class="radio">
            <?=$form->radio('view', 'USERS', $registeredCanRead)?> 
            <span><?=t('Members')?> - <?=t('Only registered users may view the website.')?></span>
        </label>

		<label class="radio">
			<?=$form->radio('view', 'PRIVATE', !$guestCanRead && !$registeredCanRead)?>
			<span><?=t('Private')?> - <?=t('Only the administrative group may view the website.')?></span>
		</label>
    </div>
    </fieldset>
    
    <fieldset>
    <legend style="margin-bottom: 0px"><?=t('Edit Access')?></legend>
        <span class="help-block"><?=t('Choose which users and groups may edit your site. Note: These settings can be overridden on specific pages.')?></span>
        <div class="form-group">
			<ul class="checkbox">
				<?foreach($gArray as $g):?>
				<li>
					<label>
						<?=$form->checkbox('gID[]', $g->getGroupID(), in_array($g->getGroupID(), $editAccess))?>
						<span><?=$g->getGroupDisplayName()?></span>
					</label>
				</li>
				<?endforeach?>
			</ul>
        </div>
    </fieldset>
    
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
        </div>
    </div>

<?endif?>
</form>
<?=$h->getDashboardPaneFooterWrapper(false);?>
