// Handle event binding.
im.bindEvent = im.bind = im.on = function (type, handler, elem) {
	if (type == 'sliderMove')
	console.log("BIND",elem);
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ccm_event.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
	if (type == 'sliderMove')
	console.log("FIRE",elem);
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ccm_event.pub(type,data,element);
};
