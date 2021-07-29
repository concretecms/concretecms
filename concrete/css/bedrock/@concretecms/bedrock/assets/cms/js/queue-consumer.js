/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

class ConcreteQueueConsumer {
    static consume(token) {
        var my = this
        new ConcreteAjaxRequest({
            loader: false,
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/messenger/consume/',
            data: {
                token: token
            },
            success: r => {
                if (r.messages > 0) {
                    setTimeout(function() {
                        my.consume(token)
                    }, 2000)
                }
            }
        })
    }
}

global.ConcreteQueueConsumer = ConcreteQueueConsumer
