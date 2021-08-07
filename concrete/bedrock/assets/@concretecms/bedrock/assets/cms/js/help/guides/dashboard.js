/* global ConcreteHelpGuideManager, ccmi18n_helpGuides, ccmi18n_tourist, Tour */

var $hack

ConcreteHelpGuideManager.register('dashboard', function() {
    var i18n = ccmi18n_helpGuides.dashboard
    var hideOverlay = function() {
        ConcreteHelpGuideManager.hideOverlay()
    }
    var steps = [{
        element: '[data-guide-toolbar-action=dashboard]',
        content: '<h3>' + i18n[0].title + '</h3>' + i18n[0].text,
        onShown: function(tour) {
            ConcreteHelpGuideManager.updateStepFooter(tour)
            $('.ccm-help-tour .popover-navigation').hide()
            $('a[data-launch-panel=dashboard]').on('click', hideOverlay)
            ConcreteEvent.subscribe('PanelOpen.concreteDashboardTour', function(e, data) {
                setTimeout(function() {
                    var panel = data.panel.getIdentifier()
                    if (panel === 'dashboard') {
                        tour.next()
                    }
                }, 500)
            })
        },
        onHide: function() {
            $('a[data-launch-panel=dashboard]').off('click', hideOverlay)
            ConcreteEvent.unsubscribe('PanelOpen.concreteDashboardTour')
        }
    }, {
        element: '#' + ConcreteHelpGuideManager.POSITIONING_BUG_HACK_ID,
        content: '<h3>' + i18n[1].title + '</h3>' + i18n[1].text,
        placement: 'left',
        preventInteraction: true,
        onShow: function(tour) {
            $hack = ConcreteHelpGuideManager.createPositioningBugHackElement($('div#ccm-panel-dashboard ul.nav a[href$=sitemap]:first').parent())
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
