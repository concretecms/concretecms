<?php defined('C5_EXECUTE') or die("Access Denied.");

$form = Loader::helper('form');

?>

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
