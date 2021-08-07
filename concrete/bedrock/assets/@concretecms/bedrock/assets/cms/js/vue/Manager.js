import Vue from 'vue'

/**
 * Typescript interface
 *\/
 interface Context {
    [key: String]: {} | Component
}
 /**/
export default class Manager {
    /**
     * Create a new Manager
     *
     * @param {{[key: String]: Context}} contexts A list of component lists keyed by context name
     */
    constructor(contexts) {
        this.contexts = contexts || {}
    }

    /**
     * Ensures that our Concrete.Vue manager is available on the window object.
     * Note: Do NOT call this before the global Concrete object is created in the CMS context.
     *
     * @param {Window} window
     */
    static bindToWindow(window) {
        window.Concrete = window.Concrete || {}

        if (!window.Concrete.Vue) {
            window.Concrete.Vue = new Manager()
            window.dispatchEvent(new CustomEvent('concrete.vue.ready', {
                detail: window.Concrete.Vue
            }))
        }
    }

    /**
     * Returns a list of components for the current string `context`
     *
     * @param {String} context
     * @returns {{[key: String]: {}}} A list of components keyed by their handle
     */
    getContext(context) {
        return this.contexts[context] || {}
    }

    /**
     * Actives a particular context (and its components) for a particular selector.
     *
     * @param {String} context
     * @param {Function} callback (Vue, options) => new Vue(options)
     */
    activateContext(context, callback) {
        return callback(Vue, {
            components: this.getContext(context)
        })
    }

    /**
     * For a given string `context`, adds the passed components to make them available within that context.
     *
     * @param {String} context The name of the context to extend
     * @param {{[key: String]: {}}} components A list of component objects to add into the context
     * @param {String} newContext The new name of the context if different from context
     */
    extendContext(context, components, newContext) {
        newContext = newContext || context
        this.contexts[newContext] = {
            ...this.getContext(context),
            ...components
        }
    }

    /**
     * Creates a Context object that has access to the specified components. If `fromContext` is passed, the new
     * context object will be created with the same components as the `fromContext` object.
     *
     * @param context
     * @param components
     * @param fromContext
     */
    createContext(context, components, fromContext) {
        this.extendContext(fromContext, components, context)
    }
}
