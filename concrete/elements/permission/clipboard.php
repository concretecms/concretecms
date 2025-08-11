<?php
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Validation\CSRF\Token;

defined('C5_EXECUTE') or die("Access Denied.");

$set = \Concrete\Core\Permission\Set::getSavedPermissionSetFromSession();

$uid = uniqid();

$resolverManager = app(ResolverManagerInterface::class);
$valt = app(Token::class);
?>
<button class="btn btn-sm btn-secondary" type="button" id="ccm-permissions-list-copy-permissions-<?= $uid ?>"><?=t('Copy')?></button>
<?php if (is_object($set) && $set->getPermissionKeyCategory() == $pkCategory->getPermissionKeyCategoryHandle()) {
    ?>
	<button class="btn btn-sm btn-secondary" type="button" id="ccm-permissions-list-paste-permissions-<?= $uid ?>"><?=t('Paste')?></button>
<?php
} ?>
<input type="hidden" name="pkCategoryHandle" value="<?=$pkCategory->getPermissionKeyCategoryHandle()?>" />
<script type="text/javascript">

$(function() {
	$('#ccm-permissions-list-copy-permissions-<?= $uid ?>').click(function() {
		var frm = $(this).closest('.ccm-permission-grid');

		jQuery.fn.dialog.showLoader();
		var data = '';
		frm.find('.ccm-permission-access-line input[type=hidden]').each(function() {
			data += $(this).attr('name') + '=' + $(this).val() + '&';
		});
		data += 'pkCategoryHandle=' + frm.find('input[name=pkCategoryHandle]').val();
		$.ajax({
			dataType: 'json',
			type: 'post',
			data: data,
			url: <?= json_encode($resolverManager->resolve(['/ccm/system/permissions/set/copy']) . '?' . $valt->getParameter('copy_permission_set')) ?>,
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
			}
		})
	})

	$('#ccm-permissions-list-paste-permissions-<?= $uid ?>').click(function() {
		jQuery.fn.dialog.showLoader();
		var frm = $(this).closest('.ccm-permission-grid');
		var data = 'pkCategoryHandle=' + frm.find('input[name=pkCategoryHandle]').val();
		$.ajax({
			dataType: 'json',
			type: 'post',
			data: data,
			url: <?= json_encode($resolverManager->resolve(['/ccm/system/permissions/set/paste']) . '?' . $valt->getParameter('paste_permission_set')) ?>,
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				for (i = 0; i < r.length; i++) {
					var cell = r[i];
					$('#ccm-permission-grid-cell-' + cell.pkID).html(cell.html);
					$('#ccm-permission-grid-name-' + cell.pkID + ' a').attr('data-paID', cell.paID);		
				}

			}				
		})
	})

})
</script>
