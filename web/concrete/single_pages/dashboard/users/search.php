<? if (is_object($user)) { ?>

<style type="text/css">
div[data-container=editable-fields] section {
	margin-bottom: 30px;
	position: relative;
	border-bottom: 1px solid #f1f1f1;
	padding-bottom: 30px;
}

div[data-container=editable-fields] section .group-header {
	position: relative;
	font-weight: bold;
}

</style>

<div class="container" data-container="editable-fields">

<section>
	<div class="row">
		<div class="col-md-6">
		<h4><?=t('Basic Details')?></h4>
		<div class="row">
			<div class="col-md-4"><p><?=t('Username')?></p></div>
			<div class="col-md-8"><p><strong <? if ($canEditUserName) { ?>data-editable-field-type="xeditable" data-url="<?=$view->action('update_username', $user->getUserID())?>" data-type="text" data-name="uName" <? } ?>><?=$user->getUserName()?></strong></p></div>
		</div>
		<div class="row">
			<div class="col-md-4"><p><?=t('Email Address')?></p></div>
			<div class="col-md-8"><p><strong <? if ($canEditEmail) { ?>data-editable-field-type="xeditable" data-url="<?=$view->action('update_email', $user->getUserID())?>"data-type="email" data-name="uEmail"<? } ?>><?=$user->getUserEmail()?></strong></p></div>
		</div>
		<div class="row">
			<div class="col-md-4"><p><?=t('Password')?></p></div>
			<div class="col-md-8"><p><? if ($canEditPassword) { ?><a href="#" class="btn btn-xs btn-default" data-button="change-password"><?=t('Change')?></a><? } else { ?>*********<? } ?></p></div>
		</div>
		<div class="row">
			<div class="col-md-4"><p><?=t('Profile Picture')?></p></div>
			<div class="col-md-8"><p>
				<div <? if ($canEditAvatar) { ?>data-editable-field-type="image" data-editable-field-inline-commands="true" data-url="<?=$this->action('update_avatar', $user->getUserID())?>"<? } ?>>
					<ul class="ccm-edit-mode-inline-commands">
						<li><a href="#" data-editable-field-command="clear"><i class="glyphicon glyphicon-trash"></i></a></li>
					</ul>
	                <span class="editable-image-wrapper">
	                    <input type="file" id="file-avatar" name="avatar" />
	                    <div class="editable-image-display"><?=Loader::helper('concrete/avatar')->outputUserAvatar($user)?></div>
					</span>
				</div>
			</p>
			</div>
		</div>
		</div>

		<div class="col-md-6">
			<h4><?=t('Groups')?></h4>
			<div class="row group-header">
				<div class="col-md-6"><p><?=t('Group')?></p></div>
				<div class="col-md-6"><p><?=t('Date Entered')?></p></div>
			</div>

			<div data-container="group-list"></div>

			<?
			$p = new Permissions();
			if ($p->canAccessGroupSearch()) { ?>
			<hr>
				<a class="btn btn-default btn-xs" data-button="assign-groups" dialog-width="640" dialog-height="480" dialog-modal="true" href="<?=URL::to('/system/dialogs/group/search')?>?filter=assign" dialog-title="<?=t('Add Groups')?>" dialog-modal="false"><?=t('Add Group')?></a>
			<? } ?>
		</div>

	</div>
</section>

<section>
	<h4><?=t('Other Attributes')?></h4>
	<? Loader::element('attribute/editable_list', array(
		'attributes' => $attributes, 
		'object' => $user,
		'formAction' => $view->action('get_attribute_form', $user->getUserID()),
		'action' => $view->action('update_attribute', $user->getUserID())
	));?>
</section>

</div>

<? if ($canEditPassword) { ?>
	
	<div style="display: none">
		<div data-dialog="change-password" class="ccm-ui">
			<form data-dialog-form="change-password" action="<?=$view->action('change_password', $user->getUserID())?>">
				<?=Loader::helper('validation/token')->output('change_password')?>

				<div class="form-group">
					<?=$form->label('uPassword', t('Password'))?>
					<?=$form->password('uPassword')?>
				</div>

				<div class="form-group">
					<?=$form->label('uPasswordConfirm', t('Confirm Password'))?>
					<?=$form->password('uPasswordConfirm')?>
				</div>
				
				<div class="dialog-buttons">
				<button class="btn pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
				<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Update')?></button>
				</div>
				

			</form>
		</div>
	</div>
<? } ?>

<script type="text/template" data-template="user-add-groups">
<% _.each(groups, function(group) { %>
	<div class="row" data-editable-field-inline-commands="true" data-group-id="<%=group.gID%>">
		<ul class="ccm-edit-mode-inline-commands">
			<li><a href="#" data-group-id="<%=group.gID%>" data-button="delete-group"><i class="glyphicon glyphicon-trash"></i></a></li>
		</ul>
		<div class="col-md-6"><p><%=group.gDisplayName%></p></div>
		<div class="col-md-6"><p><%=group.gDateTimeEntered%></p></div>
	</div>
<% }); %>
</script>

<script type="text/javascript">
$(function() {

	var _addGroupsTemplate = _.template($('script[data-template=user-add-groups]').html());
	$('div[data-container=group-list]').append(
		_addGroupsTemplate({'groups': <?=$groupsJSON?>})
	);
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		url: '<?=$this->action('save', $user->getUserID())?>',
		data: {
			ccm_token: '<?=Loader::helper('validation/token')->generate()?>'
		}
	});

	ConcreteEvent.subscribe('SelectGroup', function(e) {
		$.concreteAjax({
			url: "<?=URL::to('/system/user/add_group')?>",
			data: {
				gID: e.eventData.gID,
				uID: '<?=$user->getUserID()?>'
			},
			success: function(r) {
				ConcreteAlert.hud('<?=t('Group added successfully.')?>');
				$('div[data-container=group-list]').append(
					_addGroupsTemplate({'groups': r.groups})
				);
				_.each(r.groups, function(group) {
					$('div[data-container=group-list] div[data-group-id=' + group.gID + ']').addClass('animated bounceIn');
				});
				jQuery.fn.dialog.closeTop();
			}
		});
	});

	$('div[data-container=editable-fields]').on('click', 'a[data-button=change-password]', function() {
		$.fn.dialog.open({
			element: 'div[data-dialog=change-password]',
			title: '<?=t('Change Password')?>',
			width: '280',
			height: '220',
			modal: true
		});
		return false;
	});

	$('div[data-container=editable-fields]').on('click', 'a[data-button=delete-group]', function() {
		$.concreteAjax({
			url: "<?=URL::to('/system/user/remove_group')?>",
			data: {
				gID: $(this).attr('data-group-id'),
				uID: '<?=$user->getUserID()?>'
			},
			success: function(r) {
				ConcreteAlert.hud('<?=t('Group removed successfully.')?>');
				$('div[data-container=group-list] div[data-group-id=' + r.group.gID + ']').queue(function() {
					$(this).addClass('animated bounceOutLeft');
					$(this).dequeue();
				}).delay(500).queue(function() {
					$(this).remove();
					$(this).dequeue();
				})
				jQuery.fn.dialog.closeTop();
			}
		});
		return false;
	});

	$('a[data-button=assign-groups]').dialog();

});
</script>
<? } else {
	
	$tp = Loader::helper('concrete/user');
	if ($tp->canAccessUserSearchInterface()) { ?>
		
		<div class="ccm-dashboard-content-full" data-search="users">
		<? Loader::element('users/search', array('controller' => $searchController))?>
		</div>


	<? } else { ?>
	<div class="ccm-pane-body">
		<p><?=t('You do not have access to user search. This setting may be changed in the access section of the dashboard settings page.')?></p>
	</div>	

	<? } ?>

<? } ?>