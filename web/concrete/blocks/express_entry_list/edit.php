<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

?>

<?php
$color = Core::make('helper/form/color');
echo Core::make('helper/concrete/ui')->tabs(array(
    array('search', t('Source & Search'), true),
    array('results', t('Results')),
    array('design', t('Design'))
));
?>

<div class="ccm-tab-content" id="ccm-tab-content-search">

    <div class="form-group">
        <?=$form->label('exEntityID', t('Entity'))?>
        <?=$form->select('exEntityID', $entities, $exEntityID, [
            'data-action' => $view->action('load_entity_data')
        ]);?>
    </div>

    <div class="checkbox"><label>
            <?=$form->checkbox('enableSearch', 1, $enableSearch, array('data-options-toggle' => 'search'))?>
            <?=t('Enable Search')?>
        </label>
    </div>

    <fieldset data-options="search">

        <div class="form-group">
            <label class="control-label"><?=t("Keyword Search")?></label>
            <div class="checkbox"><label>
                    <?=$form->checkbox('enableKeywordSearch', 1, $enableKeywordSearch)?>
                    <?=t('Search by Keywords')?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?=t("Enable Searching by Attributes")?></label>

            <div data-container="advanced-search">

            </div>
        </div>
    </fieldset>

</div>

<div class="ccm-tab-content" id="ccm-tab-content-results">

    <div class="form-group">
        <?=$form->label('displayLimit', t('Items Per Page'))?>
        <?=$form->text('displayLimit', $displayLimit)?>
    </div>


    <div data-container="customize-results">
        <?php if ($customizeElement) { ?>
            <?php $customizeElement->render() ?>
        <?php } else {  ?>
            <?=t('You must choose an entity before you can customize its search results.') ?>
        <?php } ?>
    </div>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-design">

    <fieldset>
        <div class="form-group">
            <?=$form->label('tableName', t('Name'))?>
            <?=$form->text('tableName', $tableName, array('maxlength' => '128'))?>
        </div>
        <div class="form-group">
            <?=$form->label('tableDescription', t('Description'))?>
            <?=$form->text('tableDescription', $tableDescription, array('maxlength' => '128'))?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Design')?></legend>
        <div class="form-group">
            <?=$form->label('headerBackgroundColor', t('Header Background'))?>
            <div>
                <?=$color->output('headerBackgroundColor', $headerBackgroundColor)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerBackgroundColorActiveSort', t('Header Background (Active Sort)'))?>
            <div>
                <?=$color->output('headerBackgroundColorActiveSort', $headerBackgroundColorActiveSort)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerTextColor', t('Header Text Color'))?>
            <div>
                <?=$color->output('headerTextColor', $headerTextColor)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('', t('Table Striping'))?>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 0, $tableStriped)?>
                    <?=t('Off (all rows the same color)')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 1, $tableStriped)?>
                    <?=t('On (change color of alternate rows)')?>
                </label>
            </div>
        </div>

        <div class="form-group" data-options="table-striped">
            <?=$form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color'))?>
            <div>
                <?=$color->output('rowBackgroundColorAlternate', $rowBackgroundColorAlternate)?>
            </div>
        </div>
    </fieldset>

</div>

<script type="text/template" data-template="express-attribute-search-list">
    <% _.each(attributes, function(attribute) { %>
    <div class="checkbox"><label>
        <input type="checkbox" name="searchProperties[]" value="<%=attribute.akID%>">
        <%=attribute.akName%>
        </label>
    </div>
    <% }); %>
</script>


<script type="application/javascript">
    Concrete.event.publish('block.express_entry_list.open', {

    });
</script>

<?php /* ?>

<div class="ccm-tab-content" id="ccm-tab-content-header">



</div>

<div class="ccm-tab-content" id="ccm-tab-content-results">
    <div class="form-group">
        <?=$form->label('tableName', t('Table Name'))?>
        <?=$form->text('tableName', $tableName, array('maxlength' => '128'))?>
    </div>
    <div class="form-group">
        <?=$form->label('tableDescription', t('Table Description'))?>
        <?=$form->text('tableDescription', $tableDescription, array('maxlength' => '128'))?>
    </div>




    <fieldset>
        <legend><?=t('Design')?></legend>
        <div class="form-group">
            <?=$form->label('', t('Table Striping'))?>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 0, $tableStriped)?>
                    <?=t('Off (all rows the same color)')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 1, $tableStriped)?>
                    <?=t('On (change color of alternate rows)')?>
                </label>
            </div>
        </div>

        <div class="form-group" data-options="table-striped">
            <?=$form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color'))?>
            <div>
                <?=$color->output('rowBackgroundColorAlternate', $rowBackgroundColorAlternate)?>
            </div>
        </div>
    </fieldset>

</div>


<script type="text/javascript">
    $(function() {
        $('input[type=checkbox][data-options-toggle]').on('change', function() {
            var option = $(this).attr('data-options-toggle');
            if ($(this).is(':checked')) {
                $('[data-options=' + option + ']').show();
            } else {
                $('[data-options=' + option + ']').hide();
            }
        }).trigger('change');
        $('input[type=radio][data-view-property=thumbnail]').on('change', function() {
            var value = $('input[type=radio][data-view-property=thumbnail]:checked').val();
            if (value != '-1') {
                $('[data-options=thumbnail]').show();
            } else {
                $('[data-options=thumbnail]').hide();
            }
        }).trigger('change');
        $('input[type=radio][name=heightMode]').on('change', function() {
            var value = $('input[type=radio][name=heightMode]:checked').val();
            if (value == 'fixed') {
                $('[data-options=height-mode]').show();
            } else {
                $('[data-options=height-mode]').hide();
            }
        }).trigger('change');
        $('input[type=radio][name=tableStriped]').on('change', function() {
            var value = $('input[type=radio][name=tableStriped]:checked').val();
            if (value == '1') {
                $('[data-options=table-striped]').show();
            } else {
                $('[data-options=table-striped]').hide();
            }
        }).trigger('change');
    });
</script>

*/ ?>