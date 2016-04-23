<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$list = $result->getItemListObject();
?>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (item) {%>
<tr data-launch-search-menu="<%=item.treeNodeID%>" data-file-manager-tree-node="<%=item.treeNodeID%>">
    <td class="ccm-search-results-icon"><%=item.thumbnail%></td>
    <td class="ccm-search-results-name"><%=item.title%></td>
    <td><%=item.type%></td>
    <td><%=item.dateModified%></td>
    <td><%=item.size%></td>
</tr>
<% }); %>
</script>

<div data-search-element="wrapper"></div>

<div data-search-element="results">
    <div class="table-responsive">
        <table class="ccm-search-results-table ccm-search-results-table-icon">
        <thead>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
    <div class="ccm-search-results-pagination"></div>
</div>

<script type="text/template" data-template="search-results-pagination">
<%=paginationTemplate%>
</script>

<?php /* ?>

<script type="text/template" data-template="search-results-table-head">
<tr>
    <th><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="select-all" /></span></th>
    <th class="ccm-file-manager-search-results-star"><span><i class="fa fa-star"></i></span></th>
    <th><span><?php echo t('Thumbnail')?></th>
    <%
    for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
        <% } else { %>
            <th><span><%-column.title%></span></th>
        <% } %>
    <% } %>
</tr>
</script>

 */ ?>

<script type="text/template" data-template="search-results-table-head">
    <tr>
        <th></th>
        <%
        for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
        <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
        <% } else { %>
        <th><span><%-column.title%></span></th>
        <% } %>
        <% } %>
    </tr>
</script>

