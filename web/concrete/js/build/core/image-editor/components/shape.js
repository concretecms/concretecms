im.selected = false;
var me = $(this), elem = this;

var shapes = [
	{
		name:"Star",
		images: [
			"http://imageeditor.com/files/9813/6700/0214/05_SolidStar.svg",
		]
	},
	{
		name:"Square",
		images: [
			"http://imageeditor.com/files/4813/6700/0214/02_Square.svg"
		]
	},
	{
		name:"Circle",
		images: [
			"http://imageeditor.com/files/1913/6700/0213/01_Circle.svg"
		]
	},
	{
		name:"Rounded Square",
		images: [
			"http://imageeditor.com/files/6513/6700/0214/03_RoundedSquare.svg"
		]
	},
	{
		name:"Triangle",
		images: [
			"http://imageeditor.com/files/5613/6700/0214/04_Triangle.svg"
		]
	},
	{
		name:"Badge Star",
		images: [
			"http://imageeditor.com/files/1113/6700/0214/08_BadgeStar.svg"
		]
	},
	{
		name:"Badge Star",
		images: [
			"http://imageeditor.com/files/6013/6700/0214/07_ManyStars.svg"
		]
	},
	{
		name:"Speech Bubble",
		images: [
			"http://imageeditor.com/files/3113/6700/0214/09_ComicSpeechBubble_1_BG.svg",
			"http://imageeditor.com/files/2613/6700/0214/09_ComicSpeechBubble_1_FG.svg"
		]
	},
	{
		name:"Speech Bubble 2",
		images: [
			"http://imageeditor.com/files/9313/6700/0214/10_ComicSpeechBubble_2_BG.svg",
			"http://imageeditor.com/files/2513/6700/0214/10_ComicSpeechBubble_2_FG.svg"
		]
	},
	{
		name:"Rockstar",
		images: [
			"http://imageeditor.com/files/7113/6700/0771/06_RockStar_BG.svg",
			"http://imageeditor.com/files/9613/6700/0779/06_RockStar_FG_1.svg"
		]
	}
];

var ul = $(document.createElement('ul')).addClass('slideOutBoxList').addClass('slideOutBoxSvgList'), i, e;
$.each(shapes,function(i,shape) {
	var li = $(document.createElement('li')).attr('data-name',shapes[i].name).appendTo(ul);
	shape.imgs = [];
	shape.paths = [];
	$.each(shapes[i].images,function(e,imgurl) {
		var img = new Image;
		img.src = imgurl;
		shape.paths[e] = {};
		$.get(imgurl, function(d){
			var obs = $(d);
			window.d = obs;
			var path = $(d).find('path');
			shape.paths[e] = {
				path:path.attr('d'),
				fill:path.attr('fill')
			}
		});

		shapes[i].imgs.push(img);
		$(img).appendTo(li);
	});
	li.click(function(){
		var svgs = [];
		im.hideSlideOut();
		$.each(shape.paths,function(e,path) {
			var svg = new Kinetic.Path({
				data:path.path,
				fill:path.fill || "#000"
			});
			if (e > 0) {
				svg.setListening(false);
			}
			svgs.push(svg);
			svg.setSize([200,200]);
			im.addElement(svg,'svg');
		});
		svgs[0].startSize = {width:200,height:200};
		svgs[0].childShapes = svgs.slice(1);
		svgs[0].parent._setattr = svgs[0].setAttr;
		svgs[0].parent.setAttr = function(k,v) {
			for (i in svgs[0].childShapes) {
				svgs[0].childShapes[i].parent.setAttr(k,v);
			}
			this._setattr(k,v);
		};
		svgs[0]._setattr = svgs[0].setAttr;
		svgs[0].setAttr = function(k,v) {
			if (k == 'width') {
				var s = this.getScale();
				this.setScale({x:v/200,y:s.y});
			}
			if (k == 'height') {
				var s = this.getScale();
				this.setScale({y:v/200,x:s.x});
			}
			for (i in this.childShapes) {
				this.childShapes[i].setAttr(k,v);
			}
			this._setattr(k,v);
		};
		var dfunc = svgs[0].getDrawFunc();
		svgs[0].setDrawFunc(function(canvas){
			var e;
			for (e in this.childShapes) {
				this.childShapes[e].parent.draw();
			}
			dfunc.call(this,canvas);
		});
		im.setActiveElement(svgs[0]);
	});
});

im.bind('changeActiveComponent',function(e, data){
	if (data == im.namespace) {
		im.fire("selected", data, elem);
		im.selected = true;
	} else {
		im.hideSlideOut();
		im.selected = false;
	}
});

im.bind("selected",function(e){
	im.showSlideOut(ul.clone(1));
},elem);
