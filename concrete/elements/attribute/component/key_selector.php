<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array $attributes
 * @var string $selectedAttributes
 * @var string $selectAttributeUrl
 */
?>

<div data-view="attributes">

    <div v-for="attribute in selectedAttributes" :key="attribute.akID">
        <div class="form-group">
            <a class="float-right ccm-hover-icon" href="#" @click.prevent="removeAttribute(attribute.akID)">
                <i class="fa fa-minus-circle"></i>
            </a>
            <label class="control-label" :for="attribute.controlID">{{attribute.label}}</label>
            <div :id="'ccm-attribute-key-' + attribute.akID"></div>
            <input type="hidden" name="selectedKeys[]" :value="attribute.akID" />
        </div>
    </div>

    <h4><?=t('Add Attribute')?></h4>
    <div class="input-group">
        <select class="custom-select" v-model="selectedAttributeToAdd">
            <option value=""><?=t('** Choose Attribute')?></option>
            <optgroup v-for="attributeSet in attributes.sets" :label="attributeSet.name">
                <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributeSet.keys" :value="attribute.akID">{{attribute.akName}}</option>
            </optgroup>

            <template v-if="attributes.sets.length > 0 && attributes.unassigned.length > 0">
                <optgroup label="<?=t('Unassigned')?>">
                    <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributes.unassigned" :value="attribute.akID">{{attribute.akName}}</option>
                </optgroup>
            </template>
            <template v-else>
                <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributes.unassigned" :value="attribute.akID">{{attribute.akName}}</option>
            </template>

        </select>
        <div class="input-group-append">
            <button class="btn btn-primary" type="button" @click="addSelectedAttribute"><?=t('Go')?></button>
        </div>
    </div>

</div>

<script>
    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({
            components: config.components,
            el: '[data-view=attributes]',
            data: {
                selectedAttributeToAdd: '',
                selectedAttributes: <?=$selectedAttributes?>,
                attributes: <?=$attributes?>
            },
            mounted() {
                this.selectedAttributes.forEach(attribute => this.loadContent(attribute))
            },
            methods: {
                loadAssets(attribute) {
                    if (attribute.assets.css) {
                        attribute.assets.css.forEach(css => ConcreteAssetLoader.loadCSS(css))
                    }
                    if (attribute.assets.javascript) {
                        attribute.assets.javascript.forEach(javascript => ConcreteAssetLoader.loadJavaScript(javascript))
                    }
                },
                loadContent(attribute) {
                    var my = this
                    setTimeout(function() {
                        $(my.$el).find("#ccm-attribute-key-" + attribute.akID).html(attribute.content)
                    }, 5); // this is dirty. This has to be done because vue won't render the inline JS in our attributes
                },
                isSelected(akID) {
                    return this.selectedAttributes.findIndex(attribute => attribute.akID == akID) > -1
                },
                removeAttribute(akID) {
                    var my = this
                    const index = my.selectedAttributes.findIndex(attribute => attribute.akID == akID)
                    my.selectedAttributes.splice(index, 1)
                },
                addSelectedAttribute() {
                    var my = this
                    if (this.selectedAttributeToAdd) {
                        new ConcreteAjaxRequest({
                            url: '<?=$selectAttributeUrl?>',
                            data: {
                                'akID': my.selectedAttributeToAdd
                            },
                            success: function(r) {
                                my.selectedAttributeToAdd = ''
                                my.loadAssets(r)
                                my.selectedAttributes.push(r)
                                my.loadContent(r)
                            }
                        })
                    }
                }
            },
        })
    })
</script>
