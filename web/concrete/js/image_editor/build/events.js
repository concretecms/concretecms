// Handle event binding.
im.bindEvent = im.bind = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  if (element.addEventListener) {
    element.addEventListener(type.toLowerCase(), handler, false);
  } else {
    element.attachEvent('on' + type.toLowerCase(), handler);
  }
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data) {
  var event, eventName = 'ImageEditorEvent', element = im.stage.getContainer(), data = data || im;
  if (document.createEvent) {
    event = document.createEvent("HTMLEvents");
    event.initEvent(type.toLowerCase(), true, true);
  } else {
    event = document.createEventObject();
    event.eventType = type.toLowerCase();
  }
  event.eventName = eventName;
  event.eventData = data || { };

  if (document.createEvent) {
    element.dispatchEvent(event);
  } else {
    element.fireEvent("on" + event.eventType, event);
  }
  if (typeof element['on' + type.toLowerCase()] === 'function') { element['on' + type.toLowerCase()](event); }
};
