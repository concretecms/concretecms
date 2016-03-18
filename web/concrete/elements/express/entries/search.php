<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $controller \Concrete\Controller\Search\Express\Entries
 */
$result = $controller->getSearchResultObject()->getJSONObject();
$result = json_encode($result);
?>

<div data-search="express_entries">

    <script type="text/template" data-template="search-results-table-body">
        <% _.each(items, function(entity) {%>
        <tr data-entity-id="<%=entity.id%>">
            <% for(i = 0; i < entity.columns.length; i++) {
            var column = entity.columns[i];
            %>
            <td><%=column.value%></td>
            <% } %>
        </tr>
        <% }); %>
    </script>

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
        <%=paginationTemplate%>
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

    <script type="text/javascript">
        $(function() {
            $('div[data-search=express_entries]').concreteAjaxSearch({
                result: <?=$result?>,
                onUpdateResults: function(concreteSearch) {
                    concreteSearch.$element.on('mouseover', 'tr[data-entity-id]', function(e) {
                        e.stopPropagation();
                        $(this).addClass('ccm-search-select-hover');
                    });
                    concreteSearch.$element.on('mouseout', 'tr[data-entity-id]', function(e) {
                        e.stopPropagation();
                        $(this).removeClass('ccm-search-select-hover');
                    });

                    concreteSearch.$element.on('click.expressEntries', 'tr[data-entity-id]', function(e) {
                        e.stopPropagation();
                        ConcreteEvent.publish('SelectExpressEntry', {
                            exEntryID: $(this).attr('data-entity-id')
                        });
                        return false;
                    });
                }
            });
        });
    </script>

</div>