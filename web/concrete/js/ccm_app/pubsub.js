;(function(global, $) {
  'use strict';
  global.c5 = global.c5 || {};

  global.ConcreteEvent = (function(ns, $) {
    var target = $('<span />'), debug = false;

    var hasGroup = typeof global.console.group === 'function' && typeof global.console.groupEnd === 'function',
        hasLog = typeof global.console.log === 'function';


    function groupLog(group, value, dontcall) {
        if (hasGroup) {
            global.console.group(group);

            if (!dontcall && typeof value === 'function') {
                value();
            } else {
                global.console.log(value);
            }
            global.console.groupEnd();
        } else if (hasLog) {

            if (!dontcall && typeof value === 'function') {
                global.console.log('Group: "' + group + '"');
                value();
                global.console.log('GroupEnd: "' + group + '"');
            } else {
                global.console.log(group, value);
            }
        }
    }
    function getTarget(given_target) {
      if (!given_target) given_target = target;
      if (!(given_target instanceof $)) given_target = $(given_target);
      if (!given_target.length) given_target = target;
      return given_target;
    }
    var ConcreteEvent = {

      debug: function(enabled) {
          if (typeof enabled === 'undefined') {
              return debug;
          }
          return debug = !!enabled;
      },

      subscribe: function(type, handler, target) {

          if (debug) {
              groupLog('Event Subscribed', function() {
                  groupLog('Type', type, true);
                  groupLog('Handler', handler, true);
                  groupLog('Target', target, true);
                  if (typeof global.console.trace === 'function')
                      global.console.trace();
              });

          }
        if (type instanceof Array) {
          return _(type).each(function(v) {
            ConcreteEvent.subscribe(v, handler, target);
          });
        }
        getTarget(target).bind(type.toLowerCase(), handler);
        return ConcreteEvent;
      },

      publish: function(type, data, target) {
          if (debug) {
              groupLog('Event Published', function() {
                  groupLog('Type', type, true);
                  groupLog('Data', data, true);
                  groupLog('Target', target, true);
                  if (typeof global.console.trace === 'function')
                      global.console.trace();
              });

          }
        if (type instanceof Array) {
          return _(type).each(function(v) {
            ConcreteEvent.publish(v, data, target);
          });
        }
        getTarget(target).trigger(type.toLowerCase(), data);
        return ConcreteEvent;
      },

      unsubscribe: function(type, secondary_argument, target) {

          if (debug) {
              groupLog('Event Unsubscribed', function() {
                  groupLog('Type', type, true);
                  groupLog('Secondary Argument', secondary_argument, true);
                  groupLog('Target', target, true);
                  if (typeof global.console.trace === 'function')
                     global.console.trace();
              });

          }

        var args = [type.toLowerCase()];
        if (typeof secondary_argument !== undefined) args.push(secondary_argument);
        $.fn.unbind.apply(getTarget(target), args);
        return ConcreteEvent;
      }
    };

    ConcreteEvent.sub = ConcreteEvent.bind = ConcreteEvent.watch   = ConcreteEvent.on = ConcreteEvent.subscribe;
    ConcreteEvent.pub = ConcreteEvent.fire = ConcreteEvent.trigger = ConcreteEvent.publish;
    ConcreteEvent.unsub = ConcreteEvent.unbind = ConcreteEvent.unwatch   = ConcreteEvent.off = ConcreteEvent.unsubscribe;

    ns.event = ConcreteEvent;
    return ConcreteEvent;
  }(global.c5, jQuery));

}(window, jQuery));
