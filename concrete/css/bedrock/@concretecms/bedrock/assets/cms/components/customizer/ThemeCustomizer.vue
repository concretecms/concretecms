<template>
    <div>
        <div v-for="set in styleList.sets">
            <h5>{{ set.name }}</h5>
            <div class="card mb-4">
                <ul class="list-group list-group-flush">
                    <li v-for="style in set.styles" class="list-group-item">
                        <component @update="update" :style-value="style"
                                   :is="style.style.type.replace('_','-') + '-page-customizer-widget'"></component>
                    </li>
                </ul>
            </div>
        </div>

        <div class="d-none">
            <div data-dialog="save-theme-customizer-changes">

                <div class="form-group">
                    <label for="newSkinName">Skin Name</label>
                    <input type="text" :class='{"form-control": true, "is-invalid": invalidSkinName}' name="newSkinName"
                           id="newSkinName" v-model="newSkinName">
                </div>

                <div class="dialog-buttons">
                    <button class="btn btn-primary float-right" @click="createNewSkin" type="button">Create</button>
                </div>

            </div>
        </div>

        <div class="d-none">
            <div data-dialog="delete-theme-customizer-skin">

                <p>Are you sure you want to delete this custom skin? This cannot be undone.</p>

                <div class="dialog-buttons">
                    <button class="btn btn-primary float-right" @click="deleteSkin" type="button">Create</button>
                </div>

            </div>
        </div>

    </div>
</template>

<script>
/* eslint-disable no-new */
/* global ConcreteEvent ConcretePanelManager */
import ColorPageCustomizerWidget from './ColorPageCustomizerWidget'
import FontFamilyPageCustomizerWidget from './FontFamilyPageCustomizerWidget'

export default {
    components: {
        ColorPageCustomizerWidget,
        FontFamilyPageCustomizerWidget
    },
    methods: {
        goBack() {
            jQuery.fn.dialog.closeTop()
            var panel = ConcretePanelManager.getByIdentifier('customize-theme')
            panel.goBack(true)
            $('div[data-dialog=save-theme-customizer-changes]').remove()
            $('div[data-dialog=delete-theme-customizer-skin]').remove()
        },
        deleteSkin() {
            var my = this
            new ConcreteAjaxRequest({
                url: my.deleteAction,
                data: {
                    ccm_token: CCM_SECURITY_TOKEN
                },
                success: function (r) {
                    my.goBack()
                }
            })
        },
        createNewSkin() {
            var my = this
            if (!my.newSkinName) {
                my.invalidSkinName = true
            } else {
                my.invalidSkinName = false
            }
            if (!my.invalidSkinName) {
                new ConcreteAjaxRequest({
                    url: my.createNewAction,
                    data: {
                        skinName: my.newSkinName,
                        styles: my.styles,
                        ccm_token: CCM_SECURITY_TOKEN
                    },
                    success: function (r) {
                        my.goBack()
                    }
                })
            }
        },
        update(styleValue) {
            var styles = []
            this.styles.forEach(function (style) {
                if (style.variable === styleValue.variable) {
                    style.value = styleValue.value // This handles keeping the source of truth in sync with the child components
                }
                styles.push(style)
            })
            this.styles = styles

            // submit to the preview action
            // We have to build a temporary form and submit to the iframe - it's pretty stupid. This is ugly and it's
            // not really how we pass data anywhere else but I haven't found a way to post programmatic data to an iframe
            // any other way :shrug:
            var $form = $('<form />')
            var $input = $('<input />')
            var $token = $('<input />')
            $form.attr('method', 'post')
            $form.attr('target', 'ccm-theme-preview-frame')
            $form.attr('action', this.previewAction)
            $input.attr('type', 'hidden')
            $input.attr('name', 'styles')
            $input.attr('value', JSON.stringify(this.styles))
            $input.appendTo($form)
            $token.attr('type', 'hidden')
            $token.attr('name', 'ccm_token')
            $token.attr('value', CCM_SECURITY_TOKEN)
            $token.appendTo($form)
            $form.appendTo($('body'))
            $form.submit()
        }
    },
    data: function () {
        return {
            invalidSkinName: false,
            styles: [],
            newSkinName: ''
        }
    },
    mounted() {
        for (var i = 0; i < this.styleList.sets.length; i++) {
            var styleSet = this.styleList.sets[i]
            for (var j = 0; j < styleSet.styles.length; j++) {
                var style = styleSet.styles[j]
                this.styles.push({
                    variable: style.style.variable,
                    value: style.value
                })
            }
        }

        var my = this

        ConcreteEvent.unsubscribe('ThemeCustomizerSaveSkin')
        ConcreteEvent.unsubscribe('ThemeCustomizerDeleteSkin')
        ConcreteEvent.unsubscribe('ThemeCustomizerCreateSkin')
        ConcreteEvent.on('ThemeCustomizerSaveSkin', function () {
            new ConcreteAjaxRequest({
                url: my.saveAction,
                data: {
                    styles: my.styles,
                    ccm_token: CCM_SECURITY_TOKEN
                },
                success: function (r) {
                    my.goBack()
                }
            })
        })

        ConcreteEvent.on('ThemeCustomizerDeleteSkin', function () {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=delete-theme-customizer-skin]',
                modal: true,
                width: '400',
                title: 'Save',
                height: 'auto'
            })
        })

        ConcreteEvent.on('ThemeCustomizerCreateSkin', function () {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=save-theme-customizer-changes]',
                modal: true,
                width: '400',
                title: 'Save',
                height: 'auto'
            })
        })
    },
    props: {
        previewAction: {
            type: String
        },
        deleteAction: {
            type: String
        },
        saveAction: {
            type: String
        },
        createNewAction: {
            type: String
        },
        styleList: {
            type: Object
        }
    }
}
</script>
