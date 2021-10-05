<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Type\Group;
use Concrete\Controller\Search\Groups;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;

/** @var Groups $controller */

$pk = Key::getByHandle("access_group_search");

if (!$pk->validate()) { ?>
    <p>
        <?php echo t('You do not have access to the group search.') ?>
    </p>
<?php } else { ?>

    <?php
    $app = Application::getFacadeApplication();
    /** @var Form $form */
    $form = $app->make(Form::class);
    /** @var Request $request */
    $request = $app->make(Request::class);
    /** @noinspection PhpComposerExtensionStubsInspection */
    $result = json_encode($controller->getSearchResultObject()->getJSONObject());
    $tree = Group::get();
    $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
    $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);
    ?>
    <style>
        div[data-search=groups] form.ccm-search-fields {
            margin-left: 0 !important;
        }
    </style>

    <div data-search="groups">
        <script type="text/template" data-template="search-form">
            <form role="form" data-search-form="groups"
                  action="<?php echo (string)Url::to('/ccm/system/search/groups/submit') ?>"
                  class="ccm-search-fields ccm-search-fields-none">
                <?php echo $form->hidden("filter", $request->request('filter')); ?>
                <div class="form-group">
                    <div class="ccm-search-main-lookup-field">
                        <i class="fas fa-search"></i>
                        <?php echo $form->search('keywords', $request->request('keywords'), ['placeholder' => t('Name')]) ?>
                        <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1">
                            <?php echo t('Search') ?>
                        </button>
                    </div>
                </div>
            </form>
        </script>

        <script type="text/template" data-template="search-results-table-body">
            <% _.each(items, function(group) {%>
            <tr>
                <%
                for(i = 0; i < group.columns.length; i++) {
                var column = group.columns[i];
                %>
                <td><%=column.value%></td>
                <%
                }
                %>
            </tr>
            <% }); %>
        </script>

        <div data-search-element="wrapper"></div>

        <div class="group-tree" data-group-tree="<?php echo $tree->getTreeID() ?>"></div>

        <div data-search-element="results">
            <table class="ccm-search-results-table">
                <thead></thead>
                <tbody></tbody>
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
                if (column.isColumnSortable) {
                %>
                <th class="<%=column.className%>"><!--suppress HtmlUnknownTarget -->
                    <a href="<%=column.sortURL%>"><%=column.title%></a></th>
                <%
                } else {
                %>
                <th><span><%=column.title%></span></th>
                <%
                }
                }
                %>
            </tr>
        </script>

        <!--suppress EqualityComparisonWithCoercionJS, ES6ConvertVarToLetConst -->
        <script>
            $(function () {
                $('[data-group-tree]').concreteTree({
                    <?php
                    if ($request->request('filter') == 'assign') {
                    ?>
                    'removeNodesByKey': ['<?php echo $guestGroupNode->getTreeNodeID()?>', '<?php echo $registeredGroupNode->getTreeNodeID()?>'],
                    <?php
                    }
                    if ($selectMode) {
                    ?>
                    onClick: function (node) {
                        if (node.data.gID) {
                            ConcreteEvent.publish('SelectGroup', {'gID': node.data.gID, 'gName': node.title});
                        } else {
                            return false;
                        }
                    },
                    'enableDragAndDrop': false,
                    <?php
                    }
                    ?>
                    'treeID': '<?php echo $tree->getTreeID()?>'
                });
                $('div[data-search=groups]').concreteAjaxSearch({
                    result: <?php echo $result?>,
                    onLoad: function (concreteSearch) {
                        var handleSubmit = function () {
                            var $input = concreteSearch.$element.find('input[name=keywords]');
                            if ($input.val() != '') {
                                concreteSearch.$element.find('[data-group-tree]').hide();
                                concreteSearch.$results.show();
                            } else {
                                concreteSearch.$element.find('[data-group-tree]').show();
                                concreteSearch.$results.hide();
                            }
                        }
                        concreteSearch.$element.on('submit', 'form[data-search-form=groups]', handleSubmit);
                        handleSubmit();
                        concreteSearch.$element.on('keyup', 'input[name=keywords]', function () {
                            if ($(this).val() == '') {
                                handleSubmit();
                            }
                        });
                        <?php
                        if ($selectMode) {
                        ?>
                        concreteSearch.$element.on('click', 'a[data-group-id]', function () {
                            ConcreteEvent.publish('SelectGroup', {
                                gID: $(this).attr('data-group-id'),
                                gName: $(this).attr('data-group-name')
                            });
                            return false;
                        });
                        <?php
                        }
                        ?>
                    }
                });
            });
        </script>
    </div>
<?php } ?>