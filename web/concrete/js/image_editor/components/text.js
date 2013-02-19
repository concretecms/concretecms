var changeTextEvent = 'textChange';

im.selected = false;
im.bind('changeActiveComponent',function(e){
	if (e.eventData == im.namespace) {
		if (im.selected) return;
		im.selected = true;
		im.textArea.val('');
		im.text = new Kinetic.Text({
			x:0,
			y:0,
			text:'',
			fontFamily:'arial',
			fill:'black',
			detectionType:'path'
		});
		im.textDragger = new Kinetic.Rect({
			x:0,
			y:0,
			height:0,
			width:0
		});
		im.textGroup = new Kinetic.Group();
		im.textGroup.add(im.textDragger);
		im.textGroup.add(im.text);

		im.textGroup.textElement = im.text;
		im.textGroup.textDragElement = im.textDragger;

		im.textGroup.setText = function(text) {
			this.textElement.setText(text);
			im.fire(changeTextEvent);
		};
		im.textGroup._draw = im.textGroup.draw;
		im.textGroup.draw = function() {
			this.parent.draw();
		};

		im.textGroup.getWidth = function() {
			return this.textElement.getWidth();
		};
		im.textGroup.getHeight = function() {
			return this.textElement.getHeight();
		};

		im.text.setFontSize(15);
		im.addElement(im.textGroup,'text');
		im.setActiveElement(im.textGroup);
	} else {
		im.selected = false;
	}
});

var me = $(this);
im.textArea = $('textarea',me);

im.textArea.keyup(function(){im.fire(changeTextEvent)}); // Use our api.

im.bind(changeTextEvent,function(e){
	console.log('textchanged',im.textArea.val());
	im.text.setText(im.textArea.val());
	im.textDragger.setWidth(im.text.getWidth());
	im.textDragger.setHeight(im.text.getHeight());
	im.textDragger.setX(im.text.getX());
	im.textDragger.setY(im.text.getY());
	im.text.parent.draw();
});