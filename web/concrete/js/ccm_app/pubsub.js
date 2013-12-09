;(function(window){
  "use strict";

  /**
   * Event Management
   * @author Korvin Szanto <Korvin@concrete5.org>
   */
  var target = window.document.createElement('span');
  var concrete5_event = {

    /**
     * Subscribe to an event
     * @param  {String | Array} type    The event handle(s)
     * @param  {function}       handler The function to call
     * @param  {HTMLElement}    elem    An element to replace the default target
     * @return {Object}                 return `this` to allow chaining
     */
    subscribe: function concrete5EventSubscribe(type, handler, elem) {
      if (type instanceof Array) {
        for (var i = type.length - 1; i >= 0; i--) {
          this.subscribe(type[i], handler, elem);
        }
        return concrete5_event;
      }
      var element = elem || target;
      if (element.addEventListener) {
        element.addEventListener(type.toLowerCase(), handler, false);
      } else {
        element.attachEvent('on' + type.toLowerCase(), handler);
      }
      return concrete5_event; // Chaining
    },

    /**
     * Publish an event
     * @param  {String | Array} type The event handle(s)
     * @param  {unknown}        data The data to send along with the event
     * @param  {HTMLElement}    elem An element to replace the default target
     * @return {Object}              return `this` to allow chaining
     */
    publish: function concrete5EventPublish(type, data, elem) {
      if (type instanceof Array) {
        for (var i = type.length - 1; i >= 0; i--) {
          this.publish(type[i], data, elem);
        }
        return concrete5_event;
      }
      var event, eventName = 'CCMEvent', element = elem || target;
      if (document.createEvent) {
        event = document.createEvent("HTMLEvents");
        event.initEvent(type.toLowerCase(), true, true);
      } else {
        event = document.createEventObject();
        event.eventType = type.toLowerCase();
      }
      event.eventName = eventName;
      event.eventData = data || {};

      if (document.createEvent) {
        element.dispatchEvent(event);
      } else {
        element.fireEvent("on" + event.eventType, event);
      }
      if (typeof element['on' + type.toLowerCase()] === 'function') {
        element['on' + type.toLowerCase()](event);
      }
      if (typeof console == "object") {
        console.log('concrete5 Publish: ' + type, data, elem);
      }
      return concrete5_event; // Chaining
    }
  };

  // Add aliases
  concrete5_event.sub = concrete5_event.bind = concrete5_event.watch   = concrete5_event.on = concrete5_event.subscribe;
  concrete5_event.pub = concrete5_event.fire = concrete5_event.trigger = concrete5_event.publish;


  window.ccm_event     = concrete5_event;
  window.ccm_subscribe = concrete5_event.sub;
  window.ccm_publish   = concrete5_event.pub;
})(window);

/**
// Minimal example:
//Binding:
ccm_event.bind('someEvent', function(e){alert('Caught event with data: '+e.eventData.info)});

//Firing:
ccm_event.fire('someEvent', {info:'Some Data'});


// Sample subscription.
ccm_subscribe('dialogOpened',function(event){
  data = event.eventData;

  if (data.type == "add_block_dialog") {
    ccm_subscribe('close',function(event){
      console.log('Closed Dialog.');
    }, data.element);
  }
});

// Sample publish
function launchAddDialog(){
  var element = $('<div><span>Add Dialog</span></div>');
  $.fn.dialog.open({
    element:element,
    onClose:function(){
      ccm_publish('close',{},data.element);
    }
  });
  var data = {};
  data.element = element.get(0);
  data.type = 'add_block_dialog';
  ccm_publish('dialogOpened',data);
}
*/
