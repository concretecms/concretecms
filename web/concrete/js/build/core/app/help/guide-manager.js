!function (global, $) {
    'use strict';

    var ConcreteHelpGuideManager = {

        guides: {},

        register: function (key, guide) {
            this.guides[key] = guide;
        },

        getGuide: function (key) {
            return this.guides[key];
        }

    };

    global.ConcreteHelpGuideManager = ConcreteHelpGuideManager;

}(this, $);