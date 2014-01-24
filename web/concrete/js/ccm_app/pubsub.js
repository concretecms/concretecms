;(function(global, $) {
  'use strict';
  global.c5 = global.c5 || {};

  global.ConcreteEvent = (function(ns, $) {
    var target = $('<span />');
    function getTarget(given_target) {
      if (!given_target) given_target = target;
      if (!(given_target instanceof $)) given_target = $(given_target);
      if (!given_target.length) given_target = target;
      return given_target;
    }
    var ConcreteEvent = {

      subscribe: function(type, handler, target) {
        if (type instanceof Array) {
          return _(type).each(function(v) {
            ConcreteEvent.subscribe(v, handler, target);
          });
        }
        getTarget(target).bind(type.toLowerCase(), handler);
        return ConcreteEvent;
      },

      publish: function(type, data, target) {
        if (type instanceof Array) {
          return _(type).each(function(v) {
            ConcreteEvent.publish(v, data, target);
          });
        }
        getTarget(target).trigger(type.toLowerCase(), data);
        return ConcreteEvent;
      }
    };

    ConcreteEvent.sub = ConcreteEvent.bind = ConcreteEvent.watch   = ConcreteEvent.on = ConcreteEvent.subscribe;
    ConcreteEvent.pub = ConcreteEvent.fire = ConcreteEvent.trigger = ConcreteEvent.publish;

    ns.event = ConcreteEvent;
    return ConcreteEvent;
  }(global.c5, jQuery));

}(window, jQuery));
