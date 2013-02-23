

im.stage.setDragBoundFunc(function(ret) {


  var dim = im.stage.getTotalDimensions();

  var maxx = Math.max(dim.max.x,dim.min.x)-1,
      minx = Math.min(dim.max.x,dim.min.x)+1,
      maxy = Math.max(dim.max.y,dim.min.y)-1,
      miny = Math.min(dim.max.y,dim.min.y)+1;

  ret.x = Math.floor(ret.x);
  ret.y = Math.floor(ret.y);

  if (ret.x > maxx) ret.x = maxx;
  if (ret.x < minx) ret.x = minx;
  if (ret.y > maxy) ret.y = maxy;
  if (ret.y < miny) ret.y = miny;

  ret.x = Math.floor(ret.x);
  ret.y = Math.floor(ret.y);

  return ret;
});
im.stage.setDraggable(true);