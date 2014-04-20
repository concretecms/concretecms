<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $ih = Loader::helper('concrete/ui'); ?>

    <form method="post" class="form-horizontal" action="<?=$view->action('submit')?>">
		<div class="container">
		<fieldset>
    		<legend><?=t('Basic Details')?></legend>
			
			<div class="row">
				<div class="form-group">
					<label for="uName" class="control-label col-sm-2"><?=t('Username')?></label>
					<div class="col-sm-8">
						<div class="input-group">
						<?=$form->text('uName', array('autocomplete' => 'off'))?>
						<span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="uPassword" class="control-label col-sm-2"><?=t('Password')?></label>
					<div class="col-sm-8">
						<div class="input-group">
						<?=$form->password('uPassword')?>
						<span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="uEmail" class="control-label col-sm-2"><?=t('Email Address')?></label>
					<div class="col-sm-8">
						<div class="input-group">
						<?=$form->email('uEmail')?>
						<span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
						</div>
					</div>
				</div>
			</div>

			<? if (count($locales)) { // "> 1" because en_US is always available ?>
		
			<div class="row">
				<div class="form-group">
					<label for="uEmail" class="control-label col-sm-2"><?=t('Language')?></label>
					<div class="col-sm-8">
					<? print $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
					</div>
				</div>
			</div>


			<? } ?>



    	</fieldset>
	</div>

	<?=$token->output('submit');?>

	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<a href="<?=View::url('/dashboard/users/search')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
		<?=Loader::helper("form")->submit('add', t('Add'), array('class' => 'btn btn-primary pull-right'))?>
	</div>
	</div>
    </form>
