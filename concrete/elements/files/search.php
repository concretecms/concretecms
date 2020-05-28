<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var Result $result */
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
$list = $result->getItemListObject();

?>

<script type="text/template" data-template="search-results-table-body">
    <% _.each(items, function (item) {%>
        <tr data-launch-search-menu="<%=item.treeNodeID%>"
            data-file-manager-tree-node="<%=item.treeNodeID%>"
            data-file-manager-tree-node-type="<%=item.treeNodeTypeHandle%>"
            data-file-manager-file="<%=item.fID%>">

            <td class="ccm-search-results-icon">
                <%=item.resultsThumbnailImg%>
            </td>

            <% for (i = 0; i < item.columns.length; i++) {
                let column = item.columns[i]; %>

                <% if (i == 0) { %>
                    <td class="ccm-search-results-name">
                        <%-column.value%>
                    </td>
                <% } else { %>
                    <td>
                        <%-column.value%>
                    </td>
                <% } %>
            <% } %>
        </tr>
    <% }); %>
</script>

<div data-search-element="wrapper"></div>

<div data-search-element="results">
    <div class="table-responsive ccm-file-manager-list-wrapper">
        <table class="ccm-file-manager-list ccm-search-results-table ccm-search-results-table-icon">
            <thead>
                &nbsp;
            </thead>

            <tbody>
                &nbsp;
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
            <div class="dropdown">
                <button class="btn btn-menu-launcher" disabled data-toggle="dropdown"><i class="fa fa-chevron-down"></i>
                </button>
            </div>
        </th>

        <% for (i = 0; i < columns.length; i++) {
            let column = columns[i];

            if (column.isColumnSortable) { %>
                <th class="<%=column.className%>">
                    <!--suppress HtmlUnknownTarget -->
                    <a href="<%=column.sortURL%>">
                        <%-column.title%>
                    </a>
                </th>
            <% } else { %>
                <th>
                    <span>
                        <%-column.title%>
                    </span>
                </th>
            <% } %>
        <% } %>
    </tr>
</script>

