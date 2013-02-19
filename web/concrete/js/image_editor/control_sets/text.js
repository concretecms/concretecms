var me = $(this);
me.parent().parent().hide();
im.active = false;
im.bind('changeActiveElement',function(e){
	if (im.activeElement.elementType != "text") {
		me.parent().parent().slideUp();
		im.active = false;
	} else {
		me.parent().parent().slideDown();
		colorInput.val(im.activeElement.textElement.getFill());
		slider.slider('option','value',im.activeElement.textElement.getFontSize());
		im.trigger('textElementChange');
		im.active = true;
	}
});

// Color Input
var colorInput = $('p.color input',me);
colorInput.ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		colorInput.val(hex);
		im.activeElement.textElement.setFill('#'+hex);
		im.activeElement.draw();
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
}).bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
	im.activeElement.textElement.setFill('#'+this.value);
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
	if (e.keyCode == 8) {
		im.activeElement.textElement.setFontSize(sliderText.val());
		slider.slider('option','value',im.activeElement.textElement.getFontSize());
		im.trigger('textElementChange');
	}
});
im.bind('textElementChange',function(e){
	if (!im.active) return;
	im.trigger('textChange');
	$('div.fontSize > input',me).val(im.activeElement.textElement.getFontSize());
});
