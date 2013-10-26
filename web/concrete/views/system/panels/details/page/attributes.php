<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/template" class="attribute">
	<div class="form-group <% if (pending) { %>form-group-pending<% } %>" data-attribute-key-id="<%=akID%>">
		<a href="#"><i class="glyphicon glyphicon-minus-sign"></i></a>
		<label class="control-label"><%=label%></label>
		<div>
			<%=content%>
		</div>
	</div>
</script>


<section class="ccm-ui">
	<header><?=t('Attributes')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-panel-detail-form="attributes">

		<?=Loader::helper('concrete/interface/help')->notify('panel', '/page/attributes')?>
		<? if ($assignment->allowEditName()) { ?>
		<div class="form-group">
			<label for="cName" class="control-label"><?=t('Name')?></label>
			<div>
			<input type="text" class="form-control" id="cName" name="cName" value="<?=htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" />
			</div>
		</div>
		<? } ?>

		<? if ($assignment->allowEditDateTime()) { ?>
		<div class="form-group">
			<label for="cName" class="control-label"><?=t('Created Time')?></label>
			<div>
				<? print $dt->datetime('cDatePublic', $c->getCollectionDatePublic(null, 'user')); ?>
			</div>
		</div>
		<? } ?>
		
		<? if ($assignment->allowEditUserID()) { ?>
		<div class="form-group">
			<label for="cName" class="control-label"><?=t('Author')?></label>
			<div>
			<? 
			print $uh->selectUser('uID', $c->getCollectionUserID());
			?>
			</div>
		</div>
		<? } ?>
		

		<? if ($assignment->allowEditDescription()) { ?>
		<div class="form-group">
			<label for="cDescription" class="control-label"><?=t('Description')?></label>
			<div>
				<textarea id="cDescription" name="cDescription" class="form-control" rows="8"><?=$c->getCollectionDescription()?></textarea>
			</div>
		</div>
		<? } ?>

	</form>
	<div class="ccm-panel-detail-form-actions">
		<button class="pull-left btn btn-default" type="button" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>

</section>

<script type="text/javascript">

var renderAttribute = _.template(
    $('script.attribute').html()
);

var $form = $('form[data-panel-detail-form=attributes]');

CCMPageAttributeDetail = {

	addAttributeKey: function(akID) {
		jQuery.fn.dialog.showLoader();
		$.ajax({
			url: '<?=$controller->action("add_attribute")?>',
			dataType: 'json',
			data: {
				'akID': akID
			},
			type: 'post',
			success: function(r) {
				$form.append(
					renderAttribute(r)
				);
				$form.delay(1).queue(function() {
					$('[data-attribute-key-id=' + r.akID + ']').removeClass('form-group-pending');
					$(this).dequeue();
				});
			},
			complete: function() {
				jQuery.fn.dialog.hideLoader();
			}
		});
	}

}