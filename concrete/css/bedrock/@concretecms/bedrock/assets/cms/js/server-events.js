/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

class ConcreteServerEvents {
    static listen(url, topicUrl) {
        const eventSourceUrl = new URL(url)
        eventSourceUrl.searchParams.append('topic', topicUrl + '/{+anything}')
        const eventSource = new EventSource(eventSourceUrl)
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            var data = JSON.parse(event.data)
            var eventName = 'ConcreteServerEvent' + data.event
            ConcreteEvent.publish(eventName, data.data)
        }
    }
}

global.ConcreteServerEvents = ConcreteServerEvents
