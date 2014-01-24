var me = $(this);

im.activeTextElement = false;
im.on('changeActiveComponent',function(e, data){
	if (data != im.namespace) return im.hideSlideOut();
	im.activeTextElement = new Kinetic.Text({
		fontFamily:"arial",
		fontSize:23,
		fill:'#333333',
		x:0,
		y:0
	});
	im.activeTextElement.on('BEFORETEXTCHANGE',function(){
		alert('textchange');
	});
	im.addElement(im.activeTextElement,'text');
	im.setActiveElement(im.activeTextElement);

	var defFont = 'Open Sans';
	im.activeTextElement.setFontFamily(defFont);
	im.activeTextElement.setFontSize(22);
	im.activeTextElement.setPadding(5);
	$('textarea',me).val('');
	$('button.active',me).removeClass('active');
	$('button.fontname').css('font-family',defFont).text(defFont);
	colorButton.css('background','#333333');
});

var me = $(this);
String.prototype.spx = function(){return Number(this.replace('px',''))}
var fonts = $($.parseHTML($('script.font-slideout',me).html()));
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
	border:'solid 1px #666',
	"margin-top":-136
}).find('.colorpicker_new_color').css({
	width:82,
	height:150,
	top:13,
	left:180
}).end().append(buttonDiv);

buttonDiv.css({
	background:'#f5f5f5',
	'border-top':'solid 1px #ccc',
	height:50,
	width:'100%',
	top:228,
	position:'absolute',
});
var cancelButton = $('<button/>').text('Cancel').click(function(){
	colorPicker.hide();
}).css({
	color:'#666',
	"font-weight":"bold",
	float:'left',
	background:'none',
	border:'none',
	'margin-top':13,
	padding:'0 20px'
});
var okayButton = $('<button/>').text('Apply').click(function(){
	colorPicker.hide();
	currentColor = "#"+hextext.val();
	colorButton.css('background',currentColor);
	im.activeTextElement.setFill(currentColor);
	im.activeTextElement.parent.draw();
}).css({
	color:'#0099ff',
	"font-weight":"bold",
	float:'right',
	background:'none',
	border:'none',
	'margin-top':13,
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
	im.activeTextElement.setOffset([im.activeTextElement.getWidth()/2,im.activeTextElement.getHeight()/2]);
	im.activeTextElement.parent.setOffset([-im.activeTextElement.getWidth()/2,-im.activeTextElement.getHeight()/2]);
	im.activeTextElement.parent.draw();
});

$('div.alignment',me).children('button').click(function(){
	im.activeTextElement.setAlign($(this).attr('data-alignment'));
	im.activeTextElement.parent.draw();
});
$('div.style',me).children('button').click(function(){
	if ($(this).hasClass('active')) $(this).removeClass('active');
	else $(this).addClass('active');
	var style = [];
	if ($(this).parent().children('[data-style="bold"]').hasClass('active')) {
		style.push('bold');
	}
	if ($(this).parent().children('[data-style="italic"]').hasClass('active')) {
		style.push('italic');
	}
	console.log(style.join(' '));
	im.activeTextElement.setFontStyle(style.join(' '));
	im.activeTextElement.parent.draw();
	return false;
});


$('div.sizeSlider',me).find('div.slider').slider({
	min:10,
	max:400,
	value:22,
	slide:function(e,ui){
		$('div.sizeSlider',me).find('input').val(ui.value);
		im.activeTextElement.setFontSize(ui.value);
		im.activeTextElement.parent.draw();
	},
	change:function(e,ui){
		im.activeTextElement.setFontSize(ui.value);
		im.activeTextElement.parent.draw();
	}
}).end().find('input').val(22).keyup(function(){
	var val = parseInt($(this).val());
	if (isNaN(val)) val=0;
	$('div.sizeSlider',me).find('div.slider').slider("value",val);
	$(this).val(val);
});

$('div.lineHeightSlider',me).find('div.slider').slider({
	min:0,
	max:200,
	value:100,
	slide:function(e,ui){
		$('div.lineHeightSlider',me).find('input').val(ui.value/100);
		im.activeTextElement.setLineHeight(ui.value/100);
		im.activeTextElement.parent.draw();
	},
	change:function(e,ui){
		im.activeTextElement.setLineHeight(ui.value/100);
		im.activeTextElement.parent.draw();
	}
}).end().find('input').val(0).keyup(function(){
	var val = Number($(this).val());
	if (isNaN(val)) val=1;
	$('div.lineHeightSlider',me).find('div.slider').slider("value",val);
	$(this).val(val);
});
