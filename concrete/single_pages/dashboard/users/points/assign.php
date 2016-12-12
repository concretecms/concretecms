<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Assign Community Points'), false, false, false, array(), Page::getByPath('/dashboard/users/points', 'ACTIVE'))?>
<form method="post" action="<?=$view->action('save')?>" id="ccm-community-point-entry">
	<?php
    \Core::make('helper/validation/token')->output('add_community_points');
    if (isset($upID) && $upID > 0) {
        echo $form->hidden('upID', $upID);
    }
    ?>
	<div class="form-group">
	    <?=$form->label('upUser', t('User'))?>
		<div class="input">
			<?php echo $form_user_selector->quickSelect('upUser', $upUser, ['autofocus' => 'autofocus']);?>
		</div>
	</div>
	
	<div class="form-group">
	    <?=$form->label('upaID', t('Action'))?>
		<div class="input">
			<?php echo $form->select('upaID', $userPointActions, $upaID, array('json-src' => $view->action('getJsonDefaultPointAction'))); ?>
		</div>
	</div>
	
	<div class="form-group">
	    <?=$form->label('upPoints', t('Points'))?>
		<div class="input">
			<?php echo $form->number('upPoints', $upPoints);?>
		</div>
	</div>
	
	<div class="form-group">
	    <?=$form->label('upComments', t('Comments'))?>
		<div class="input">
			<?php echo $form->textarea('upComments', $upComments);?>
		</div>
	</div>
	
	<div class="form-group">
	    <?=$form->label('dtoverride', t('Override Timestamp'))?>
		<div class="input">
		    <div class="checkbox">
			    <?php echo $form_date_time->datetime('dtoverride', $timestamp, true);?>
            </div>
		</div>
	</div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/users/points')?>" class="btn btn-default pull-left"><?=t('Back to List')?></a>
            <button type="submit" class="btn btn-primary pull-right"><?=t('Assign')?> <i class="icon-white icon-ok"></i></button>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function() {
	
	$('#upaID').change(function() {
		var src = $('#upaID').attr('json-src')+'/-/'+$('#upaID').val();
		$.getJSON(src,function(j) {
			$('#upPoints').val(j);
		});
	});

	
});
</script>