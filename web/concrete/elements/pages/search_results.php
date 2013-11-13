<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<script type="text/template" data-template="search-results-table-head">
<tr>
	<th><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="select-all" /></span></th>
	<% 
	for (i = 0; i < columns.length; i++) {
		var column = columns[i];
		if (column.isColumnSortable) { %>
			<th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
		<% } else { %>
			<th><span><%=column.title%></span></th>
		<% } %>
	<% } %>
</tr>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function(page) {%>
<tr>
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="individual" value="<%=page.cID%>" /></span></td>
	<% if (page.isIndexedSearch) { %>
		<td><%=page.score%></td>
	<% } %>
	<% for(i = 0; i < page.columns.length; i++) {
		var column = page.columns[i];
		if (column.key == 'cvName') { %>
			<td class="ccm-search-results-name"><%=page.title%></td>
		<% } else { %>
			<td><%=column.value%></td>
		<% } %>
	<% } %>
</tr>
<% }); %>
</script>

<script type="text/template" data-template="search-results-pagination">
<ul class="pagination">
	<li class="<%=pagination.prevClass%>"><%=pagination.previousPage%></li>
	<%=pagination.pages%>
	<li class="<%=pagination.nextClass%>"><%=pagination.nextPage%></li>
</div>
</script>

<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
<thead>
</thead>
<tbody>
</tbody>
</table>

<div class="ccm-search-results-pagination"></div>