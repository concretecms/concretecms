<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div data-vue="accordion-block">

    <input type="hidden" name="accordionBlockData" :value="JSON.stringify(entries)" />

    <div class="p-2 btn-toolbar border-primary mb-2 border" role="toolbar">
        <button type="button" class="btn-sm btn btn-secondary" @click="addEntry"><i class="fas fa-plus-circle"></i> <?=t('Add Entry')?></button>
    </div>

    <draggable class="image-container" v-model="entries">
        <div v-for="(entry, index) in entries" :class="{'position-relative': true, 'p-2': true, 'm-2': true, 'bg-light': true, 'bg-opacity-50': !entry.expanded}">
            <div class="btn-group" style="position: absolute; top: 0; right: 0">
                <a href="javascript:void(0)" v-if="entry.expanded" class="d-flex align-items-center btn btn-secondary btn-sm" @click="entry.expanded = false"><i class="fas fa-compress-alt"></i></a>
                <a href="javascript:void(0)" v-if="!entry.expanded" class="d-flex align-items-center btn btn-secondary btn-sm" @click="entry.expanded = true"><i class="fas fa-expand-alt"></i></a>
                <a href="javascript:void(0)" @click="deleteEntry(index)" class="d-flex align-items-center btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
            </div>
            <div v-if="entry.expanded">
                <div class="mb-3">
                    <label class="form-label"><?=t('Title')?></label>
                    <input type="text" autocomplete="off" class="form-control" v-model="entry.title" />
                </div>
                <div>
                    <label class="form-label"><?=t('Body')?></label>
                    <ckeditor v-model="entry.description"></ckeditor>
                </div>
            </div>
            <div v-else>
                <a href="javascript:void(0)" style="cursor: move" class="d-block">{{entry.title}}</a>
            </div>
        </div>
    </draggable>

</div>

<script>
    $(function() {
        Concrete.Vue.activateContext('accordion', function (Vue, config) {
            Vue.use(config.components.CKEditor) // I don't understand why this is required :(
            new Vue({
                el: 'div[data-vue=accordion-block]',
                components: config.components,
                data: {
                    entries: <?=json_encode($entries)?>
                },
                methods: {
                    addEntry() {
                        this.entries.push({
                            title: '',
                            description: '',
                            expanded: true
                        })
                    },
                    deleteEntry(index) {
                        this.entries.splice(index, 1)
                    }
                }
            })
        })
    })
</script>

