/* global ConcreteHelpGuideManager, ccmi18n_helpGuides, ccmi18n_tourist, Tour */

var $hack

ConcreteHelpGuideManager.register('add-page', function() {
    var i18n = ccmi18n_helpGuides['add-page']
    var hideOverlay = function() {
        ConcreteHelpGuideManager.hideOverlay()
    }
    var steps = [{
        element: '[data-guide-toolbar-action=sitemap]',
        content: '<h3>' + i18n[0].title + '</h3>' + i18n[0].text,
        onShown: function(tour) {
            ConcreteHelpGuideManager.updateStepFooter(tour)
            $('.ccm-help-tour .popover-navigation').hide()
            $('a[data-launch-panel=sitemap]').on('click', hideOverlay)
            ConcreteEvent.subscribe('PanelOpen.concreteAddPageTour', function(e, data) {
                setTimeout(function() {
                    var panel = data.panel.getIdentifier()
                    if (panel === 'sitemap') {
                        tour.next()
                    }
                }, 500)
            })
        },
        onHide: function() {
            $('a[data-launch-panel=sitemap]').off('click', hideOverlay)
            ConcreteEvent.unsubscribe('PanelOpen.concreteAddPageTour')
        }
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[1].title + '</h3>' + i18n[1].text,
        placement: 'left',
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('#ccm-panel-sitemap header:nth-of-type(1)>*:first'))
        },
        onHidden: function(tour) {
            $hack.remove()
        }
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[2].title + '</h3>' + i18n[2].text,
        placement: 'left',
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('#ccm-panel-sitemap header:nth-of-type(2)>*:first'))
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
        onStart: function() {
            ConcreteHelpGuideManager.enterToolbarGuideMode()
            $('#tourBackdrop').detach() // https://github.com/IGreatlyDislikeJavascript/bootstrap-tourist/issues/42
        },
        onShown: ConcreteHelpGuideManager.updateStepFooter,
        onEnd: function() {
            ConcreteHelpGuideManager.exitToolbarGuideMode()
        }
    })
})
