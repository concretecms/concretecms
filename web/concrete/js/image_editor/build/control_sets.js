im.bind('imageload',function(){
  var cs = settings.controlsets || {}, namespace,first;
  for (namespace in cs) {
    var myns = namespace;
    if (!first) first = myns;
    var running = 0;
    $.ajax(cs[myns]['src'],{
      dataType:'text',
      cache:false,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        running--;
        var nso = im.addExtension(this.myns,js,cs[this.myns]['element']);
        im.fire('controlsetload',nso);
        if (0 == running) {
          im.activeControlSet = first;
          im.trigger('changecontrolset',first);
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.activeControlSet = first;
          im.trigger('changecontrolset',first);
        }
      }
    });
  }
});
im.bind('changecontrolset',function(e){
  var active = $('div.controlset[data-namespace='+e.eventData+']','div.controls')
    .children('div.control').slideDown().end().children('h4').addClass('active').end();
  $('div.controlset','div.controls').not(active)
    .children('div.control').slideUp().end().children('h4').removeClass('active');
});