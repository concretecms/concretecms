<?php defined('C5_EXECUTE') or die("Access Denied.");

$form = Loader::helper('form');

$searchFields = $controller->getSearchFields();

if (Config::get('concrete.permissions.model') == 'advanced') {
    $searchFields['group_set'] = t('Group Set');
}

$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$dk = PermissionKey::getByHandle('delete_user');

$flr = new \Concrete\Core\Search\StickyRequest('users');
$searchRequest = $flr->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="users" action="<?php echo URL::to('/ccm/system/search/users/submit')?>" class="ccm-search-fields">
	<div class="ccm-search-fields-row form-inline">
	<div class="form-group">
		<select data-bulk-action="users" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?php echo t('Items Selected')?></option>
			<?php if ($ek->validate()) { ?>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Edit Properties')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Edit Properties')?></option>
			<?php } ?>
			<?php /*
			<?php if ($ik->validate()) { ?>
				<option value="activate"><?=t('Activate')?></option>
				<option value="deactivate"><?=t('Deactivate')?></option>
			<?php } ?>
			<option value="group_add"><?=t('Add to Group')?></option>
			<option value="group_remove"><?=t('Remove from Group')?></option>
			<?php if ($dk->validate()) { ?>
			<option value="delete"><?=t('Delete')?></option>
			<?php } ?>
 */ ?>
			<?php if ($mode == 'choose_multiple') { ?>
				<option value="choose"><?php echo t('Choose')?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="fa fa-search"></i>
			<?php echo $form->search('keywords', $searchRequest['keywords'], array('placeholder' => t('Username or Email')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?php echo t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="#" data-search-toggle="advanced"><?php echo t('Advanced Search')?></a>
		<li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?php echo URL::to('/ccm/system/dialogs/user/search/customize')?>"><?php echo t('Customize Results')?></a>
	</ul>
	</div>
	<div class="ccm-search-fields-row">
		<div class="form-group form-group-full">
			<?php echo $form->label('gID', t('In Group'))?>
			<?php
            $gl = new GroupList();
            $g1 = $gl->getResults();
            ?>
			<div class="ccm-search-field-content">
			<select multiple name="gID[]" class="select2-select" style="width: 360px">
				<?php foreach ($g1 as $g) {
                    $gp = new Permissions($g);
                    if ($gp->canSearchUsersInGroup($g)) {
                        ?>
					<option value="<?php echo $g->getGroupID()?>"  <?php if (is_array($searchRequest['gID']) && in_array($g->getGroupID(), $searchRequest['gID'])) { ?> selected="selected" <?php } ?>><?php echo $g->getGroupDisplayName()?></option>
				<?php
                    }
                } ?>
			</select>
			</div>
		</div>
	</div>
	<div class="ccm-search-fields-advanced"></div>
	<div class="ccm-search-fields-row ccm-search-fields-submit">
		<div class="form-group form-group-full">
			<label class="control-label"><?=t('Per Page')?></label>
			<div class="ccm-search-field-content ccm-search-field-content-select2">
				<?=$form->select('numResults', array(10 => t('10'), 20 => t('20'), 50 => t('50'), 100 => t('100'), 250 => t('250'), 500 => t('500'), 1000 => t('1000'))); ?>
			</div>
		</div>
		<button type="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
	</div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
	<select name="field[]" class="ccm-search-choose-field" data-search-field="users">
		<option value=""><?php echo t('Choose Field')?></option>
		<?php foreach ($searchFields as $key => $value) { ?>
			<option value="<?php echo $key?>" <% if (typeof(field) != 'undefined' && field.field == '<?php echo $key?>') { %>selected<% } %> data-search-field-url="<?php echo URL::to('/ccm/system/search/users/field', $key)?>"><?php echo $value?></option>
		<?php } ?>
	</select>

	<div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
	<a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
</div>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (user) {%>
<tr>
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-user-id="<%-user.uID%>" data-user-name="<%-user.uName%>" data-user-email="<%-user.uEmail%>" data-search-checkbox="individual" value="<%-user.uID%>" /></span></td>
	<% for (i = 0; i < user.columns.length; i++) {
		var column = user.columns[i];
		%>
		<td><%= column.value %></td>
	<% } %>
</tr>
<% }); %>
</script>

<?php Loader::element('search/template')?>
