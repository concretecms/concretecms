<?php defined('C5_EXECUTE') or die("Access Denied.");

$form = Loader::helper('form');

$searchFields = array(
    'date_added' => t('Registered Between'),
    'is_active' => t('Activated Users')
);

if (Config::get('concrete.permissions.model') == 'advanced') {
    $searchFields['group_set'] = t('Group Set');
}

$searchFieldAttributes = UserAttributeKey::getSearchableList();
foreach ($searchFieldAttributes as $ak) {
    $searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}

$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$dk = PermissionKey::getByHandle('delete_user');

$flr = new \Concrete\Core\Search\StickyRequest('users');
$searchRequest = $flr->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="users" action="<?=URL::to('/ccm/system/search/users/submit')?>" class="form-inline ccm-search-fields">
	<div class="ccm-search-fields-row">
	<div class="form-group">
		<select data-bulk-action="users" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?=t('Items Selected')?></option>
			<?php if ($ek->validate()) { ?>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Edit Properties')?>" data-bulk-action-url="<?=URL::to('/ccm/system/dialogs/user/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?=t('Edit Properties')?></option>
			<?php } ?>
			<? /*
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
				<option value="choose"><?=t('Choose')?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="fa fa-search"></i>
			<?=$form->search('keywords', $searchRequest['keywords'], array('placeholder' => t('Username or Email')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="#" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
		<li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?=URL::to('/ccm/system/dialogs/user/search/customize')?>"><?=t('Customize Results')?></a>
	</ul>
	</div>
	<div class="ccm-search-fields-row">
		<div class="form-group form-group-full">
			<?=$form->label('gID', t('In Group'))?>
			<?php
            $gl = new GroupList();
            $g1 = $gl->getResults();
            ?>
			<div class="ccm-search-field-content">
			<select multiple name="gID[]" class="select2-select" style="width: 100%">
				<?php foreach ($g1 as $g) {
                    $gp = new Permissions($g);
                    if ($gp->canSearchUsersInGroup($g)) {
                        ?>
					<option value="<?=$g->getGroupID()?>"  <?php if (is_array($searchRequest['gID']) && in_array($g->getGroupID(), $searchRequest['gID'])) { ?> selected="selected" <?php } ?>><?=$g->getGroupDisplayName()?></option>
				<?php
                    }
                } ?>
			</select>
			</div>
		</div>
	</div>
	<div class="ccm-search-fields-advanced"></div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
	<select name="field[]" class="ccm-search-choose-field" data-search-field="users">
		<option value=""><?=t('Choose Field')?></option>
		<?php foreach ($searchFields as $key => $value) { ?>
			<option value="<?=$key?>" <% if (typeof(field) != 'undefined' && field.field == '<?=$key?>') { %>selected<% } %> data-search-field-url="<?=URL::to('/ccm/system/search/users/field', $key)?>"><?=$value?></option>
		<?php } ?>
	</select>
	<div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
	<a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
</div>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (user) {%>
<tr>
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-user-id="<%=user.uID%>" data-user-name="<%=user.uName%>" data-user-email="<%=user.uEmail%>" data-search-checkbox="individual" value="<%=user.uID%>" /></span></td>
	<% for (i = 0; i < user.columns.length; i++) {
		var column = user.columns[i];
		%>
		<td><%=column.value%></td>
	<% } %>
</tr>
<% }); %>
</script>

<?php Loader::element('search/template')?>
