// Handle event binding.
im.bindEvent = im.bind = im.on = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ccm_event.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ccm_event.pub(type,data,element);
};
