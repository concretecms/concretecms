<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<?=$h->getDashboardPaneHeaderWrapper(t('Tracking Codes'));?>
<form id="tracking-code-form" class="form-stacked" action="<?=$this->action('')?>" method="post">
	<?=$this->controller->token->output('update_tracking_code')?>
	<?php if (!empty($token_error) && is_array($token_error)) { ?>
	<div class="alert-message error"><?=$token_error[0]?></div>
	<?php } ?>
	<div class="clearfix">
		<?=$form->label('tracking_code', t('Tracking Codes'))?>
		<div class="input">
			<?=$form->textarea('tracking_code', $tracking_code, array('class' => 'xxlarge', 'rows' => 4, 'cols' => 50))?>
			<span class="help-block"><?=t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.')?></span>
		</div>
	</div>
	<div class="clearfix">
		<?=$form->label('tracking_code_position', t('Position'))?>
		<div class="input">
			<ul class="inputs-list">
				<li>
					<label>
						<?=$form->radio('tracking_code_position', 'top', $tracking_code_position)?>
						<span><?=t('Header of the page')?></span>
					</label>
				</li>
				<li>
					<label>
						<?=$form->radio('tracking_code_position', 'bottom', $tracking_code_position)?>
						<span><?=t('Footer of the page')?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>
	
		
	<?
	$submit = $ih->submit( t('Save'), 'tracking-code-form', 'right', 'primary');
	$cancel = $ih->button( t('Cancel'), $this->url('/dashboard'), 'left');
	print $ih->buttons($cancel, $submit);
	?> 
</form>
<?=$h->getDashboardPaneFooterWrapper();?>