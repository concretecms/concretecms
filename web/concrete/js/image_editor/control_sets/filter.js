var me = $(this);
im.disable();

im.selected = false;
im.bind('ChangeActiveAction',function(e){
  if (e.eventData != im.namespace) {
    if (im.selected)
      im.hideSlideOut();
    im.selected = false;
  } else {
    im.selected = true;
    im.showSlideOut(ul.clone(1))
  }
});
im.bind('ChangeActiveElement',function(e){
  if (im.activeElement.elementType != 'image') {
    im.disable();
    return;
  }
  im.enable();
});

var img = new Image();
var loaded = false;
var waiting = [];
im.onload = function() {
  loaded = true;
  $.each(waiting,function(e,func){

  });
};
var lis = {};
img.src = "/concrete/images/image_editor/default_filter_image.jpg";

var ul = $($.parseHTML('<ul/>')).addClass('slideOutBlockList');
im.bind('filterLoad',function(e) {
  var newFilter = e.eventData;
  var li = $($.parseHTML('<li/>')).appendTo(ul);
  var title = $($.parseHTML('<span/>')).appendTo(li).text(newFilter.name).addClass('title');
  lis[newFilter.im.namespace] = li;
  (function() {
    var div = document.createElement(div);
    var stage = new Kinetic.Stage({
      container:div,
      width:160,
      height:130
    });
    var layer = new Kinetic.Layer();
    var image = new Kinetic.Image({
      image:img,
      width:160,
      height:130
    });
    layer.add(image);
    stage.add(layer);
    stage.draw();
    im.fire('filterApplyExample',{namespace:newFilter.im.namespace, image:image, elem:li.get(0)});
    im.bind('filterBuiltExample',function(e){
      stage.toImage({
        width:160,
        height:130,
        x:0,
        y:0,
        callback:function(renderedimage){
          li.append($(renderedimage));
        }
      });
    },li.get(0));
  })();
  newFilter.parent = me;

  // Bindings.
  li.click(function(){
    me.slideUp(150,function(){
      me.empty();
      im.fire('filterChange',newFilter);
      me.slideDown(150);
    })
  });

  me.append(newFilter.label);
  me.append(newFilter.controls);
  im.fire('filterFullyLoaded',newFilter);
});
