<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$form = Loader::helper('form');
$searchRequest = $controller->getSearchRequest();
?>

<style type="text/css">
	div[data-search=groups].ccm-ui form.ccm-search-fields {
		margin-left: 0px;
	}
</style>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="groups" action="<?=URL::to('/system/search/groups/submit')?>" class="form-inline ccm-search-fields">
	<div class="ccm-search-fields-row">
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="glyphicon glyphicon-search"></i>
			<?=$form->search('keywords', $searchRequest['keywords'], array('placeholder' => t('Name')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	</div>
</form>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function(user) {%>
<tr>
	<% for(i = 0; i < user.columns.length; i++) {
		var column = user.columns[i]; 
		%>
		<td><%=column.value%></td>
	<% } %>
</tr>
<% }); %>
</script>

<div data-search-element="wrapper"></div>

<div data-search-element="results">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
<thead>
</thead>
<tbody>
</tbody>
</table>

<div class="ccm-search-results-pagination"></div>

</div>

<script type="text/template" data-template="search-results-pagination">
<ul class="pagination">
	<li class="<%=pagination.prevClass%>"><%=pagination.previousPage%></li>
	<%=pagination.pages%>
	<li class="<%=pagination.nextClass%>"><%=pagination.nextPage%></li>
</div>
</script>

<script type="text/template" data-template="search-results-table-head">
<tr>
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




