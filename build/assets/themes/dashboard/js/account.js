// If the Dashboard is rendering the My Account we need to trigger some of the same JS.
// Much of this has been pulled from the Bedrock frontend account JS - but we can't
// just include that file because it uses the frontend Vue context and since we're
// in the Dashboard we need to use the backend JS context.

if (document.querySelectorAll('[data-view=account]').length) {
    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({
            el: '[data-view=account]',
            components: config.components
        })
    })
}
