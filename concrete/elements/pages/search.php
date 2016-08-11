<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

?>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (page) {%>
<tr data-launch-search-menu="<%=page.cID%>" data-page-id="<%=page.cID%>" data-page-name="<%=page.cvName%>">
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="individual" value="<%=page.cID%>" /></span></td>
	<% for (i = 0; i < page.columns.length; i++) {
		var column = page.columns[i];
		if (column.key == 'cvName') { %>
			<td class="ccm-search-results-name"><%=column.value%></td>
		<% } else { %>
			<td><%=column.value%></td>
		<% } %>
	<% } %>
</tr>
<% }); %>
</script>

<?php Loader::element('search/template')?>
