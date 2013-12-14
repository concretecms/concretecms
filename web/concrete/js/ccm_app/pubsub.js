!function(global) {
  'use strict';

  var ConcreteEvent = {

    bindings: {},

    Event: function(type, data, target) {
      this.target = target || ConcreteEvent.bindings;
      this.continuePropagation = true;
      this.eventData = data || {};
      this.type = type;
    },

    subscribe: function(type, handler, target) {
      target = target || ConcreteEvent.bindings;
      type = type.toLowerCase();
      (target[type] = target[type] || []).push(handler);
      return ConcreteEvent;
    },

    publish: function(type, data, target) {
      (new ConcreteEvent.Event(type.toLowerCase(), data, target)).propagate();
      return ConcreteEvent;
    }
  }

  ConcreteEvent.Event.prototype = {
    stopPropagation: function() {
      this.continuePropagation = false;
    },
    propagate: function() {
      this.continuePropagation = true;
      var bound = this.target[this.type] || [], l = bound.length;
      while (l-- && this.continuePropagation) {
        bound[l].call(this, this);
      }
    }
  };
  

  ConcreteEvent.sub = ConcreteEvent.bind = ConcreteEvent.watch   = ConcreteEvent.on = ConcreteEvent.subscribe;
  ConcreteEvent.pub = ConcreteEvent.fire = ConcreteEvent.trigger = ConcreteEvent.publish;

  global.ConcreteEvent = ConcreteEvent;
  
}(window);