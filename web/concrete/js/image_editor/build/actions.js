im.bind('imageload',function(){
  var cs = settings.controlsets || {}, filters = settings.filters || {}, components = settings.components || {}, namespace, firstcs, firstf, firstc;
  for (namespace in cs) {
    var myns = "ControlSet_" + namespace;
    console.log(myns);
    if (!firstcs) firstcs = myns;
    var running = 0;
    $.ajax(cs[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        running--;
        var nso = im.addControlSet(this.myns,js,cs[this.namespace]['element']);
        console.log(nso);
        im.fire('controlSetLoad',nso);
        if (0 == running) {
          im.activeControlSet = firstcs;
          im.trigger('ChangeActiveAction',firstcs);
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.activeControlSet = firstcs;
          im.trigger('ChangeActiveAction',firstcs);
        }
      }
    });
  }
  for (namespace in filters) {
    var myns = "Filter_" + namespace;
    if (!firstf) firstf = myns;
    $.ajax(filters[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      success:function(js){
        var nso = im.addFilter(this.myns,js,cs[this.namespace]['element']);
        im.fire('filterLoad',nso);
      }
    });
  }
  for (namespace in components) {
    var myns = "Component_" + namespace;
    if (!firstc) firstc = myns;
    var running = 0;
    $.ajax(components[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      success:function(js){
        var nso = im.addComponent(this.myns,js,cs[this.namespace]['element']);
        im.fire('componentLoad',nso);
      }
    });
  }
});
im.bind('ChangeActiveAction',function(e){
  var ns = e.eventData.substr(11);
  var active = $('div.controlset[data-namespace='+ns+']','div.controls')
    .children('div.control').slideDown().end().children('h4').addClass('active').end();
  $('div.controlset','div.controls').not(active)
    .children('div.control').slideUp().end().children('h4').removeClass('active');
});