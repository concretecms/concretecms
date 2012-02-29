<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (isset($_REQUEST['message'])) { ?>

<div class="alert-message success" id="ccm-permissions-message-list">
<?
if ($_REQUEST['message'] == 'entity_removed') { ?>
	<?=t('User/Group Removed')?>
<? } else if ($_REQUEST['message'] == 'entity_added') { ?>
	<?=t('User/Group Added')?>
<? } ?>
</div>

<? } ?>

<script type="text/javascript">
$(function() {
	$("#ccm-permissions-message-list").show('highlight', {'color': '#fff'}, function() {
		setTimeout(function() {
			$("#ccm-permissions-message-list").fadeOut(300, 'easeInExpo');
		}, 1200);
	});
});
</script>

<div class="alert-message notice block-message">
	<p><?=t('Note: Permission changes happen in realtime. They are not versioned, and they are applied immediately.')?></p>
</div>