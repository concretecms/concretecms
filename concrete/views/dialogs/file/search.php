<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-choose="file-manager" class="h-100">
    <concrete-file-chooser></concrete-file-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=file-manager]',
            components: config.components
        })
    })

</script>

<?php

/* ?>

<div data-search="files" class="ccm-ui">

    <script type="text/template" data-template="search-results-table-body">
        <% _.each(items, function (item) {%>
        <tr
            data-file-manager-tree-node="<%=item.treeNodeID%>"
            data-file-manager-tree-node-type="<%=item.treeNodeTypeHandle%>"
            data-file-manager-file="<%=item.fID%>">
            <td class="ccm-search-results-icon">
                <%=item.resultsThumbnailImg%>
            </td>
            <% for (i = 0; i < item.columns.length; i++) {
            var column = item.columns[i]; %>
            <% if (i == 0) { %>
            <td class="ccm-search-results-name"><%-column.value%></td>
            <% } else { %>
            <td><%-column.value%></td>
            <% } %>
            <% } %>
        </tr>
        <% }); %>
    </script>


    <div data-search-element="wrapper"></div>

    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table">
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

    <script type="text/template" data-template="search-results-table-head">
        <tr>
            <th>
            </th>
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

</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=files]').concreteFileManager({
		result: <?=json_encode($result->getJSONObject())?>
	});
});
</script>

<?php */ ?>
