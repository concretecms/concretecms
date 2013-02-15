// Handle event binding.
im.bindEvent = im.bind = im.on = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  ccm_event.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
  var element = im.stage.getContainer() || elem;
  ccm_event.pub(type,data,element);
};
