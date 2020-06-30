<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @TODO - this entire page needs to be abstracted so that the board data sources
 * can provide both the VueJS options for their selectors as well as the items that can be selected
 * Right now since calendar event and page are the only data sources we're going to hard-code those into this
 * page, because I don't want to add methods to the data source interfaces only to have us undo them in the future
 * So in the meantime, we're going to hard code this page for calendar event and page, and come back to making it
 * more abstracted sometime soon.
 */
?>


<form method="post" action="<?=$view->action('submit', $element->getID())?>" data-form="choose-items" v-cloak>
    <?=$token->output('submit')?>

    <div v-for="(item, index) in items" :key="index">

        <div>
            <div class="form-group">
                <label class="control-label">{{item.label}}</></label>
                <a @click="removeItem(index)" href="#" class="ccm-hover-icon float-right">
                    <svg width="20" height="20"><use xlink:href="#icon-minus-circle" /></svg>
                </a>
                <component :is="item.itemComponent" v-bind="item.data"></component>
            </div>
        </div>


    </div>

    <h3 class="font-weight-light"><?=t('Add Item')?></h3>
    <div class="dropdown">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
            <?=t('Choose Type')?>
        </button>

        <div class="dropdown-menu">
            <a href="#" @click="addItem('page')" class="dropdown-item"><?=t('Page')?></a>
            <a href="#" @click="addItem('calendar_event')" class="dropdown-item"><?=t('Calendar Event')?></a>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn float-right btn-secondary"><?=t('Next')?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'form[data-form=choose-items]',
            components: config.components,
            data: {
                availableItems: {
                    'page': {
                        'itemType': 'page',
                        'itemComponent': 'ConcretePageInput',
                        'label': <?=json_encode(t('Page'))?>,
                        'data': {
                            'chooseText': <?=json_encode(t('Choose Page'))?>,
                            'inputName': 'field[page][]'
                        }
                    },
                    'calendar_event': {
                        'itemType': 'calendar_event',
                        'itemComponent': 'ConcreteEventOccurrenceInput',
                        'label': <?=json_encode(t('Calendar Event'))?>,
                        'data': {
                            'calendarId': 1,
                            'chooseText': <?=json_encode(t('Choose Event'))?>,
                            'inputName': 'field[calendar_event][]'
                        }
                    },
                },
                items: [],
            },

            watch: {},

            methods: {
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                addItem(itemTypeHandle) {
                    const data = this.availableItems[itemTypeHandle]
                    this.items.push(data)
                }
            }
        })
    })
});
</script>