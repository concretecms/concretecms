var me = $(this);


im.activeTextElement = false;
im.on('changeActiveComponent',function(e){
	if (e.eventData != im.namespace) return;
	im.activeTextElement = new Kinetic.Text({
		fontFamily:"arial",
		fontSize:23,
		fill:'#333333',
		x:0,
		y:0
	});
	im.addElement(im.activeTextElement);
	im.activeTextElement.doppelganger = new Kinetic.Rect({});
	im.activeTextElement.doppelganger.doppelganger = im.activeTextElement;

	im.setActiveElement(im.activeTextElement);

	$('textarea',me).val('');
});

var me = $(this);
String.prototype.spx = function(){return Number(this.replace('px',''))}
var fonts = $($('script.font-slideout',me).html());
fonts.find('li').css('cursor','pointer').click(function(){
	$('button.fontname').css('font-family',$(this).text()).text($(this).text());
	im.hideSlideOut();
	im.activeTextElement.setFontFamily($(this).text());
	im.activeTextElement.parent.draw();
});

me.find('button.fontname').click(function(e){
	if (im.slideOut.hasClass('active')) return im.hideSlideOut();
	im.showSlideOut(fonts.clone(1));
});
var colorButton = me.find('button.color');
colorButton.ColorPicker();
var colorPicker = im.colorPicker = $('#'+colorButton.data('colorpickerId'));
var cpo = colorPicker.data('colorpicker');

colorPicker.find('.colorpicker_current_color').hide().end()
		   .find('.colorpicker_rgb_r').hide().end()
		   .find('.colorpicker_rgb_g').hide().end()
		   .find('.colorpicker_rgb_b').hide().end()
		   .find('.colorpicker_hsb_h').hide().end()
		   .find('.colorpicker_hsb_s').hide().end()
		   .find('.colorpicker_hsb_b').hide().end()
		   .find('.colorpicker_hex').hide().end()
		   .find('.colorpicker_hue').hide();
var huehandle = cpo.hue;
var buttonDiv = $('<div/>');
colorPicker.addClass('ccm-ui').css({
	width:275,
	height:272,
	'background-image':'none',
	background:'white',
	'border-radius':'5px',
	border:'solid 1px #333',
	top:'-=136'
}).find('.colorpicker_new_color').css({
	width:82,
	height:150,
	top:13,
	left:180
}).end().append(buttonDiv);

buttonDiv.css({
	background:'black',
	height:60,
	width:'100%',
	top:218,
	position:'absolute',
});
var cancelButton = $('<button/>').text('Cancel').click(function(){
	colorPicker.hide();
}).css({
	color:'#aaa',
	float:'left',
	background:'none',
	border:'none',
	'margin-top':18,
	padding:'0 20px'
});
var okayButton = $('<button/>').text('Apply').click(function(){
	colorPicker.hide();
	currentColor = "#"+hextext.val();
	colorButton.css('background',currentColor);
	im.activeTextElement.setFill(currentColor);
	im.activeTextElement.parent.draw();
}).css({
	color:'#fff',
	float:'right',
	background:'none',
	border:'none',
	'margin-top':18,
	padding:'0 20px'
});
buttonDiv.append(cancelButton).append(okayButton);
var hextext = colorPicker.find('.colorpicker_hex input.text');
hextext.removeClass('text').css({
	width:69,
	'text-align':'center',
	left:180,
	position:'absolute',
	top:176
});
colorPicker.append(hextext);

var hueslider = $('<div/>').css({
	background:'url(/concrete/images/widgets/colorpicker/custom_horizontal_background.png) no-repeat',
	'background-position':'0px',
	width:154,
	height:35,
	left:14,
	top:174,
	position:'absolute'
}),
slider = $('<div/>').css({
	width:9,
	height:35,
	background:'url(/concrete/images/widgets/colorpicker/custom_indic_horizontal.gif)',
	overflow:'hidden',
	position:'absolute',
	'margin-left':-4
}).appendTo(hueslider);
colorPicker.append(hueslider);

var sliderSliding = false,sliderClientStart,sliderStart;
slider.draggable({ 
	containment: "parent",
	drag:function(ev){
		cpo.color.h = -(12/5)*(154-(""+slider.css('left')).spx() - 154);
		cpo.change.apply(
			cpo.fields
				.eq(4)
				.val(cpo.color.h)
				.get(0),
			[]
		);
	}
});

var currentColor = "#333333";

cpo.onShow = function(elem,a) {
	colorPicker.hide();
	im.hideSlideOut();
	colorPicker.show();
	var r = im.slideOut.css('right');
	r = r.spx();
	colorPicker.css({
		left: 'auto',
		right: Number(r) + 100
	});
	colorButton.ColorPickerSetColor(currentColor);
};












$('textarea',me).keyup(function(){
	im.activeTextElement.setText($(this).val());
	im.activeTextElement.parent.draw();
});