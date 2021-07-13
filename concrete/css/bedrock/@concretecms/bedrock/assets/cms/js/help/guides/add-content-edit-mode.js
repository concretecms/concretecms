/* global ConcreteHelpGuideManager, ccmi18n_helpGuides, ccmi18n_tourist, Tour */

var $hack

ConcreteHelpGuideManager.register('add-content-edit-mode', function() {
    var i18n = ccmi18n_helpGuides['add-content-edit-mode']
    var steps = [{
        element: '[data-guide-toolbar-action=add-content]',
        content: '<h3>' + i18n[0].title + '</h3>' + i18n[0].text,
        preventInteraction: true
    }, {
        element: '#ccm-panel-add-block',
        content: '<h3>' + i18n[1].title + '</h3>' + i18n[1].text,
        placement: 'right',
        preventInteraction: true
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[2].title + '</h3>' + i18n[2].text,
        placement: 'right',
        preventInteraction: true,
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('#ccm-panel-add-block .ccm-panel-header-accordion'))
        },
        onHidden: function(tour) {
            $hack.remove()
        }
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[3].title + '</h3>' + i18n[3].text,
        placement: 'right',
        preventInteraction: true,
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('#ccm-panel-add-block input[data-input=search-blocks]'))
        },
        onHidden: function(tour) {
            $hack.remove()
        }
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[4].title + '</h3>' + i18n[4].text,
        placement: 'right',
        preventInteraction: true,
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('#ccm-panel-add-block .ccm-panel-add-block-draggable-block-type:first'))
        },
        onHidden: function(tour) {
            $hack.remove()
        }
    }]

    return new Tour({
        steps: steps,
        framework: 'bootstrap4',
        template: ccmi18n_tourist.template,
        localization: ccmi18n_tourist.localization,
        storage: false,
        showProgressBar: false,
        sanitizeWhitelist: {
            a: [/^data-/, 'href']
        },
        onPreviouslyEnded: function(tour) {
            tour.restart()
        },
        onStart: function(tour) {
            ConcreteHelpGuideManager.clearGuideToLaunchOnRefresh()
            $('#tourBackdrop').detach() // https://github.com/IGreatlyDislikeJavascript/bootstrap-tourist/issues/42
            if (!$('#ccm-panel-add-block').hasClass('ccm-panel-active')) {
                tour.end()
                return
            }
            ConcreteHelpGuideManager.enterToolbarGuideMode()
        },
        onShown: ConcreteHelpGuideManager.updateStepFooter,
        onEnd: function() {
            ConcreteHelpGuideManager.exitToolbarGuideMode()
        }
    })
})
