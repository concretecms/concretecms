/* global ConcreteHelpGuideManager, ccmi18n_helpGuides, ccmi18n_tourist, Tour */

ConcreteHelpGuideManager.register('add-content', function() {
    var i18n = ccmi18n_helpGuides['add-content']
    var steps = [{
        element: '[data-guide-toolbar-action=add-content]',
        content: '<h3>' + i18n[0].title + '</h3>' + i18n[0].text
    }]
    var tourRunning = false
    var tour = new Tour({
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
        onStart: function() {
            ConcreteHelpGuideManager.enterToolbarGuideMode()
            ConcreteHelpGuideManager.launchGuideOnRefresh('add-content-edit-mode')
            $('#tourBackdrop').detach() // https://github.com/IGreatlyDislikeJavascript/bootstrap-tourist/issues/42
            tourRunning = true
        },
        onShown: ConcreteHelpGuideManager.updateStepFooter,
        onEnd: function() {
            ConcreteHelpGuideManager.exitToolbarGuideMode()
            tourRunning = false
        }
    })
    ConcreteEvent.subscribe('PanelOpen', function(e, data) {
        if (tourRunning && data.panel.options.identifier === 'add-block') {
            tour.end()
            setTimeout(function() {
                ConcreteHelpGuideManager.getGuide('add-content-edit-mode').start()
            }, 0)
        }
    })

    return tour
})
