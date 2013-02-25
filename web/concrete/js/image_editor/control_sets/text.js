var me = $(this);
me.parent().parent().hide();
im.active = false;

im.bind('changeActiveAction',function(e){
	if (e.eventData == im.namespace) {
		im.fillData();
		im.active = true;
	} else {
		im.active = false;
	}
})
im.bind('changeActiveElement',function(e){
	if (im.activeElement.elementType != "text") {
		me.parent().parent().slideUp();
	} else {
		im.fillData()
		me.parent().parent().slideDown();
		im.trigger('textElementChange');
	}
});

// Color Input
var colorInput = $('p.color input',me);
colorInput.keyup(function(){
	im.activeElement.textElement.setFill('#'+this.value);
});
colorInput.ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		colorInput.val(hex);
		im.activeElement.textElement.setFill('#'+hex);
		im.activeElement.draw();
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
})
colorInput.bind('keyup', function(){
	$(this).ColorPickerSetColor($(this).val());
	im.activeElement.textElement.setFill('#'+$(this).val());
	im.activeElement.draw();
});

// Font Size
var slider = $('div.fontSize > div',me), sliderText = $('div.fontSize > input',me);
slider.slider({
	step: 1,
	range: "min",
	min:4,
	max:72,
	value:0,
	animate: true,
	slide: function(ev,e){
		if (!im.active) return;
		im.activeElement.textElement.setFontSize(e.value);
		sliderText.val(im.activeElement.textElement.getFontSize());
		im.trigger('textElementChange');
		im.activeElement.parent.draw();
	}
});

// Font
var fontInput = $('div.font input',me);
fontInput.keyup(function(){
	im.activeElement.textElement.setFontFamily(fontInput.val().trim() || 'arial');
	im.activeElement.draw();
	im.trigger('textElementChange');
});

// Value
var valueInput = $('div.value input',me);
valueInput.keyup(function(){
	im.activeElement.setText(valueInput.val());
	im.activeElement.draw();
	im.trigger('textElementChange');
});

// Size
sliderText.keypress(function(e){
	if (String.fromCharCode(e.keyCode).match(/\d/)) {
		var v = sliderText.val() + "" + String.fromCharCode(e.keyCode);
		im.activeElement.textElement.setFontSize(v);
		slider.slider('option','value',im.activeElement.textElement.getFontSize());
		im.trigger('textElementChange');
		return false;
	}
	return false;
});
sliderText.keyup(function(e){
	im.activeElement.textElement.setFontSize(sliderText.val());
	slider.slider('option','value',im.activeElement.textElement.getFontSize());
	im.activeElement.draw();
	im.trigger('textElementChange');

});
im.bind('textElementChange',function(e){
	if (!im.active) return;
	im.trigger('textChange');
	$('div.fontSize > input',me).val(im.activeElement.textElement.getFontSize());
});

im.fillData = function() {
	if (im.activeElement.elementType != 'text') return;

	colorInput.val(im.activeElement.textElement.getFill());
	slider.slider('option','value',im.activeElement.textElement.getFontSize());
	sliderText.val(im.activeElement.textElement.getFontSize());
	fontInput.val(im.activeElement.textElement.getFontFamily());
	valueInput.val(im.activeElement.textElement.getText());
};


