im.active = false;
im.disable();
var cutlayer = new Kinetic.Layer(),
me = $(this), self = me.get(0),
cache = {width:0,height:0,offset:{width:0,height:0}};
cutlayer.autoCrop = false;

im.on('changeActiveAction',function(e){
	if (e.eventData == im.namespace) {
		im.active = true;
		im.fire('activate',{},self);
	} else {
		im.active = false;
		im.fire('deactivate',{},self);
	}
});
im.on('changeActiveElement',function(e){
	if (e.eventData.elementType !== 'image') {
		im.disable();
		if (im.active) {
			im.fire('changeActiveAction');
		}
	} else {
		im.enable();
	}
});
var cutarea;
var fields = {
	width:$('div.widthinput input',me),
	height:$('div.heightinput input',me)
};
fields.width.keyup(function(){
	var d = parseInt($(this).val());
	cutarea.setRealWidth(d);
	dragger.setX(cutarea.getRealX() + cutarea.getRealWidth());
	dragger.setY(cutarea.getRealY() + cutarea.getRealHeight());
	cutlayer.draw();
});
fields.height.keyup(function(){
	var d = parseInt($(this).val());
	cutarea.setRealHeight(d);
	dragger.setX(cutarea.getRealX() + cutarea.getRealWidth());
	dragger.setY(cutarea.getRealY() + cutarea.getRealHeight());
	cutlayer.draw();
});
im.on('activate',function(){
	cutarea = im.coverLayer.clone();
	cutarea.getRealWidth = function(){return this.getWidth()-this.getStrokeWidth()};
	cutarea.getRealHeight = function(){return this.getHeight()-this.getStrokeWidth()};
	cutarea.setRealWidth = function(w){
		w = Math.max(w,0);
		fields.width.val(w);
		this.setWidth(w+this.getStrokeWidth());
	};
	cutarea.setRealHeight = function(h){
		h = Math.max(h,0);
		fields.height.val(h);
		this.setHeight(h+this.getStrokeWidth());
	};
	cutarea.getRealX = function(){return this.getX()+this.getStrokeWidth()/2};
	cutarea.getRealY = function(){return this.getY()+this.getStrokeWidth()/2};
	cutarea.setRealWidth(cutarea.getRealWidth());
	cutarea.setRealHeight(cutarea.getRealHeight());
	cutarea.setStroke('rgba(0,0,0,.5)');
	cutarea.setListening(true);
	cutarea.setDraggable(true);
	cutarea.setDragBoundFunc(function(pos){
		dragger.setX(cutarea.getRealX() + cutarea.getRealWidth());
		dragger.setY(cutarea.getRealY() + cutarea.getRealHeight());
		cutlayer.draw();
		return pos;
	});

	cache.width = im.saveWidth;
	im.alterCore('test',cutarea);
	cache.height = im.saveHeight;
	cache.offset.width = cache.width - cutarea.getWidth();
	cache.offset.height = cache.height - cutarea.getHeight();

	dragger.setX(cutarea.getRealX() + cutarea.getRealWidth());
	dragger.setY(cutarea.getRealY() + cutarea.getRealHeight());

	cutlayer.add(cutarea);
	dragger.moveToTop();
	im.stage.add(cutlayer);
},self);
im.on('deactivate',function(){
	if (cutarea) cutarea.remove();
	if (cutlayer) cutlayer.remove();
	im.stage.draw();
	cutarea = false;
},self);
$('button.crop',me).click(function(){
	var stagepos = {
		x:im.stage.getX(),
		y:im.stage.getY()
	};
	im.activeElement.toImage({
		width:cutarea.getRealWidth(),
		height:cutarea.getRealHeight(),
		x:cutarea.getRealX() + stagepos.x,
		y:cutarea.getRealY() + stagepos.y,
		callback:function(img){
			im.activeElement.setImage(img);
			im.activeElement.setX(cutarea.getRealX());
			im.activeElement.setY(cutarea.getRealY());
			im.activeElement.setWidth(cutarea.getRealWidth());
			im.activeElement.setHeight(cutarea.getRealHeight());
			im.activeElement.setOffset([im.activeElement.getWidth()/2,im.activeElement.getHeight()/2]);
			im.activeElement.parent.setOffset([-im.activeElement.getWidth()/2,-im.activeElement.getHeight()/2]);
			im.activeElement.setRotation(0);
			im.activeElement.parent.draw();
		}
	});
});

var dragger = new Kinetic.Rect({
	width:7,
	height:7,
	x:0,
	y:0,
	offset:[3.5,3.5],
	stroke:'black',
	draggable:true
});
dragger.on('dragmove',function(){
	cutarea.setRealWidth(dragger.getX() - cutarea.getRealX());
	cutarea.setRealHeight(dragger.getY() - cutarea.getRealY());
	cutarea.parent.draw();
});
cutlayer.add(dragger);