<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<?=$h->getDashboardPaneHeaderWrapper(t('Site Access'), false, false, false);?>
<form id="site-permissions-form" action="<?=$this->action('')?>" method="post">
	<?=$this->controller->token->output('site_permissions_code')?>

<? if(PERMISSIONS_MODEL != 'simple'):?>
<div class="ccm-pane-body ccm-pane-body-footer">
<p>
<?=t('Your concrete5 site does not use the simple permissions model. You must change your permissions for each specific page and content area.')?>
</p>
</div>
<?else:?>
<div class="ccm-pane-body">
	<div class="clearfix">
		<?=$form->label('view', t('Viewing Permissions'))?>
		<div class="input">
			<ul class="inputs-list">
				<li>
					<label>
						<?=$form->radio('view', 'ANYONE', $guestCanRead)?>
						<span><?=t('Public')?> - <?=t('Anyone may view the website.')?></span>
					</label>
				</li>
				<li>
					<label>
						<?=$form->radio('view', 'USERS', $registeredCanRead)?>
						<span><?=t('Members')?> - <?=t('Only registered users may view the website.')?></span>
					</label>
				</li>
				<li>
					<label>
						<?=$form->radio('view', 'PRIVATE', !$guestCanRead && !$registeredCanRead)?>
						<span><?=t('Private')?> - <?=t('Only the administrative group may view the website.')?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>
	<div class="clearfix">
		<?=$form->label('gID', t('Edit Access'))?>
		<div class="input">
			<ul class="inputs-list">
				<?foreach($gArray as $g):?>
				<li>
					<label>
						<?=$form->checkbox('gID[]', $g->getGroupID(), in_array($g->getGroupID(), $editAccess))?>
						<span><?=$g->getGroupName()?></span>
					</label>
				</li>
				<?endforeach?>
			</ul>
			<span class="help-block"><?=t('Choose which users and groups may edit your site. Note: These settings can be overridden on specific pages.')?></span>
		</div>
	</div>
</div>
<div class="ccm-pane-footer">
<?
	$submit = $ih->submit( t('Save'), 'site-permissions-form', 'right', 'primary');
	print $submit;
?>
</div>

<?endif?>
</div>
</form>
<?=$h->getDashboardPaneFooterWrapper(false);?>
