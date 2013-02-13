im.bind('imageload',function(){
  var cs = settings.controlsets || {}, filters = settings.filters || {}, components = settings.components || {}, namespace, firstcs;
  var running = 0;
  im.fire('LoadingControlSets');
  for (namespace in cs) {
    var myns = "ControlSet_" + namespace;
    log(myns);
    if (!firstcs) firstcs = myns;
    $.ajax(cs[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        if (im === undefined) {
          im
        }
        running--;
        var nso = im.addControlSet(this.myns,js,cs[this.namespace]['element']);
        log(nso);
        im.fire('controlSetLoad',nso);
        if (0 == running) {
          im.trigger('ControlSetsLoaded');
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.trigger('ControlSetsLoaded');
        }
      }
    });
  }
});
im.bind('ControlSetsLoaded',function(){ // do this when the control sets finish loading.
  log('Loaded');
  var filters = settings.filters || {}, components = settings.components || {}, namespace, firstf, firstc ;
  im.fire('LoadingFilters');
  for (namespace in filters) {
    var myns = "Filter_" + namespace;
    var name = filters[namespace].name;
    if (!firstf) firstf = myns;
    $.ajax(filters[namespace].src,{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      name:name,
      success:function(js){
        var nso = im.addFilter(this.myns,js);
        nso.name = this.name;
        im.fire('filterLoad',nso);
      }
    });
  }
  im.fire('LoadingComponents');
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
  var ns = e.eventData;
  if (ns === im.activeControlSet) return;
  for (var ons in im.controlSets) {
    if (ons !== ns) $(im.controlSets[ons]).slideUp();
  }
  im.activeControlSet = ns;
  if (!ns) return;
  var cs = $(im.controlSets[ns]),
      height = cs.show().height();
  cs.hide().height(height).slideDown(function(){$(this).height('');});
});

im.bind('ChangeNavTab',function(e) {
  im.trigger('ChangeActiveAction');
  switch(e.eventData) {
    case 'add':

  }
});