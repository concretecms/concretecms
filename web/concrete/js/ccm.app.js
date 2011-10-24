/* ==========================================================
 * bootstrap-twipsy.js v1.3.0
 * http://twitter.github.com/bootstrap/javascript.html#twipsy
 * Adapted from the original jQuery.tipsy by Jason Frame
 * ==========================================================
 * Copyright 2011 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function( $ ) {

 /* CSS TRANSITION SUPPORT (https://gist.github.com/373874)
  * ======================================================= */

  var transitionEnd

  $(document).ready(function () {

    $.support.transition = (function () {
      var thisBody = document.body || document.documentElement
        , thisStyle = thisBody.style
        , support = thisStyle.transition !== undefined || thisStyle.WebkitTransition !== undefined || thisStyle.MozTransition !== undefined || thisStyle.MsTransition !== undefined || thisStyle.OTransition !== undefined
      return support
    })()

    // set CSS transition event type
    if ( $.support.transition ) {
      transitionEnd = "TransitionEnd"
      if ( $.browser.webkit ) {
      	transitionEnd = "webkitTransitionEnd"
      } else if ( $.browser.mozilla ) {
      	transitionEnd = "transitionend"
      } else if ( $.browser.opera ) {
      	transitionEnd = "oTransitionEnd"
      }
    }

  })


 /* TWIPSY PUBLIC CLASS DEFINITION
  * ============================== */

  var Twipsy = function ( element, options ) {
    this.$element = $(element)
    this.options = options
    this.enabled = true
    this.fixTitle()
  }

  Twipsy.prototype = {

    show: function() {
      var pos
        , actualWidth
        , actualHeight
        , placement
        , $tip
        , tp

      if (this.getTitle() && this.enabled) {
        $tip = this.tip()
        this.setContent()

        if (this.options.animate) {
          $tip.addClass('fade')
        }
		
		if ($("#twipsy-holder").length == 0) {
			$('<div />').attr('id','twipsy-holder').attr('class', 'ccm-ui').prependTo(document.body);
		}
		
        $tip
          .remove()
          .css({ top: 0, left: 0, display: 'block' })
          .prependTo($("#twipsy-holder"))

        pos = $.extend({}, this.$element.offset(), {
          width: this.$element[0].offsetWidth
        , height: this.$element[0].offsetHeight
        })

        actualWidth = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight
        placement = _.maybeCall(this.options.placement, this.$element[0])

        switch (placement) {
          case 'below':
            tp = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'above':
            tp = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth - this.options.offset}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset}
            break
        }

        $tip
          .css(tp)
          .addClass(placement)
          .addClass('in')
      }
    }

  , setContent: function () {
      var $tip = this.tip()
      $tip.find('.twipsy-inner')[this.options.html ? 'html' : 'text'](this.getTitle())
      $tip[0].className = 'twipsy'
    }

  , hide: function() {
      var that = this
        , $tip = this.tip()

      $tip.removeClass('in')

      function removeElement () {
        $tip.remove()
      }

      removeElement()
    }

  , fixTitle: function() {
      var $e = this.$element
      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
        $e.attr('data-original-title', $e.attr('title') || '').removeAttr('title')
      }
    }

  , getTitle: function() {
      var title
        , $e = this.$element
        , o = this.options

        this.fixTitle()

        if (typeof o.title == 'string') {
          title = $e.attr(o.title == 'title' ? 'data-original-title' : o.title)
        } else if (typeof o.title == 'function') {
          title = o.title.call($e[0])
        }

        title = ('' + title).replace(/(^\s*|\s*$)/, "")

        return title || o.fallback
    }

  , tip: function() {
      if (!this.$tip) {
        this.$tip = $('<div class="twipsy" />').html('<div class="twipsy-arrow"></div><div class="twipsy-inner"></div>')
      }
      return this.$tip
    }

  , validate: function() {
      if (!this.$element[0].parentNode) {
        this.hide()
        this.$element = null
        this.options = null
      }
    }

  , enable: function() {
      this.enabled = true
    }

  , disable: function() {
      this.enabled = false
    }

  , toggleEnabled: function() {
      this.enabled = !this.enabled
    }

  }


 /* TWIPSY PRIVATE METHODS
  * ====================== */

   var _ = {

     maybeCall: function ( thing, ctx ) {
       return (typeof thing == 'function') ? (thing.call(ctx)) : thing
     }

   }


 /* TWIPSY PLUGIN DEFINITION
  * ======================== */

  $.fn.twipsy = function (options) {
    $.fn.twipsy.initWith.call(this, options, Twipsy, 'twipsy')
    return this
  }

  $.fn.twipsy.initWith = function (options, Constructor, name) {
    var twipsy
      , binder
      , eventIn
      , eventOut

    if (options === true) {
      return this.data(name)
    } else if (typeof options == 'string') {
      twipsy = this.data(name)
      if (twipsy) {
        twipsy[options]()
      }
      return this
    }

    options = $.extend({}, $.fn[name].defaults, options)

    function get(ele) {
      var twipsy = $.data(ele, name)

      if (!twipsy) {
        twipsy = new Constructor(ele, $.fn.twipsy.elementOptions(ele, options))
        $.data(ele, name, twipsy)
      }

      return twipsy
    }

    function enter() {
      var twipsy = get(this)
      twipsy.hoverState = 'in'

      if (options.delayIn == 0) {
        twipsy.show()
      } else {
        twipsy.fixTitle()
        setTimeout(function() {
          if (twipsy.hoverState == 'in') {
            twipsy.show()
          }
        }, options.delayIn)
      }
    }

    function leave() {
      var twipsy = get(this)
      twipsy.hoverState = 'out'
      if (options.delayOut == 0) {
        twipsy.hide()
      } else {
        setTimeout(function() {
          if (twipsy.hoverState == 'out') {
            twipsy.hide()
          }
        }, options.delayOut)
      }
    }

    if (!options.live) {
      this.each(function() {
        get(this)
      })
    }

    if (options.trigger != 'manual') {
      binder   = options.live ? 'live' : 'bind'
      eventIn  = options.trigger == 'hover' ? 'mouseenter' : 'focus'
      eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur'
      this[binder](eventIn, enter)[binder](eventOut, leave)
    }

    return this
  }

  $.fn.twipsy.Twipsy = Twipsy

  $.fn.twipsy.defaults = {
    animate: true
  , delayIn: 0
  , delayOut: 0
  , fallback: ''
  , placement: 'above'
  , html: false
  , live: false
  , offset: 0
  , title: 'title'
  , trigger: 'hover'
  }

  $.fn.twipsy.elementOptions = function(ele, options) {
    return $.metadata ? $.extend({}, options, $(ele).metadata()) : options
  }

}( window.jQuery || window.ender );
/* ===========================================================
 * bootstrap-popover.js v1.3.0
 * http://twitter.github.com/bootstrap/javascript.html#popover
 * ===========================================================
 * Copyright 2011 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * =========================================================== */


!function( $ ) {

  var Popover = function ( element, options ) {
    this.$element = $(element)
    this.options = options
    this.enabled = true
    this.fixTitle()
  }

  /* NOTE: POPOVER EXTENDS BOOTSTRAP-TWIPSY.js
     ========================================= */

  Popover.prototype = $.extend({}, $.fn.twipsy.Twipsy.prototype, {

    setContent: function () {
      var $tip = this.tip()
      $tip.find('.title')[this.options.html ? 'html' : 'text'](this.getTitle())
      $tip.find('.content p')[this.options.html ? 'html' : 'text'](this.getContent())
      $tip[0].className = 'popover'
    }

  , getContent: function () {
      var content
       , $e = this.$element
       , o = this.options

      if (typeof this.options.content == 'string') {
        content = $e.attr(o.content)
      } else if (typeof this.options.content == 'function') {
        content = this.options.content.call(this.$element[0])
      }
      return content
    }

  , tip: function() {
      if (!this.$tip) {
        this.$tip = $('<div class="popover" />')
          .html('<div class="arrow"></div><div class="inner"><h3 class="title"></h3><div class="content"><p></p></div></div>')
      }
      return this.$tip
    }

  })


 /* POPOVER PLUGIN DEFINITION
  * ======================= */

  $.fn.popover = function (options) {
    if (typeof options == 'object') options = $.extend({}, $.fn.popover.defaults, options)
    $.fn.twipsy.initWith.call(this, options, Popover, 'popover')
    return this
  }

  $.fn.popover.defaults = $.extend({} , $.fn.twipsy.defaults, { content: 'data-content', placement: 'right'})

}( window.jQuery || window.ender );
/**
 *
 * Color picker
 * Author: Stefan Petre www.eyecon.ro
 * 
 */
(function ($) {
	var ColorPicker = function () {
		var
			ids = {},
			inAction,
			charMin = 65,
			visible,
			tpl = '<div class="colorpicker"><div class="colorpicker_color"><div><div></div></div></div><div class="colorpicker_hue"><div></div></div><div class="colorpicker_new_color"></div><div class="colorpicker_current_color"></div><div class="colorpicker_hex"><input type="text" class="text" maxlength="6" size="6" /></div><div class="colorpicker_rgb_r colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_g colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_b colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_h colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_s colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_b colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><input type="button" class="colorpicker_none" name="none" value="' + ccmi18n.x + '" /><input type="button" class="colorpicker_submit" name="save" value="' + ccmi18n.ok + '" /></div>',
			
			defaults = {
				eventName: 'click',
				onShow: function () {},
				onBeforeShow: function(){},
				onHide: function () {},
				onNone: function () {},
				onChange: function () {},
				onSubmit: function () {},
				color: 'ff0000',
				livePreview: true,
				flat: false
			},
			fillRGBFields = function  (hsb, cal) {
				var rgb = HSBToRGB(hsb);
				$(cal).data('colorpicker').fields
					.eq(1).val(rgb.r).end()
					.eq(2).val(rgb.g).end()
					.eq(3).val(rgb.b).end();
			},
			fillHSBFields = function  (hsb, cal) {
				$(cal).data('colorpicker').fields
					.eq(4).val(hsb.h).end()
					.eq(5).val(hsb.s).end()
					.eq(6).val(hsb.b).end();
			},
			fillHexFields = function (hsb, cal) {
				$(cal).data('colorpicker').fields
					.eq(0).val(HSBToHex(hsb)).end();
			},
			setSelector = function (hsb, cal) {
				$(cal).data('colorpicker').selector.css('backgroundColor', '#' + HSBToHex({h: hsb.h, s: 100, b: 100}));
				$(cal).data('colorpicker').selectorIndic.css({
					left: parseInt(150 * hsb.s/100, 10),
					top: parseInt(150 * (100-hsb.b)/100, 10)
				});
			},
			setHue = function (hsb, cal) {
				$(cal).data('colorpicker').hue.css('top', parseInt(150 - 150 * hsb.h/360, 10));
			},
			setCurrentColor = function (hsb, cal) {
				$(cal).data('colorpicker').currentColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			setNewColor = function (hsb, cal) {
				$(cal).data('colorpicker').newColor.css('backgroundColor', '#' + HSBToHex(hsb));
			},
			keyDown = function (ev) {
				var pressedKey = ev.charCode || ev.keyCode || -1;
				if ((pressedKey > charMin && pressedKey <= 90) || pressedKey == 32) {
					return false;
				}
				var cal = $(this).parent().parent();
				if (cal.data('colorpicker').livePreview === true) {
					change.apply(this);
				}
			},
			change = function (ev) {
				var cal = $(this).parent().parent(), col; 
				
				if(!cal.data('colorpicker') || !cal.data('colorpicker').fields) return; 
				
				if (this.parentNode.className.indexOf('_hex') > 0) {
					cal.data('colorpicker').color = col = HexToHSB(fixHex(this.value));
				} else if (this.parentNode.className.indexOf('_hsb') > 0) {
					cal.data('colorpicker').color = col = fixHSB({
						h: parseInt(cal.data('colorpicker').fields.eq(4).val(), 10),
						s: parseInt(cal.data('colorpicker').fields.eq(5).val(), 10),
						b: parseInt(cal.data('colorpicker').fields.eq(6).val(), 10)
					});
				} else {
					cal.data('colorpicker').color = col = RGBToHSB(fixRGB({
						r: parseInt(cal.data('colorpicker').fields.eq(1).val(), 10),
						g: parseInt(cal.data('colorpicker').fields.eq(2).val(), 10),
						b: parseInt(cal.data('colorpicker').fields.eq(3).val(), 10)
					}));
				}
				if (ev) {
					fillRGBFields(col, cal.get(0));
					fillHexFields(col, cal.get(0));
					fillHSBFields(col, cal.get(0));
				}
				setSelector(col, cal.get(0));
				setHue(col, cal.get(0));
				setNewColor(col, cal.get(0));
				cal.data('colorpicker').onChange.apply(cal, [col, HSBToHex(col), HSBToRGB(col)]);
			},
			blur = function (ev) {
				var cal = $(this).parent().parent();
				var colorpicker = cal.data('colorpicker')
				if(colorpicker && colorpicker.fields) 
					colorpicker.fields.parent().removeClass('colorpicker_focus')
			},
			focus = function () {
				charMin = this.parentNode.className.indexOf('_hex') > 0 ? 70 : 65;
				//alert(this.parentNode.innerHTML+' '+this.parentNode.id);
				var colorpicker = $(this).parent().parent().data('colorpicker')
				if(colorpicker && colorpicker.fields) 
					colorpicker.fields.parent().removeClass('colorpicker_focus');
				$(this).parent().addClass('colorpicker_focus');
			},
			downIncrement = function (ev) {
				var field = $(this).parent().find('input').focus();
				var current = {
					el: $(this).parent().addClass('colorpicker_slider'),
					max: this.parentNode.className.indexOf('_hsb_h') > 0 ? 360 : (this.parentNode.className.indexOf('_hsb') > 0 ? 100 : 255),
					y: ev.pageY,
					field: field,
					val: parseInt(field.val(), 10),
					preview: $(this).parent().parent().data('colorpicker').livePreview					
				};
				$(document).bind('mouseup', current, upIncrement);
				$(document).bind('mousemove', current, moveIncrement);
			},
			moveIncrement = function (ev) {
				ev.data.field.val(Math.max(0, Math.min(ev.data.max, parseInt(ev.data.val + ev.pageY - ev.data.y, 10))));
				if (ev.data.preview) {
					change.apply(ev.data.field.get(0), [true]);
				}
				return false;
			},
			upIncrement = function (ev) {
				change.apply(ev.data.field.get(0), [true]);
				ev.data.el.removeClass('colorpicker_slider').find('input').focus();
				$(document).unbind('mouseup', upIncrement);
				$(document).unbind('mousemove', moveIncrement);
				return false;
			},
			downHue = function (ev) {
				var current = {
					cal: $(this).parent(),
					y: $(this).offset().top
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upHue);
				$(document).bind('mousemove', current, moveHue);
			},
			moveHue = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(4)
						.val(parseInt(360*(150 - Math.max(0,Math.min(150,(ev.pageY - ev.data.y))))/150, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upHue = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upHue);
				$(document).unbind('mousemove', moveHue);
				return false;
			},
			downSelector = function (ev) {
				var current = {
					cal: $(this).parent(),
					pos: $(this).offset()
				};
				current.preview = current.cal.data('colorpicker').livePreview;
				$(document).bind('mouseup', current, upSelector);
				$(document).bind('mousemove', current, moveSelector);
			},
			moveSelector = function (ev) {
				change.apply(
					ev.data.cal.data('colorpicker')
						.fields
						.eq(6)
						.val(parseInt(100*(150 - Math.max(0,Math.min(150,(ev.pageY - ev.data.pos.top))))/150, 10))
						.end()
						.eq(5)
						.val(parseInt(100*(Math.max(0,Math.min(150,(ev.pageX - ev.data.pos.left))))/150, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upSelector = function (ev) {
				fillRGBFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colorpicker').color, ev.data.cal.get(0));
				$(document).unbind('mouseup', upSelector);
				$(document).unbind('mousemove', moveSelector);
				return false;
			},
			enterSubmit = function (ev) {
				$(this).addClass('colorpicker_focus');
			},
			leaveSubmit = function (ev) {
				$(this).removeClass('colorpicker_focus');
			},
			clickSubmit = function (ev) {
				var cal = $(this).parent();
				var col = cal.data('colorpicker').color;
				cal.data('colorpicker').origColor = col;
				setCurrentColor(col, cal.get(0));
				var cal2 = $('#' + $(this).data('colorpickerId'));
				cal.data('colorpicker').onSubmit(col, HSBToHex(col), HSBToRGB(col), cal);
			},
			clickNone = function (ev) {  
				var cal = $(this).parent();
				cal.data('colorpicker').onNone(cal);
				cal.hide(); 
			},			
			show = function (ev) {
				var cal = $('#' + $(this).data('colorpickerId'));
				cal.data('colorpicker').onBeforeShow.apply(this, [cal.get(0)]);
				var pos = $(this).offset();
				var viewPort = getViewport();
				var top = pos.top + this.offsetHeight;
				var left = pos.left;
				if (top + 176 > viewPort.t + viewPort.h) {
					top -= this.offsetHeight + 176;
				}
				if (left + 356 > viewPort.l + viewPort.w) {
					left -= 356;
				}
				cal.css({left: left + 'px', top: top + 'px'});
				if (cal.data('colorpicker').onShow.apply(this, [cal.get(0)]) != false) {
					cal.show();
				}
				$(document).bind('mousedown', {cal: cal}, hide);
				return false;
			},
			hide = function (ev) {
				if (!isChildOf(ev.data.cal.get(0), ev.target, ev.data.cal.get(0))) {
					if (ev.data.cal.data('colorpicker').onHide.apply(this, [ev.data.cal.get(0)]) != false) {
						ev.data.cal.hide();
					}
					$(document).unbind('mousedown', hide);
				}
			},
			isChildOf = function(parentEl, el, container) {
				if (parentEl == el) {
					return true;
				}
				if (parentEl.contains) {
					return parentEl.contains(el);
				}
				if ( parentEl.compareDocumentPosition ) {
					return !!(parentEl.compareDocumentPosition(el) & 16);
				}
				var prEl = el.parentNode;
				while(prEl && prEl != container) {
					if (prEl == parentEl)
						return true;
					prEl = prEl.parentNode;
				}
				return false;
			},
			getViewport = function () {
				var m = document.compatMode == 'CSS1Compat';
				return {
					l : window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft),
					t : window.pageYOffset || (m ? document.documentElement.scrollTop : document.body.scrollTop),
					w : window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth),
					h : window.innerHeight || (m ? document.documentElement.clientHeight : document.body.clientHeight)
				};
			},
			fixHSB = function (hsb) {
				return {
					h: Math.min(360, Math.max(0, hsb.h)),
					s: Math.min(100, Math.max(0, hsb.s)),
					b: Math.min(100, Math.max(0, hsb.b))
				};
			}, 
			fixRGB = function (rgb) {
				return {
					r: Math.min(255, Math.max(0, rgb.r)),
					g: Math.min(255, Math.max(0, rgb.g)),
					b: Math.min(255, Math.max(0, rgb.b))
				};
			},
			fixHex = function (hex) {
				var len = 6 - hex.length;
				if (len > 0) {
					var o = [];
					for (var i=0; i<len; i++) {
						o.push('0');
					}
					o.push(hex);
					hex = o.join('');
				}
				return hex;
			}, 
			HexToRGB = function (hex) {
				var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
				return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
			},
			HexToHSB = function (hex) {
				return RGBToHSB(HexToRGB(hex));
			},
			RGBToHSB = function (rgb) {
				var hsb = {};
				hsb.b = Math.max(Math.max(rgb.r,rgb.g),rgb.b);
				hsb.s = (hsb.b <= 0) ? 0 : Math.round(100*(hsb.b - Math.min(Math.min(rgb.r,rgb.g),rgb.b))/hsb.b);
				hsb.b = Math.round((hsb.b /255)*100);
				if((rgb.r==rgb.g) && (rgb.g==rgb.b)) hsb.h = 0;
				else if(rgb.r>=rgb.g && rgb.g>=rgb.b) hsb.h = 60*(rgb.g-rgb.b)/(rgb.r-rgb.b);
				else if(rgb.g>=rgb.r && rgb.r>=rgb.b) hsb.h = 60  + 60*(rgb.g-rgb.r)/(rgb.g-rgb.b);
				else if(rgb.g>=rgb.b && rgb.b>=rgb.r) hsb.h = 120 + 60*(rgb.b-rgb.r)/(rgb.g-rgb.r);
				else if(rgb.b>=rgb.g && rgb.g>=rgb.r) hsb.h = 180 + 60*(rgb.b-rgb.g)/(rgb.b-rgb.r);
				else if(rgb.b>=rgb.r && rgb.r>=rgb.g) hsb.h = 240 + 60*(rgb.r-rgb.g)/(rgb.b-rgb.g);
				else if(rgb.r>=rgb.b && rgb.b>=rgb.g) hsb.h = 300 + 60*(rgb.r-rgb.b)/(rgb.r-rgb.g);
				else hsb.h = 0;
				hsb.h = Math.round(hsb.h);
				return hsb;
			},
			HSBToRGB = function (hsb) {
				var rgb = {};
				var h = Math.round(hsb.h);
				var s = Math.round(hsb.s*255/100);
				var v = Math.round(hsb.b*255/100);
				if(s == 0) {
					rgb.r = rgb.g = rgb.b = v;
				} else {
					var t1 = v;
					var t2 = (255-s)*v/255;
					var t3 = (t1-t2)*(h%60)/60;
					if(h==360) h = 0;
					if(h<60) {rgb.r=t1;	rgb.b=t2; rgb.g=t2+t3}
					else if(h<120) {rgb.g=t1; rgb.b=t2;	rgb.r=t1-t3}
					else if(h<180) {rgb.g=t1; rgb.r=t2;	rgb.b=t2+t3}
					else if(h<240) {rgb.b=t1; rgb.r=t2;	rgb.g=t1-t3}
					else if(h<300) {rgb.b=t1; rgb.g=t2;	rgb.r=t2+t3}
					else if(h<360) {rgb.r=t1; rgb.g=t2;	rgb.b=t1-t3}
					else {rgb.r=0; rgb.g=0;	rgb.b=0}
				}
				return {r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b)};
			},
			RGBToHex = function (rgb) {
				var hex = [
					rgb.r.toString(16),
					rgb.g.toString(16),
					rgb.b.toString(16)
				];
				$.each(hex, function (nr, val) {
					if (val.length == 1) {
						hex[nr] = '0' + val;
					}
				});
				return hex.join('');
			},
			HSBToHex = function (hsb) {
				return RGBToHex(HSBToRGB(hsb));
			};
		return {
			init: function (options) {
				options = $.extend({}, defaults, options||{});
				if (typeof options.color == 'string') {
					options.color = HexToHSB(options.color);
				} else if (options.color.r != undefined && options.color.g != undefined && options.color.b != undefined) {
					options.color = RGBToHSB(options.color);
				} else if (options.color.h != undefined && options.color.s != undefined && options.color.b != undefined) {
					options.color = fixHSB(options.color);
				} else {
					return this;
				}
				options.origColor = options.color;
				return this.each(function () {
					if (!$(this).data('colorpickerId')) {
						var id = 'collorpicker_' + parseInt(Math.random() * 1000);
						
						//alert(id);
						
						$(this).data('colorpickerId', id);
						var cal = $(tpl).attr('id', id); 
						
						if (options.flat) {
							cal.appendTo(this).show();
						} else {
							cal.appendTo(document.body);
						}
						options.fields = cal
											.find('input')
												.bind('keydown', keyDown)
												.bind('change', change)
												.bind('blur', blur)
												.bind('focus', focus);
						cal.find('span').bind('mousedown', downIncrement);
						options.selector = cal.find('div.colorpicker_color').bind('mousedown', downSelector);
						options.selectorIndic = options.selector.find('div div');
						options.hue = cal.find('div.colorpicker_hue div');
						cal.find('div.colorpicker_hue').bind('mousedown', downHue);
						options.newColor = cal.find('div.colorpicker_new_color');
						options.currentColor = cal.find('div.colorpicker_current_color');
						cal.data('colorpicker', options); 
						
						/*
						var noneBTN = cal.find('input.colorpicker_none');
						noneBTN.get(0).cal=cal.get(0);
						noneBTN.click( function(){ 
								
								cal.hide();
							}); 
						*/
						cal.find('input.colorpicker_none').bind('click', clickNone);
						cal.find('input.colorpicker_submit')
							.bind('click', clickSubmit);
							/*
							.bind('mouseenter', enterSubmit)
							.bind('mouseleave', leaveSubmit)
							*/

						fillRGBFields(options.color, cal.get(0));
						fillHSBFields(options.color, cal.get(0));
						fillHexFields(options.color, cal.get(0));
						setHue(options.color, cal.get(0));
						setSelector(options.color, cal.get(0));
						setCurrentColor(options.color, cal.get(0));
						setNewColor(options.color, cal.get(0));
						if (options.flat) {
							cal.css({
								position: 'relative',
								display: 'block'
							});
						} else {
							$(this).bind(options.eventName, show);
						}
					}
				});
			},
			showPicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId')) {
						show.apply(this);
					}
				});
			},
			hidePicker: function() {
				return this.each( function () {
					if ($(this).data('colorpickerId')) {
						$('#' + $(this).data('colorpickerId')).hide();
					}
				});
			},
			setColor: function(col) {
				if (typeof col == 'string') {
					col = HexToHSB(col);
				} else if (col.r != undefined && col.g != undefined && col.b != undefined) {
					col = RGBToHSB(col);
				} else if (col.h != undefined && col.s != undefined && col.b != undefined) {
					col = fixHSB(col);
				} else {
					return this;
				}
				return this.each(function(){
					if ($(this).data('colorpickerId')) {
						var cal = $('#' + $(this).data('colorpickerId'));
						cal.data('colorpicker').color = col;
						cal.data('colorpicker').origColor = col;
						fillRGBFields(col, cal.get(0));
						fillHSBFields(col, cal.get(0));
						fillHexFields(col, cal.get(0));
						setHue(col, cal.get(0));
						setSelector(col, cal.get(0));
						setCurrentColor(col, cal.get(0));
						setNewColor(col, cal.get(0));
					}
				});
			}
		};
	}();
	$.fn.extend({
		ColorPicker: ColorPicker.init,
		ColorPickerHide: ColorPicker.hide,
		ColorPickerShow: ColorPicker.show,
		ColorPickerSetColor: ColorPicker.setColor
	});
})(jQuery);


/** 
 * Much thanks to http://static.railstips.org/orderedlist
 */
 
(function($) {  
	var self = null;
 	var lutype = 'blocktypes';
 	var searchValue = null;
 	
	$.fn.liveUpdate = function(list, type) {	
		return this.each(function() {
			new $.liveUpdate(this, list, type);
		});
	};
	
	$.liveUpdate = function (e, list, type) {
		this.field = $(e);
		this.list  = $('#' + list);
		this.lutype = 'blocktypes';
		if (typeof(type) != 'undefined') {
			this.lutype = type;
		}

		if (this.list.length > 0) {
			this.init();
		}
	};
	
	$.liveUpdate.prototype = {
		init: function() {
			var self = this;
			this.setupCache();
			this.field.parents('form').submit(function() { return false; });
			this.field.keyup(function() { self.filter(); });
			self.filter();
		},
		
		filter: function() {
			if (this.field.val() != searchValue) {
				if ($.trim(this.field.val()) == '') { 
					if (this.lutype == 'blocktypes') {
						this.list.children('li').addClass('ccm-block-type-available'); 
						this.list.children('li').removeClass('ccm-block-type-selected'); 
					} else if (this.lutype == 'attributes') {
						this.list.children('li').addClass('ccm-attribute-available'); 
						this.list.children('li').removeClass('ccm-attribute-selected'); 
					} else if (this.lutype == 'stacks') {
						this.list.children('li').addClass('ccm-stack-available'); 
						this.list.children('li').removeClass('ccm-stack-selected'); 
					} else if (this.lutype == 'intelligent-search') {
						if (this.list.is(':visible')) {
							this.list.hide();
						}
					} else {
						this.list.children('li').show();
					}
					return; 
				}
				if (this.lutype != 'intelligent-search' || this.field.val().length > 2) {
					this.displayResults(this.getScores(this.field.val().toLowerCase()));
				} else if (this.lutype  == 'intelligent-search') {
					if (this.list.is(':visible')) {
						this.list.hide();
					}
				}
			}
			searchValue = this.field.val();
			if (searchValue == '' && this.lutype  == 'intelligent-search') {
				if (this.list.is(':visible')) {
					this.list.hide();
				}
			}

		},
		
		setupCache: function() {
			var self = this;
			this.cache = [];
			this.rows = [];
			var lutype = this.lutype;
			this.list.find('li').each(function() {
				if (lutype == 'blocktypes') {
					self.cache.push($(this).find('a.ccm-block-type-inner').html().toLowerCase());
				} else if (lutype == 'attributes') {
					var val = $(this).find('a,span').html().toLowerCase();
					self.cache.push(val);
				} else if (lutype == 'stacks') {
					var val = $(this).find('a,span').html().toLowerCase();
					self.cache.push(val);
				} else if (lutype == 'fileset') {
					self.cache.push($(this).find('label').html().toLowerCase());
				} else if (lutype == 'intelligent-search') {
					self.cache.push($(this).find('span').html().toLowerCase());
				}
				self.rows.push($(this));
			});
			this.cache_length = this.cache.length;
		},
		
		displayResults: function(scores) {
			var self = this;
			if (this.lutype == 'blocktypes') {
				this.list.children('li').removeClass('ccm-block-type-available');
				this.list.children('li').removeClass('ccm-block-type-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-block-type-available'); });
				$(this.list.find('li.ccm-block-type-available')[0]).addClass('ccm-block-type-selected');
			} else if (this.lutype == 'attributes') {
				this.list.children('li').removeClass('ccm-attribute-available');
				this.list.children('li').removeClass('ccm-attribute-selected');
				this.list.children('li').removeClass('ccm-item-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-attribute-available'); });
				this.list.children('li.icon-select-list-header').removeClass("ccm-attribute-available");
				$(this.list.find('li.ccm-attribute-available')[0]).addClass('ccm-item-selected');

			} else if (this.lutype == 'stacks') {
				this.list.children('li').removeClass('ccm-stack-available');
				this.list.children('li').removeClass('ccm-stack-selected');
				this.list.children('li').removeClass('ccm-item-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-stack-available'); });
				this.list.children('li.icon-select-list-header').removeClass("ccm-stack-available");
				$(this.list.find('li.ccm-stack-available')[0]).addClass('ccm-item-selected');
			} else if (this.lutype == 'intelligent-search') {
				if (!this.list.is(':visible')) {
					this.list.fadeIn(160, 'easeOutExpo');
				}
				this.list.find('.ccm-intelligent-search-results-module-onsite').hide();
				this.list.find('li').hide();
				var shown = 0;
				$.each(scores, function(i, score) { 
					$li = self.rows[score[1]];
					if (score[0] > 0.7) {
						shown++;
						if (!$li.parent().parent().is(':visible')) {
							$li.parent().parent().show();
						}
						$li.show();
					}
				});
				this.list.find('li a').removeClass('ccm-intelligent-search-result-selected');
				this.list.find('li:visible a:first').addClass('ccm-intelligent-search-result-selected');
			} else {
				this.list.children('li').hide();
				$.each(scores, function(i, score) { self.rows[score[1]].show(); });
			}
		},
		
		getScores: function(term) {
			var scores = [];
			for (var i=0; i < this.cache_length; i++) {
				var score = this.cache[i].score(term);
				if (score > 0) { scores.push([score, i]); }
			}
			return scores.sort(function(a, b) { return b[0] - a[0]; });
		}
	}
})(jQuery);
/*
 * Metadata - jQuery plugin for parsing metadata from elements
 *
 * Copyright (c) 2006 John Resig, Yehuda Katz, Jörn Zaefferer, Paul McLanahan
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 *
 */

/**
 * Sets the type of metadata to use. Metadata is encoded in JSON, and each property
 * in the JSON will become a property of the element itself.
 *
 * There are three supported types of metadata storage:
 *
 *   attr:  Inside an attribute. The name parameter indicates *which* attribute.
 *          
 *   class: Inside the class attribute, wrapped in curly braces: { }
 *   
 *   elem:  Inside a child element (e.g. a script tag). The
 *          name parameter indicates *which* element.
 *          
 * The metadata for an element is loaded the first time the element is accessed via jQuery.
 *
 * As a result, you can define the metadata type, use $(expr) to load the metadata into the elements
 * matched by expr, then redefine the metadata type and run another $(expr) for other elements.
 * 
 * @name $.metadata.setType
 *
 * @example <p id="one" class="some_class {item_id: 1, item_label: 'Label'}">This is a p</p>
 * @before $.metadata.setType("class")
 * @after $("#one").metadata().item_id == 1; $("#one").metadata().item_label == "Label"
 * @desc Reads metadata from the class attribute
 * 
 * @example <p id="one" class="some_class" data="{item_id: 1, item_label: 'Label'}">This is a p</p>
 * @before $.metadata.setType("attr", "data")
 * @after $("#one").metadata().item_id == 1; $("#one").metadata().item_label == "Label"
 * @desc Reads metadata from a "data" attribute
 * 
 * @example <p id="one" class="some_class"><script>{item_id: 1, item_label: 'Label'}</script>This is a p</p>
 * @before $.metadata.setType("elem", "script")
 * @after $("#one").metadata().item_id == 1; $("#one").metadata().item_label == "Label"
 * @desc Reads metadata from a nested script element
 * 
 * @param String type The encoding type
 * @param String name The name of the attribute to be used to get metadata (optional)
 * @cat Plugins/Metadata
 * @descr Sets the type of encoding to be used when loading metadata for the first time
 * @type undefined
 * @see metadata()
 */

(function($) {

$.extend({
	metadata : {
		defaults : {
			type: 'class',
			name: 'metadata',
			cre: /({.*})/,
			single: 'metadata'
		},
		setType: function( type, name ){
			this.defaults.type = type;
			this.defaults.name = name;
		},
		get: function( elem, opts ){
			var settings = $.extend({},this.defaults,opts);
			// check for empty string in single property
			if ( !settings.single.length ) settings.single = 'metadata';
			
			var data = $.data(elem, settings.single);
			// returned cached data if it already exists
			if ( data ) return data;
			
			data = "{}";
			
			if ( settings.type == "class" ) {
				var m = settings.cre.exec( elem.className );
				if ( m )
					data = m[1];
			} else if ( settings.type == "elem" ) {
				if( !elem.getElementsByTagName ) return;
				var e = elem.getElementsByTagName(settings.name);
				if ( e.length )
					data = $.trim(e[0].innerHTML);
			} else if ( elem.getAttribute != undefined ) {
				var attr = elem.getAttribute( settings.name );
				if ( attr )
					data = attr;
			}
			
			if ( data.indexOf( '{' ) <0 )
			data = "{" + data + "}";
			
			data = eval("(" + data + ")");
			
			$.data( elem, settings.single, data );
			return data;
		}
	}
});

/**
 * Returns the metadata object for the first member of the jQuery object.
 *
 * @name metadata
 * @descr Returns element's metadata object
 * @param Object opts An object contianing settings to override the defaults
 * @type jQuery
 * @cat Plugins/Metadata
 */
$.fn.metadata = function( opts ){
	return $.metadata.get( this[0], opts );
};

})(jQuery);
/*
 * jQuery Nivo Slider v2.6
 * http://nivo.dev7studios.com
 *
 * Copyright 2011, Gilbert Pellegrom
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * March 2010
 */

(function($) {

    var NivoSlider = function(element, options){
		//Defaults are below
		var settings = $.extend({}, $.fn.nivoSlider.defaults, options);

        //Useful variables. Play carefully.
        var vars = {
            currentSlide: 0,
            currentImage: '',
            totalSlides: 0,
            randAnim: '',
            running: false,
            paused: false,
            stop: false
        };
    
        //Get this slider
        var slider = $(element);
        slider.data('nivo:vars', vars);
        slider.css('position','relative');
        slider.addClass('nivoSlider');
        
        //Find our slider children
        var kids = slider.children();
        kids.each(function() {
            var child = $(this);
            var link = '';
            if(!child.is('img')){
                if(child.is('a')){
                    child.addClass('nivo-imageLink');
                    link = child;
                }
                child = child.find('img:first');
            }
            //Get img width & height
            var childWidth = child.width();
            if(childWidth == 0) childWidth = child.attr('width');
            var childHeight = child.height();
            if(childHeight == 0) childHeight = child.attr('height');
            //Resize the slider
            if(childWidth > slider.width()){
                slider.width(childWidth);
            }
            if(childHeight > slider.height()){
                slider.height(childHeight);
            }
            if(link != ''){
                link.css('display','none');
            }
            child.css('display','none');
            vars.totalSlides++;
        });
        
        //Set startSlide
        if(settings.startSlide > 0){
            if(settings.startSlide >= vars.totalSlides) settings.startSlide = vars.totalSlides - 1;
            vars.currentSlide = settings.startSlide;
        }
        
        //Get initial image
        if($(kids[vars.currentSlide]).is('img')){
            vars.currentImage = $(kids[vars.currentSlide]);
        } else {
            vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
        }
        
        //Show initial link
        if($(kids[vars.currentSlide]).is('a')){
            $(kids[vars.currentSlide]).css('display','block');
        }
        
        //Set first background
        slider.css('background','url("'+ vars.currentImage.attr('src') +'") no-repeat');

        //Create caption
        slider.append(
            $('<div class="nivo-caption"><p></p></div>').css({ display:'none', opacity:settings.captionOpacity })
        );			
		
		// Process caption function
		var processCaption = function(settings){
			var nivoCaption = $('.nivo-caption', slider);
			if(vars.currentImage.attr('title') != '' && vars.currentImage.attr('title') != undefined){
				var title = vars.currentImage.attr('title');
				if(title.substr(0,1) == '#') title = $(title).html();	

				if(nivoCaption.css('display') == 'block'){
					nivoCaption.find('p').fadeOut(settings.animSpeed, function(){
						$(this).html(title);
						$(this).fadeIn(settings.animSpeed);
					});
				} else {
					nivoCaption.find('p').html(title);
				}					
				nivoCaption.fadeIn(settings.animSpeed);
			} else {
				nivoCaption.fadeOut(settings.animSpeed);
			}
		}
		
        //Process initial  caption
        processCaption(settings);
        
        //In the words of Super Mario "let's a go!"
        var timer = 0;
        if(!settings.manualAdvance && kids.length > 1){
            timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
        }

        //Add Direction nav
        if(settings.directionNav){
            slider.append('<div class="nivo-directionNav"><a class="nivo-prevNav">'+ settings.prevText +'</a><a class="nivo-nextNav">'+ settings.nextText +'</a></div>');
            
            //Hide Direction nav
            if(settings.directionNavHide){
                $('.nivo-directionNav', slider).hide();
                slider.hover(function(){
                    $('.nivo-directionNav', slider).show();
                }, function(){
                    $('.nivo-directionNav', slider).hide();
                });
            }
            
            $('a.nivo-prevNav', slider).live('click', function(){
                if(vars.running) return false;
                clearInterval(timer);
                timer = '';
                vars.currentSlide -= 2;
                nivoRun(slider, kids, settings, 'prev');
            });
            
            $('a.nivo-nextNav', slider).live('click', function(){
                if(vars.running) return false;
                clearInterval(timer);
                timer = '';
                nivoRun(slider, kids, settings, 'next');
            });
        }
        
        //Add Control nav
        if(settings.controlNav){
            var nivoControl = $('<div class="nivo-controlNav"></div>');
            slider.append(nivoControl);
            for(var i = 0; i < kids.length; i++){
                if(settings.controlNavThumbs){
                    var child = kids.eq(i);
                    if(!child.is('img')){
                        child = child.find('img:first');
                    }
                    if (settings.controlNavThumbsFromRel) {
                        nivoControl.append('<a class="nivo-control" rel="'+ i +'"><img src="'+ child.attr('rel') + '" alt="" /></a>');
                    } else {
                        nivoControl.append('<a class="nivo-control" rel="'+ i +'"><img src="'+ child.attr('src').replace(settings.controlNavThumbsSearch, settings.controlNavThumbsReplace) +'" alt="" /></a>');
                    }
                } else {
                    nivoControl.append('<a class="nivo-control" rel="'+ i +'">'+ (i + 1) +'</a>');
                }
                
            }
            //Set initial active link
            $('.nivo-controlNav a:eq('+ vars.currentSlide +')', slider).addClass('active');
            
            $('.nivo-controlNav a', slider).live('click', function(){
                if(vars.running) return false;
                if($(this).hasClass('active')) return false;
                clearInterval(timer);
                timer = '';
                slider.css('background','url("'+ vars.currentImage.attr('src') +'") no-repeat');
                vars.currentSlide = $(this).attr('rel') - 1;
                nivoRun(slider, kids, settings, 'control');
            });
        }
        
        //Keyboard Navigation
        if(settings.keyboardNav){
            $(window).keypress(function(event){
                //Left
                if(event.keyCode == '37'){
                    if(vars.running) return false;
                    clearInterval(timer);
                    timer = '';
                    vars.currentSlide-=2;
                    nivoRun(slider, kids, settings, 'prev');
                }
                //Right
                if(event.keyCode == '39'){
                    if(vars.running) return false;
                    clearInterval(timer);
                    timer = '';
                    nivoRun(slider, kids, settings, 'next');
                }
            });
        }
        
        //For pauseOnHover setting
        if(settings.pauseOnHover){
            slider.hover(function(){
                vars.paused = true;
                clearInterval(timer);
                timer = '';
            }, function(){
                vars.paused = false;
                //Restart the timer
                if(timer == '' && !settings.manualAdvance){
                    timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
                }
            });
        }
        
        //Event when Animation finishes
        slider.bind('nivo:animFinished', function(){ 
            vars.running = false; 
            //Hide child links
            $(kids).each(function(){
                if($(this).is('a')){
                    $(this).css('display','none');
                }
            });
            //Show current link
            if($(kids[vars.currentSlide]).is('a')){
                $(kids[vars.currentSlide]).css('display','block');
            }
            //Restart the timer
            if(timer == '' && !vars.paused && !settings.manualAdvance){
                timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
            }
            //Trigger the afterChange callback
            settings.afterChange.call(this);
        });
        
        // Add slices for slice animations
        var createSlices = function(slider, settings, vars){
            for(var i = 0; i < settings.slices; i++){
				var sliceWidth = Math.round(slider.width()/settings.slices);
				if(i == settings.slices-1){
					slider.append(
						$('<div class="nivo-slice"></div>').css({ 
							left:(sliceWidth*i)+'px', width:(slider.width()-(sliceWidth*i))+'px',
							height:'0px', 
							opacity:'0', 
							background: 'url("'+ vars.currentImage.attr('src') +'") no-repeat -'+ ((sliceWidth + (i * sliceWidth)) - sliceWidth) +'px 0%'
						})
					);
				} else {
					slider.append(
						$('<div class="nivo-slice"></div>').css({ 
							left:(sliceWidth*i)+'px', width:sliceWidth+'px',
							height:'0px', 
							opacity:'0', 
							background: 'url("'+ vars.currentImage.attr('src') +'") no-repeat -'+ ((sliceWidth + (i * sliceWidth)) - sliceWidth) +'px 0%'
						})
					);
				}
			}
        }
		
		// Add boxes for box animations
		var createBoxes = function(slider, settings, vars){
			var boxWidth = Math.round(slider.width()/settings.boxCols);
			var boxHeight = Math.round(slider.height()/settings.boxRows);
			
			for(var rows = 0; rows < settings.boxRows; rows++){
				for(var cols = 0; cols < settings.boxCols; cols++){
					if(cols == settings.boxCols-1){
						slider.append(
							$('<div class="nivo-box"></div>').css({ 
								opacity:0,
								left:(boxWidth*cols)+'px', 
								top:(boxHeight*rows)+'px',
								width:(slider.width()-(boxWidth*cols))+'px',
								height:boxHeight+'px',
								background: 'url("'+ vars.currentImage.attr('src') +'") no-repeat -'+ ((boxWidth + (cols * boxWidth)) - boxWidth) +'px -'+ ((boxHeight + (rows * boxHeight)) - boxHeight) +'px'
							})
						);
					} else {
						slider.append(
							$('<div class="nivo-box"></div>').css({ 
								opacity:0,
								left:(boxWidth*cols)+'px', 
								top:(boxHeight*rows)+'px',
								width:boxWidth+'px',
								height:boxHeight+'px',
								background: 'url("'+ vars.currentImage.attr('src') +'") no-repeat -'+ ((boxWidth + (cols * boxWidth)) - boxWidth) +'px -'+ ((boxHeight + (rows * boxHeight)) - boxHeight) +'px'
							})
						);
					}
				}
			}
		}

        // Private run method
		var nivoRun = function(slider, kids, settings, nudge){
			//Get our vars
			var vars = slider.data('nivo:vars');
            
            //Trigger the lastSlide callback
            if(vars && (vars.currentSlide == vars.totalSlides - 1)){ 
				settings.lastSlide.call(this);
			}
            
            // Stop
			if((!vars || vars.stop) && !nudge) return false;
			
			//Trigger the beforeChange callback
			settings.beforeChange.call(this);
					
			//Set current background before change
			if(!nudge){
				slider.css('background','url("'+ vars.currentImage.attr('src') +'") no-repeat');
			} else {
				if(nudge == 'prev'){
					slider.css('background','url("'+ vars.currentImage.attr('src') +'") no-repeat');
				}
				if(nudge == 'next'){
					slider.css('background','url("'+ vars.currentImage.attr('src') +'") no-repeat');
				}
			}
			vars.currentSlide++;
            //Trigger the slideshowEnd callback
			if(vars.currentSlide == vars.totalSlides){ 
				vars.currentSlide = 0;
				settings.slideshowEnd.call(this);
			}
			if(vars.currentSlide < 0) vars.currentSlide = (vars.totalSlides - 1);
			//Set vars.currentImage
			if($(kids[vars.currentSlide]).is('img')){
				vars.currentImage = $(kids[vars.currentSlide]);
			} else {
				vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
			}
			
			//Set active links
			if(settings.controlNav){
				$('.nivo-controlNav a', slider).removeClass('active');
				$('.nivo-controlNav a:eq('+ vars.currentSlide +')', slider).addClass('active');
			}
			
			//Process caption
			processCaption(settings);
			
			// Remove any slices from last transition
			$('.nivo-slice', slider).remove();
			
			// Remove any boxes from last transition
			$('.nivo-box', slider).remove();
			
			if(settings.effect == 'random'){
				var anims = new Array('sliceDownRight','sliceDownLeft','sliceUpRight','sliceUpLeft','sliceUpDown','sliceUpDownLeft','fold','fade',
                'boxRandom','boxRain','boxRainReverse','boxRainGrow','boxRainGrowReverse');
				vars.randAnim = anims[Math.floor(Math.random()*(anims.length + 1))];
				if(vars.randAnim == undefined) vars.randAnim = 'fade';
			}
            
            //Run random effect from specified set (eg: effect:'fold,fade')
            if(settings.effect.indexOf(',') != -1){
                var anims = settings.effect.split(',');
                vars.randAnim = anims[Math.floor(Math.random()*(anims.length))];
				if(vars.randAnim == undefined) vars.randAnim = 'fade';
            }
		
			//Run effects
			vars.running = true;
			if(settings.effect == 'sliceDown' || settings.effect == 'sliceDownRight' || vars.randAnim == 'sliceDownRight' ||
				settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft'){
				createSlices(slider, settings, vars);
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft') slices = $('.nivo-slice', slider)._reverse();
				
				slices.each(function(){
					var slice = $(this);
					slice.css({ 'top': '0px' });
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUp' || settings.effect == 'sliceUpRight' || vars.randAnim == 'sliceUpRight' ||
					settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft'){
				createSlices(slider, settings, vars);
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft') slices = $('.nivo-slice', slider)._reverse();
				
				slices.each(function(){
					var slice = $(this);
					slice.css({ 'bottom': '0px' });
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUpDown' || settings.effect == 'sliceUpDownRight' || vars.randAnim == 'sliceUpDown' || 
					settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft'){
				createSlices(slider, settings, vars);
				var timeBuff = 0;
				var i = 0;
				var v = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft') slices = $('.nivo-slice', slider)._reverse();
				
				slices.each(function(){
					var slice = $(this);
					if(i == 0){
						slice.css('top','0px');
						i++;
					} else {
						slice.css('bottom','0px');
						i = 0;
					}
					
					if(v == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					v++;
				});
			} 
			else if(settings.effect == 'fold' || vars.randAnim == 'fold'){
				createSlices(slider, settings, vars);
				var timeBuff = 0;
				var i = 0;
				
				$('.nivo-slice', slider).each(function(){
					var slice = $(this);
					var origWidth = slice.width();
					slice.css({ top:'0px', height:'100%', width:'0px' });
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			}  
			else if(settings.effect == 'fade' || vars.randAnim == 'fade'){
				createSlices(slider, settings, vars);
				
				var firstSlice = $('.nivo-slice:first', slider);
                firstSlice.css({
                    'height': '100%',
                    'width': slider.width() + 'px'
                });
    
				firstSlice.animate({ opacity:'1.0' }, (settings.animSpeed*2), '', function(){ slider.trigger('nivo:animFinished'); });
			}          
            else if(settings.effect == 'slideInRight' || vars.randAnim == 'slideInRight'){
				createSlices(slider, settings, vars);
				
                var firstSlice = $('.nivo-slice:first', slider);
                firstSlice.css({
                    'height': '100%',
                    'width': '0px',
                    'opacity': '1'
                });

                firstSlice.animate({ width: slider.width() + 'px' }, (settings.animSpeed*2), '', function(){ slider.trigger('nivo:animFinished'); });
            }
            else if(settings.effect == 'slideInLeft' || vars.randAnim == 'slideInLeft'){
				createSlices(slider, settings, vars);
				
                var firstSlice = $('.nivo-slice:first', slider);
                firstSlice.css({
                    'height': '100%',
                    'width': '0px',
                    'opacity': '1',
                    'left': '',
                    'right': '0px'
                });

                firstSlice.animate({ width: slider.width() + 'px' }, (settings.animSpeed*2), '', function(){ 
                    // Reset positioning
                    firstSlice.css({
                        'left': '0px',
                        'right': ''
                    });
                    slider.trigger('nivo:animFinished'); 
                });
            }
			else if(settings.effect == 'boxRandom' || vars.randAnim == 'boxRandom'){
				createBoxes(slider, settings, vars);
				
				var totalBoxes = settings.boxCols * settings.boxRows;
				var i = 0;
				var timeBuff = 0;
				
				var boxes = shuffle($('.nivo-box', slider));
				boxes.each(function(){
					var box = $(this);
					if(i == totalBoxes-1){
						setTimeout(function(){
							box.animate({ opacity:'1' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							box.animate({ opacity:'1' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 20;
					i++;
				});
			}
			else if(settings.effect == 'boxRain' || vars.randAnim == 'boxRain' || settings.effect == 'boxRainReverse' || vars.randAnim == 'boxRainReverse' || 
                    settings.effect == 'boxRainGrow' || vars.randAnim == 'boxRainGrow' || settings.effect == 'boxRainGrowReverse' || vars.randAnim == 'boxRainGrowReverse'){
				createBoxes(slider, settings, vars);
				
				var totalBoxes = settings.boxCols * settings.boxRows;
				var i = 0;
				var timeBuff = 0;
				
				// Split boxes into 2D array
				var rowIndex = 0;
				var colIndex = 0;
				var box2Darr = new Array();
				box2Darr[rowIndex] = new Array();
				var boxes = $('.nivo-box', slider);
				if(settings.effect == 'boxRainReverse' || vars.randAnim == 'boxRainReverse' ||
                   settings.effect == 'boxRainGrowReverse' || vars.randAnim == 'boxRainGrowReverse'){
					boxes = $('.nivo-box', slider)._reverse();
				}
				boxes.each(function(){
					box2Darr[rowIndex][colIndex] = $(this);
					colIndex++;
					if(colIndex == settings.boxCols){
						rowIndex++;
						colIndex = 0;
						box2Darr[rowIndex] = new Array();
					}
				});
				
				// Run animation
				for(var cols = 0; cols < (settings.boxCols * 2); cols++){
					var prevCol = cols;
					for(var rows = 0; rows < settings.boxRows; rows++){
						if(prevCol >= 0 && prevCol < settings.boxCols){
							/* Due to some weird JS bug with loop vars 
							being used in setTimeout, this is wrapped
							with an anonymous function call */
							(function(row, col, time, i, totalBoxes) {
								var box = $(box2Darr[row][col]);
                                var w = box.width();
                                var h = box.height();
                                if(settings.effect == 'boxRainGrow' || vars.randAnim == 'boxRainGrow' ||
                                   settings.effect == 'boxRainGrowReverse' || vars.randAnim == 'boxRainGrowReverse'){
                                    box.width(0).height(0);
                                }
								if(i == totalBoxes-1){
									setTimeout(function(){
										box.animate({ opacity:'1', width:w, height:h }, settings.animSpeed/1.3, '', function(){ slider.trigger('nivo:animFinished'); });
									}, (100 + time));
								} else {
									setTimeout(function(){
										box.animate({ opacity:'1', width:w, height:h }, settings.animSpeed/1.3);
									}, (100 + time));
								}
							})(rows, prevCol, timeBuff, i, totalBoxes);
							i++;
						}
						prevCol--;
					}
					timeBuff += 100;
				}
			}
		}
		
		// Shuffle an array
		var shuffle = function(arr){
			for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
			return arr;
		}
        
        // For debugging
        var trace = function(msg){
            if (this.console && typeof console.log != "undefined")
                console.log(msg);
        }
        
        // Start / Stop
        this.stop = function(){
            if(!$(element).data('nivo:vars').stop){
                $(element).data('nivo:vars').stop = true;
                trace('Stop Slider');
            }
        }
        
        this.start = function(){
            if($(element).data('nivo:vars').stop){
                $(element).data('nivo:vars').stop = false;
                trace('Start Slider');
            }
        }
        
        //Trigger the afterLoad callback
        settings.afterLoad.call(this);
		
		return this;
    };
        
    $.fn.nivoSlider = function(options) {
    
        return this.each(function(key, value){
            var element = $(this);
            // Return early if this element already has a plugin instance
            if (element.data('nivoslider')) return element.data('nivoslider');
            // Pass options to plugin constructor
            var nivoslider = new NivoSlider(this, options);
            // Store plugin object in this element's data
            element.data('nivoslider', nivoslider);
        });

	};
	
	//Default settings
	$.fn.nivoSlider.defaults = {
		effect: 'random',
		slices: 15,
		boxCols: 8,
		boxRows: 4,
		animSpeed: 500,
		pauseTime: 3000,
		startSlide: 0,
		directionNav: true,
		directionNavHide: true,
		controlNav: true,
		controlNavThumbs: false,
        controlNavThumbsFromRel: false,
		controlNavThumbsSearch: '.jpg',
		controlNavThumbsReplace: '_thumb.jpg',
		keyboardNav: true,
		pauseOnHover: true,
		manualAdvance: false,
		captionOpacity: 0.8,
		prevText: 'Prev',
		nextText: 'Next',
		beforeChange: function(){},
		afterChange: function(){},
		slideshowEnd: function(){},
        lastSlide: function(){},
        afterLoad: function(){}
	};
	
	$.fn._reverse = [].reverse;
	
})(jQuery);
/*
 ### jQuery Star Rating Plugin v3.00 - 2009-03-16 ###
 * Home: http://www.fyneworks.com/jquery/star-rating/
 * Code: http://code.google.com/p/jquery-star-rating-plugin/
 *
	* Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 ###
*//*
	Based on http://www.phpletter.com/Demo/Jquery-Star-Rating-Plugin/
 Original comments:
	This is hacked version of star rating created by <a href="http://php.scripts.psu.edu/rja171/widgets/rating.php">Ritesh Agrawal</a>
	It transforms a set of radio type input elements to star rating type and remain the radio element name and value,
	so could be integrated with your form. It acts as a normal radio button.
	modified by : Logan Cai (cailongqun[at]yahoo.com.cn)
*/

/*# AVOID COLLISIONS #*/
;if(window.jQuery) (function($){
/*# AVOID COLLISIONS #*/
	
	// IE6 Background Image Fix
	if ($.browser.msie) try { document.execCommand("BackgroundImageCache", false, true)} catch(e) { }
	// Thanks to http://www.visualjquery.com/rating/rating_redux.html
	
	// plugin initialization
	$.fn.rating = function(options){
		if(this.length==0) return this; // quick fail
		
		// Handle API methods
		if(typeof arguments[0]=='string'){
			// Perform API methods on individual elements
			if(this.length>1){
				var args = arguments;
				return this.each(function(){
					$.fn.rating.apply($(this), args);
    });
			};
			// Invoke API method handler
			$.fn.rating[arguments[0]].apply(this, $.makeArray(arguments).slice(1) || []);
			// Quick exit...
			return this;
		};
		
		// Initialize options for this call
		var options = $.extend(
			{}/* new object */,
			$.fn.rating.options/* default options */,
			options || {} /* just-in-time options */
		);
		
		// loop through each matched element
		this.each(function(){
			
			// Generate internal control ID
			// - ignore square brackets in element names
			var eid = (this.name || 'unnamed-rating').replace(/\[|\]+/g, "_");
			var context = $(this.form || document.body);
			var raters = context.data('rating') || { count:0 };
			var rater = raters[eid];
			var control;
			// ---------------
			
			if(rater){
				control = rater.data('rating');
				control.count++;
			}
			else{
				// Initialize options for this raters
				control = $.extend(
					{}/* new object */,
					options || {} /* current call options */,
					($.metadata? $(this).metadata(): ($.meta?$(this).data():null)) || {}, /* metadata options */
					{ count:0, stars: [] }
				);
				
				// increment number of rating controls
				control.serial = raters.count++;
				
				// create rating element
				rater = $('<input type="text" class="rating-star-control" name="' + this.name + '" id="' + eid + '" value="" size="2"/>');
				$(this).before(rater.hide());
				
				// Mark element for initialization (once all stars are ready)
				rater.addClass('rating-to-be-drawn');
				
				// Accept readOnly setting from 'disabled' property
				if($(this).attr('disabled')) control.readOnly = true;
				
				// Create 'cancel' button
				$(this).before(
					control.cancel = $('<div class="rating-cancel"><a title="' + control.cancel + '">' + control.cancelValue + '</a></div>')
					.mouseover(function(){ $(this).rating('drain'); $(this).addClass('rating-star-hover'); })
					.mouseout(function(){ $(this).rating('draw'); $(this).removeClass('rating-star-hover'); })
					.click(function(){ $(this).rating('select'); })
					.data('rating', control)
				);
				
			}; // first element of group
			
			// insert rating star
			var star = $('<div class="rating-star rater-'+ control.serial +'"><a title="' + (this.title || this.value) + '">' + this.value + '</a></div>');
			$(this).after(star);
			
			// inherit attributes from input element
			if(this.id) star.attr('id', this.id);
			if(this.className) star.addClass(this.className);
			
			// Half-stars?
			if(control.half) control.split = 2;
			
			// Prepare division control
			if(typeof control.split=='number' && control.split>0){
				var stw = ($.fn.width ? star.width() : 0) || control.starWidth;
				var spi = (control.count % control.split), spw = Math.floor(stw/control.split);
				star
				// restrict star's width and hide overflow (already in CSS)
				.width(spw)
				// move the star left by using a negative margin
				// this is work-around to IE's stupid box model (position:relative doesn't work)
				.find('a').css({ 'margin-left':'-'+ (spi*spw) +'px' })
			};
			
			// readOnly?
			if(control.readOnly)//{ //save a byte!
				// Mark star as readOnly so user can customize display
				star.addClass('rating-star-readonly');
			//}  //save a byte!
			else//{ //save a byte!
			 // Enable hover css effects
				star.addClass('rating-star-live')
				 // Attach mouse events
				 .mouseover(function(){ $(this).rating('fill'); })
				 .mouseout(function(){ $(this).rating('draw'); })
				 .click(function(){ $(this).rating('select'); })
				;
			//}; //save a byte!
			
			////if(window.console) console.log(['###', gid, this.checked, $.fn.rating.group[gid].initial]);
			if(this.checked)	control.current = star;
			
			// remove this checkbox - values will be stored in a hidden field
			$(this).remove();
			
			// store control information in form (or body when form not available)
			control.stars[control.stars.length] = star;
			control.element = raters[eid] = rater;
			control.context = context;
			control.raters = raters;
			
			rater.data('rating', control);
			star.data('rating', control);
			context.data('rating', raters);
  }); // each element
		
		// Initialize ratings (first draw)
		$('.rating-to-be-drawn').rating('draw').removeClass('rating-to-be-drawn');
		
		return this; // don't break the chain...
	};
	
	/*--------------------------------------------------------*/
	
	/*
		### Core functionality and API ###
	*/
	$.extend($.fn.rating, {
		
		fill: function(){ // fill to the current mouse position.
			var control = this.data('rating'); if(!control) return this;
			// do not execute when control is in read-only mode
			if(control.readOnly) return;
			// Reset all stars and highlight them up to this element
			this.rating('drain');
			this.prevAll().andSelf().filter('.rater-'+ control.serial).addClass('rating-star-hover');
			// focus handler, as requested by focusdigital.co.uk
			if(control.focus) control.focus.apply(
    control.element, [this.text(), $('a',this)[0]]
			);
		},// $.fn.rating.fill
		
		drain: function() { // drain all the stars.
			var control = this.data('rating'); if(!control) return this;
			// do not execute when control is in read-only mode
			if(control.readOnly) return;
			// Reset all stars
			control.element.siblings().filter('.rater-'+ control.serial).removeClass('rating-star-on').removeClass('rating-star-hover');
		},// $.fn.rating.drain
		
		draw: function(){ // set value and stars to reflect current selection
			var control = this.data('rating'); if(!control) return this;
			// Clear all stars
			this.rating('drain');
			// Set control value
			if(control.current){
 			control.element.val(control.current.text());
				control.current.prevAll().andSelf().filter('.rater-'+ control.serial).addClass('rating-star-on');
			}
			else
			 control.element.val('');
			// Show/hide 'cancel' button
			control.cancel[control.readOnly || control.required?'hide':'show']();
			// Add/remove read-only classes to remove hand pointer
			this.siblings()[control.readOnly?'addClass':'removeClass']('rating-star-readonly');
			// blur handler, as requested by focusdigital.co.uk
			if(control.blur) control.blur.apply(
    control.element, [this.text(), $('a',this)[0]]
			);
		},// $.fn.rating.draw
		
		select: function(value){ // select a value
			var control = this.data('rating'); if(!control) return this;
			// do not execute when control is in read-only mode
			if(control.readOnly) return;
			// clear selection
			control.current = null;
			// programmatically (based on user input)
			if(typeof value!='undefined'){
			 // select by index (0 based)
				if(typeof value=='number')
 			 return control.stars[value].rating('select');
				// select by literal value (must be passed as a string
				if(typeof value=='string')
					return $(control.stars).each(function(){
						if(this.text()==value) this.rating('select');
					});
			}
			else if(this.is('.rater-'+ control.serial))
			 control.current = this;
			// Update rating control state
			this.data('rating', control);
			// Update display
			this.rating('draw');
			// click callback, as requested here: http://plugins.jquery.com/node/1655
			if(control.callback) control.callback.apply(
    control.element,
				 control.current ?
				 [control.current.text(), $('a',control.current)[0]] :
					['']
			);// callback event
		},// $.fn.rating.select
		
		readOnly: function(toggle, disable){ // make the control read-only (still submits value)
			var control = this.data('rating'); if(!control) return this;
			// setread-only status
			control.readOnly = toggle ? true : false;
			// enable/disable control value submission
			if(disable) control.element.attr("disabled", "disabled");
			else     			control.element.removeAttr("disabled");
			// Update rating control state
			this.data('rating', control);
			// Update display
			this.rating('draw');
		},// $.fn.rating.readOnly
		
		disable: function(){ // make read-only and never submit value
			this.rating('readOnly', true, true);
		},// $.fn.rating.disable
		
		enable: function(){ // make read/write and submit value
			this.rating('readOnly', false, false);
		}// $.fn.rating.select
		
 });
	
	/*--------------------------------------------------------*/
	
	/*
		### Default Settings ###
		eg.: You can override default control like this:
		$.fn.rating.options.cancel = 'Clear';
	*/
	$.fn.rating.options = { //$.extend($.fn.rating, { options: {
			cancel: 'Cancel Rating',   // advisory title for the 'cancel' link
			cancelValue: '',           // value to submit when user click the 'cancel' link
			split: 0,                  // split the star into how many parts?
			
			// Width of star image in case the plugin can't work it out. This can happen if
			// the jQuery.dimensions plugin is not available OR the image is hidden at installation
			starWidth: 16//,
			
			//NB.: These don't need to be pre-defined (can be undefined/null) so let's save some code!
			//half:     false,         // just a shortcut to control.split = 2
			//required: false,         // disables the 'cancel' button so user can only select one of the specified values
			//readOnly: false,         // disable rating plugin interaction/ values cannot be changed
			//focus:    function(){},  // executed when stars are focused
			//blur:     function(){},  // executed when stars are focused
			//callback: function(){},  // executed when a star is clicked
 }; //} });
	
	/*--------------------------------------------------------*/
	
	/*
		### Default implementation ###
		The plugin will attach itself to file inputs
		with the class 'multi' when the page loads
	*/
	$(function(){ $('input[type=radio].star').rating(); });
	
	
	
/*# AVOID COLLISIONS #*/
})(jQuery);
/*# AVOID COLLISIONS #*/

// Chosen, a Select Box Enhancer for jQuery and Protoype
// by Patrick Filler for Harvest, http://getharvest.com
// 
// Version 0.9.5
// Full source at https://github.com/harvesthq/chosen
// Copyright (c) 2011 Harvest http://getharvest.com

// MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md
// This file is generated by `cake build`, do not edit it by hand.
(function() {
  /*
  Chosen source: generate output using 'cake build'
  Copyright (c) 2011 by Harvest
  */  var $, Chosen, get_side_border_padding, root;
  var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
  root = this;
  $ = jQuery;
  $.fn.extend({
    chosen: function(options) {
      if ($.browser === "msie" && ($.browser.version === "6.0" || $.browser.version === "7.0")) {
        return this;
      }
      return $(this).each(function(input_field) {
        if (!($(this)).hasClass("chzn-done")) {
          return new Chosen(this, options);
        }
      });
    }
  });
  Chosen = (function() {
    function Chosen(form_field, options) {
      this.form_field = form_field;
      this.options = options != null ? options : {};
      this.set_default_values();
      this.form_field_jq = $(this.form_field);
      this.is_multiple = this.form_field.multiple;
      this.is_rtl = this.form_field_jq.hasClass("chzn-rtl");
      this.default_text_default = this.form_field.multiple ? "Select Some Options" : "Select an Option";
      this.set_up_html();
      this.register_observers();
      this.form_field_jq.addClass("chzn-done");
    }
    Chosen.prototype.set_default_values = function() {
      this.click_test_action = __bind(function(evt) {
        return this.test_active_click(evt);
      }, this);
      this.activate_action = __bind(function(evt) {
        return this.activate_field(evt);
      }, this);
      this.active_field = false;
      this.mouse_on_container = false;
      this.results_showing = false;
      this.result_highlighted = null;
      this.result_single_selected = null;
      this.allow_single_deselect = (this.options.allow_single_deselect != null) && this.form_field.options[0].text === "" ? this.options.allow_single_deselect : false;
      this.disable_search_threshold = this.options.disable_search_threshold || 0;
      this.choices = 0;
      return this.results_none_found = this.options.no_results_text || "No results match";
    };
    Chosen.prototype.set_up_html = function() {
      var container_div, dd_top, dd_width, sf_width;
      this.container_id = this.form_field.id.length ? this.form_field.id.replace(/(:|\.)/g, '_') : this.generate_field_id();
      this.container_id += "_chzn";
      this.f_width = this.form_field_jq.outerWidth();
      this.default_text = this.form_field_jq.data('placeholder') ? this.form_field_jq.data('placeholder') : this.default_text_default;
      container_div = $("<div />", {
        id: this.container_id,
        "class": "chzn-container" + (this.is_rtl ? ' chzn-rtl' : ''),
        style: 'width: ' + this.f_width + 'px;'
      });
      if (this.is_multiple) {
        container_div.html('<ul class="chzn-choices"><li class="search-field"><input type="text" value="' + this.default_text + '" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chzn-drop" style="left:-9000px;"><ul class="chzn-results"></ul></div>');
      } else {
        container_div.html('<a href="javascript:void(0)" class="chzn-single"><span>' + this.default_text + '</span><div><b></b></div></a><div class="chzn-drop" style="left:-9000px;"><div class="chzn-search"><input type="text" autocomplete="off" /></div><ul class="chzn-results"></ul></div>');
      }
      this.form_field_jq.hide().after(container_div);
      this.container = $('#' + this.container_id);
      this.container.addClass("chzn-container-" + (this.is_multiple ? "multi" : "single"));
      if (!this.is_multiple && this.form_field.options.length <= this.disable_search_threshold) {
        this.container.addClass("chzn-container-single-nosearch");
      }
      this.dropdown = this.container.find('div.chzn-drop').first();
      dd_top = this.container.height();
      dd_width = this.f_width - get_side_border_padding(this.dropdown);
      this.dropdown.css({
        "width": dd_width + "px",
        "top": dd_top + "px"
      });
      this.search_field = this.container.find('input').first();
      this.search_results = this.container.find('ul.chzn-results').first();
      this.search_field_scale();
      this.search_no_results = this.container.find('li.no-results').first();
      if (this.is_multiple) {
        this.search_choices = this.container.find('ul.chzn-choices').first();
        this.search_container = this.container.find('li.search-field').first();
      } else {
        this.search_container = this.container.find('div.chzn-search').first();
        this.selected_item = this.container.find('.chzn-single').first();
        sf_width = dd_width - get_side_border_padding(this.search_container) - get_side_border_padding(this.search_field);
        this.search_field.css({
          "width": sf_width + "px"
        });
      }
      this.results_build();
      return this.set_tab_index();
    };
    Chosen.prototype.register_observers = function() {
      this.container.mousedown(__bind(function(evt) {
        return this.container_mousedown(evt);
      }, this));
      this.container.mouseup(__bind(function(evt) {
        return this.container_mouseup(evt);
      }, this));
      this.container.mouseenter(__bind(function(evt) {
        return this.mouse_enter(evt);
      }, this));
      this.container.mouseleave(__bind(function(evt) {
        return this.mouse_leave(evt);
      }, this));
      this.search_results.mouseup(__bind(function(evt) {
        return this.search_results_mouseup(evt);
      }, this));
      this.search_results.mouseover(__bind(function(evt) {
        return this.search_results_mouseover(evt);
      }, this));
      this.search_results.mouseout(__bind(function(evt) {
        return this.search_results_mouseout(evt);
      }, this));
      this.form_field_jq.bind("liszt:updated", __bind(function(evt) {
        return this.results_update_field(evt);
      }, this));
      this.search_field.blur(__bind(function(evt) {
        return this.input_blur(evt);
      }, this));
      this.search_field.keyup(__bind(function(evt) {
        return this.keyup_checker(evt);
      }, this));
      this.search_field.keydown(__bind(function(evt) {
        return this.keydown_checker(evt);
      }, this));
      if (this.is_multiple) {
        this.search_choices.click(__bind(function(evt) {
          return this.choices_click(evt);
        }, this));
        return this.search_field.focus(__bind(function(evt) {
          return this.input_focus(evt);
        }, this));
      }
    };
    Chosen.prototype.search_field_disabled = function() {
      this.is_disabled = this.form_field_jq.attr('disabled');
      if (this.is_disabled) {
        this.container.addClass('chzn-disabled');
        this.search_field.attr('disabled', true);
        if (!this.is_multiple) {
          this.selected_item.unbind("focus", this.activate_action);
        }
        return this.close_field();
      } else {
        this.container.removeClass('chzn-disabled');
        this.search_field.attr('disabled', false);
        if (!this.is_multiple) {
          return this.selected_item.bind("focus", this.activate_action);
        }
      }
    };
    Chosen.prototype.container_mousedown = function(evt) {
      var target_closelink;
      if (!this.is_disabled) {
        target_closelink = evt != null ? ($(evt.target)).hasClass("search-choice-close") : false;
        if (evt && evt.type === "mousedown") {
          evt.stopPropagation();
        }
        if (!this.pending_destroy_click && !target_closelink) {
          if (!this.active_field) {
            if (this.is_multiple) {
              this.search_field.val("");
            }
            $(document).click(this.click_test_action);
            this.results_show();
          } else if (!this.is_multiple && evt && ($(evt.target) === this.selected_item || $(evt.target).parents("a.chzn-single").length)) {
            evt.preventDefault();
            this.results_toggle();
          }
          return this.activate_field();
        } else {
          return this.pending_destroy_click = false;
        }
      }
    };
    Chosen.prototype.container_mouseup = function(evt) {
      if (evt.target.nodeName === "ABBR") {
        return this.results_reset(evt);
      }
    };
    Chosen.prototype.mouse_enter = function() {
      return this.mouse_on_container = true;
    };
    Chosen.prototype.mouse_leave = function() {
      return this.mouse_on_container = false;
    };
    Chosen.prototype.input_focus = function(evt) {
      if (!this.active_field) {
        return setTimeout((__bind(function() {
          return this.container_mousedown();
        }, this)), 50);
      }
    };
    Chosen.prototype.input_blur = function(evt) {
      if (!this.mouse_on_container) {
        this.active_field = false;
        return setTimeout((__bind(function() {
          return this.blur_test();
        }, this)), 100);
      }
    };
    Chosen.prototype.blur_test = function(evt) {
      if (!this.active_field && this.container.hasClass("chzn-container-active")) {
        return this.close_field();
      }
    };
    Chosen.prototype.close_field = function() {
      $(document).unbind("click", this.click_test_action);
      if (!this.is_multiple) {
        this.selected_item.attr("tabindex", this.search_field.attr("tabindex"));
        this.search_field.attr("tabindex", -1);
      }
      this.active_field = false;
      this.results_hide();
      this.container.removeClass("chzn-container-active");
      this.winnow_results_clear();
      this.clear_backstroke();
      this.show_search_field_default();
      return this.search_field_scale();
    };
    Chosen.prototype.activate_field = function() {
      if (!this.is_multiple && !this.active_field) {
        this.search_field.attr("tabindex", this.selected_item.attr("tabindex"));
        this.selected_item.attr("tabindex", -1);
      }
      this.container.addClass("chzn-container-active");
      this.active_field = true;
      this.search_field.val(this.search_field.val());
      return this.search_field.focus();
    };
    Chosen.prototype.test_active_click = function(evt) {
      if ($(evt.target).parents('#' + this.container_id).length) {
        return this.active_field = true;
      } else {
        return this.close_field();
      }
    };
    Chosen.prototype.results_build = function() {
      var content, data, startTime, _i, _len, _ref;
      startTime = new Date();
      this.parsing = true;
      this.results_data = root.SelectParser.select_to_array(this.form_field);
      if (this.is_multiple && this.choices > 0) {
        this.search_choices.find("li.search-choice").remove();
        this.choices = 0;
      } else if (!this.is_multiple) {
        this.selected_item.find("span").text(this.default_text);
      }
      content = '';
      _ref = this.results_data;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        data = _ref[_i];
        if (data.group) {
          content += this.result_add_group(data);
        } else if (!data.empty) {
          content += this.result_add_option(data);
          if (data.selected && this.is_multiple) {
            this.choice_build(data);
          } else if (data.selected && !this.is_multiple) {
            this.selected_item.find("span").text(data.text);
            if (this.allow_single_deselect) {
              this.selected_item.find("span").first().after("<abbr class=\"search-choice-close\"></abbr>");
            }
          }
        }
      }
      this.search_field_disabled();
      this.show_search_field_default();
      this.search_field_scale();
      this.search_results.html(content);
      return this.parsing = false;
    };
    Chosen.prototype.result_add_group = function(group) {
      if (!group.disabled) {
        group.dom_id = this.container_id + "_g_" + group.array_index;
        return '<li id="' + group.dom_id + '" class="group-result">' + $("<div />").text(group.label).html() + '</li>';
      } else {
        return "";
      }
    };
    Chosen.prototype.result_add_option = function(option) {
      var classes, style;
      if (!option.disabled) {
        option.dom_id = this.container_id + "_o_" + option.array_index;
        classes = option.selected && this.is_multiple ? [] : ["active-result"];
        if (option.selected) {
          classes.push("result-selected");
        }
        if (option.group_array_index != null) {
          classes.push("group-option");
        }
        if (option.classes !== "") {
          classes.push(option.classes);
        }
        style = option.style.cssText !== "" ? " style=\"" + option.style + "\"" : "";
        return '<li id="' + option.dom_id + '" class="' + classes.join(' ') + '"' + style + '>' + option.html + '</li>';
      } else {
        return "";
      }
    };
    Chosen.prototype.results_update_field = function() {
      this.result_clear_highlight();
      this.result_single_selected = null;
      return this.results_build();
    };
    Chosen.prototype.result_do_highlight = function(el) {
      var high_bottom, high_top, maxHeight, visible_bottom, visible_top;
      if (el.length) {
        this.result_clear_highlight();
        this.result_highlight = el;
        this.result_highlight.addClass("highlighted");
        maxHeight = parseInt(this.search_results.css("maxHeight"), 10);
        visible_top = this.search_results.scrollTop();
        visible_bottom = maxHeight + visible_top;
        high_top = this.result_highlight.position().top + this.search_results.scrollTop();
        high_bottom = high_top + this.result_highlight.outerHeight();
        if (high_bottom >= visible_bottom) {
          return this.search_results.scrollTop((high_bottom - maxHeight) > 0 ? high_bottom - maxHeight : 0);
        } else if (high_top < visible_top) {
          return this.search_results.scrollTop(high_top);
        }
      }
    };
    Chosen.prototype.result_clear_highlight = function() {
      if (this.result_highlight) {
        this.result_highlight.removeClass("highlighted");
      }
      return this.result_highlight = null;
    };
    Chosen.prototype.results_toggle = function() {
      if (this.results_showing) {
        return this.results_hide();
      } else {
        return this.results_show();
      }
    };
    Chosen.prototype.results_show = function() {
      var dd_top;
      if (!this.is_multiple) {
        this.selected_item.addClass("chzn-single-with-drop");
        if (this.result_single_selected) {
          this.result_do_highlight(this.result_single_selected);
        }
      }
      dd_top = this.is_multiple ? this.container.height() : this.container.height() - 1;
      this.dropdown.css({
        "top": dd_top + "px",
        "left": 0
      });
      this.results_showing = true;
      this.search_field.focus();
      this.search_field.val(this.search_field.val());
      return this.winnow_results();
    };
    Chosen.prototype.results_hide = function() {
      if (!this.is_multiple) {
        this.selected_item.removeClass("chzn-single-with-drop");
      }
      this.result_clear_highlight();
      this.dropdown.css({
        "left": "-9000px"
      });
      return this.results_showing = false;
    };
    Chosen.prototype.set_tab_index = function(el) {
      var ti;
      if (this.form_field_jq.attr("tabindex")) {
        ti = this.form_field_jq.attr("tabindex");
        this.form_field_jq.attr("tabindex", -1);
        if (this.is_multiple) {
          return this.search_field.attr("tabindex", ti);
        } else {
          this.selected_item.attr("tabindex", ti);
          return this.search_field.attr("tabindex", -1);
        }
      }
    };
    Chosen.prototype.show_search_field_default = function() {
      if (this.is_multiple && this.choices < 1 && !this.active_field) {
        this.search_field.val(this.default_text);
        return this.search_field.addClass("default");
      } else {
        this.search_field.val("");
        return this.search_field.removeClass("default");
      }
    };
    Chosen.prototype.search_results_mouseup = function(evt) {
      var target;
      target = $(evt.target).hasClass("active-result") ? $(evt.target) : $(evt.target).parents(".active-result").first();
      if (target.length) {
        this.result_highlight = target;
        return this.result_select(evt);
      }
    };
    Chosen.prototype.search_results_mouseover = function(evt) {
      var target;
      target = $(evt.target).hasClass("active-result") ? $(evt.target) : $(evt.target).parents(".active-result").first();
      if (target) {
        return this.result_do_highlight(target);
      }
    };
    Chosen.prototype.search_results_mouseout = function(evt) {
      if ($(evt.target).hasClass("active-result" || $(evt.target).parents('.active-result').first())) {
        return this.result_clear_highlight();
      }
    };
    Chosen.prototype.choices_click = function(evt) {
      evt.preventDefault();
      if (this.active_field && !($(evt.target).hasClass("search-choice" || $(evt.target).parents('.search-choice').first)) && !this.results_showing) {
        return this.results_show();
      }
    };
    Chosen.prototype.choice_build = function(item) {
      var choice_id, link;
      choice_id = this.container_id + "_c_" + item.array_index;
      this.choices += 1;
      this.search_container.before('<li class="search-choice" id="' + choice_id + '"><span>' + item.html + '</span><a href="javascript:void(0)" class="search-choice-close" rel="' + item.array_index + '"></a></li>');
      link = $('#' + choice_id).find("a").first();
      return link.click(__bind(function(evt) {
        return this.choice_destroy_link_click(evt);
      }, this));
    };
    Chosen.prototype.choice_destroy_link_click = function(evt) {
      evt.preventDefault();
      if (!this.is_disabled) {
        this.pending_destroy_click = true;
        return this.choice_destroy($(evt.target));
      } else {
        return evt.stopPropagation;
      }
    };
    Chosen.prototype.choice_destroy = function(link) {
      this.choices -= 1;
      this.show_search_field_default();
      if (this.is_multiple && this.choices > 0 && this.search_field.val().length < 1) {
        this.results_hide();
      }
      this.result_deselect(link.attr("rel"));
      return link.parents('li').first().remove();
    };
    Chosen.prototype.results_reset = function(evt) {
      this.form_field.options[0].selected = true;
      this.selected_item.find("span").text(this.default_text);
      this.show_search_field_default();
      $(evt.target).remove();
      this.form_field_jq.trigger("change");
      if (this.active_field) {
        return this.results_hide();
      }
    };
    Chosen.prototype.result_select = function(evt) {
      var high, high_id, item, position;
      if (this.result_highlight) {
        high = this.result_highlight;
        high_id = high.attr("id");
        this.result_clear_highlight();
        if (this.is_multiple) {
          this.result_deactivate(high);
        } else {
          this.search_results.find(".result-selected").removeClass("result-selected");
          this.result_single_selected = high;
        }
        high.addClass("result-selected");
        position = high_id.substr(high_id.lastIndexOf("_") + 1);
        item = this.results_data[position];
        item.selected = true;
        this.form_field.options[item.options_index].selected = true;
        if (this.is_multiple) {
          this.choice_build(item);
        } else {
          this.selected_item.find("span").first().text(item.text);
          if (this.allow_single_deselect) {
            this.selected_item.find("span").first().after("<abbr class=\"search-choice-close\"></abbr>");
          }
        }
        if (!(evt.metaKey && this.is_multiple)) {
          this.results_hide();
        }
        this.search_field.val("");
        this.form_field_jq.trigger("change");
        return this.search_field_scale();
      }
    };
    Chosen.prototype.result_activate = function(el) {
      return el.addClass("active-result");
    };
    Chosen.prototype.result_deactivate = function(el) {
      return el.removeClass("active-result");
    };
    Chosen.prototype.result_deselect = function(pos) {
      var result, result_data;
      result_data = this.results_data[pos];
      result_data.selected = false;
      this.form_field.options[result_data.options_index].selected = false;
      result = $("#" + this.container_id + "_o_" + pos);
      result.removeClass("result-selected").addClass("active-result").show();
      this.result_clear_highlight();
      this.winnow_results();
      this.form_field_jq.trigger("change");
      return this.search_field_scale();
    };
    Chosen.prototype.results_search = function(evt) {
      if (this.results_showing) {
        return this.winnow_results();
      } else {
        return this.results_show();
      }
    };
    Chosen.prototype.winnow_results = function() {
      var found, option, part, parts, regex, result_id, results, searchText, startTime, startpos, text, zregex, _i, _j, _len, _len2, _ref;
      startTime = new Date();
      this.no_results_clear();
      results = 0;
      searchText = this.search_field.val() === this.default_text ? "" : $('<div/>').text($.trim(this.search_field.val())).html();
      regex = new RegExp('^' + searchText.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), 'i');
      zregex = new RegExp(searchText.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), 'i');
      _ref = this.results_data;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        option = _ref[_i];
        if (!option.disabled && !option.empty) {
          if (option.group) {
            $('#' + option.dom_id).hide();
          } else if (!(this.is_multiple && option.selected)) {
            found = false;
            result_id = option.dom_id;
            if (regex.test(option.html)) {
              found = true;
              results += 1;
            } else if (option.html.indexOf(" ") >= 0 || option.html.indexOf("[") === 0) {
              parts = option.html.replace(/\[|\]/g, "").split(" ");
              if (parts.length) {
                for (_j = 0, _len2 = parts.length; _j < _len2; _j++) {
                  part = parts[_j];
                  if (regex.test(part)) {
                    found = true;
                    results += 1;
                  }
                }
              }
            }
            if (found) {
              if (searchText.length) {
                startpos = option.html.search(zregex);
                text = option.html.substr(0, startpos + searchText.length) + '</em>' + option.html.substr(startpos + searchText.length);
                text = text.substr(0, startpos) + '<em>' + text.substr(startpos);
              } else {
                text = option.html;
              }
              if ($("#" + result_id).html !== text) {
                $("#" + result_id).html(text);
              }
              this.result_activate($("#" + result_id));
              if (option.group_array_index != null) {
                $("#" + this.results_data[option.group_array_index].dom_id).show();
              }
            } else {
              if (this.result_highlight && result_id === this.result_highlight.attr('id')) {
                this.result_clear_highlight();
              }
              this.result_deactivate($("#" + result_id));
            }
          }
        }
      }
      if (results < 1 && searchText.length) {
        return this.no_results(searchText);
      } else {
        return this.winnow_results_set_highlight();
      }
    };
    Chosen.prototype.winnow_results_clear = function() {
      var li, lis, _i, _len, _results;
      this.search_field.val("");
      lis = this.search_results.find("li");
      _results = [];
      for (_i = 0, _len = lis.length; _i < _len; _i++) {
        li = lis[_i];
        li = $(li);
        _results.push(li.hasClass("group-result") ? li.show() : !this.is_multiple || !li.hasClass("result-selected") ? this.result_activate(li) : void 0);
      }
      return _results;
    };
    Chosen.prototype.winnow_results_set_highlight = function() {
      var do_high, selected_results;
      if (!this.result_highlight) {
        selected_results = !this.is_multiple ? this.search_results.find(".result-selected.active-result") : [];
        do_high = selected_results.length ? selected_results.first() : this.search_results.find(".active-result").first();
        if (do_high != null) {
          return this.result_do_highlight(do_high);
        }
      }
    };
    Chosen.prototype.no_results = function(terms) {
      var no_results_html;
      no_results_html = $('<li class="no-results">' + this.results_none_found + ' "<span></span>"</li>');
      no_results_html.find("span").first().html(terms);
      return this.search_results.append(no_results_html);
    };
    Chosen.prototype.no_results_clear = function() {
      return this.search_results.find(".no-results").remove();
    };
    Chosen.prototype.keydown_arrow = function() {
      var first_active, next_sib;
      if (!this.result_highlight) {
        first_active = this.search_results.find("li.active-result").first();
        if (first_active) {
          this.result_do_highlight($(first_active));
        }
      } else if (this.results_showing) {
        next_sib = this.result_highlight.nextAll("li.active-result").first();
        if (next_sib) {
          this.result_do_highlight(next_sib);
        }
      }
      if (!this.results_showing) {
        return this.results_show();
      }
    };
    Chosen.prototype.keyup_arrow = function() {
      var prev_sibs;
      if (!this.results_showing && !this.is_multiple) {
        return this.results_show();
      } else if (this.result_highlight) {
        prev_sibs = this.result_highlight.prevAll("li.active-result");
        if (prev_sibs.length) {
          return this.result_do_highlight(prev_sibs.first());
        } else {
          if (this.choices > 0) {
            this.results_hide();
          }
          return this.result_clear_highlight();
        }
      }
    };
    Chosen.prototype.keydown_backstroke = function() {
      if (this.pending_backstroke) {
        this.choice_destroy(this.pending_backstroke.find("a").first());
        return this.clear_backstroke();
      } else {
        this.pending_backstroke = this.search_container.siblings("li.search-choice").last();
        return this.pending_backstroke.addClass("search-choice-focus");
      }
    };
    Chosen.prototype.clear_backstroke = function() {
      if (this.pending_backstroke) {
        this.pending_backstroke.removeClass("search-choice-focus");
      }
      return this.pending_backstroke = null;
    };
    Chosen.prototype.keyup_checker = function(evt) {
      var stroke, _ref;
      stroke = (_ref = evt.which) != null ? _ref : evt.keyCode;
      this.search_field_scale();
      switch (stroke) {
        case 8:
          if (this.is_multiple && this.backstroke_length < 1 && this.choices > 0) {
            return this.keydown_backstroke();
          } else if (!this.pending_backstroke) {
            this.result_clear_highlight();
            return this.results_search();
          }
          break;
        case 13:
          evt.preventDefault();
          if (this.results_showing) {
            return this.result_select(evt);
          }
          break;
        case 27:
          if (this.results_showing) {
            return this.results_hide();
          }
          break;
        case 9:
        case 38:
        case 40:
        case 16:
        case 91:
        case 17:
          break;
        default:
          return this.results_search();
      }
    };
    Chosen.prototype.keydown_checker = function(evt) {
      var stroke, _ref;
      stroke = (_ref = evt.which) != null ? _ref : evt.keyCode;
      this.search_field_scale();
      if (stroke !== 8 && this.pending_backstroke) {
        this.clear_backstroke();
      }
      switch (stroke) {
        case 8:
          this.backstroke_length = this.search_field.val().length;
          break;
        case 9:
          this.mouse_on_container = false;
          break;
        case 13:
          evt.preventDefault();
          break;
        case 38:
          evt.preventDefault();
          this.keyup_arrow();
          break;
        case 40:
          this.keydown_arrow();
          break;
      }
    };
    Chosen.prototype.search_field_scale = function() {
      var dd_top, div, h, style, style_block, styles, w, _i, _len;
      if (this.is_multiple) {
        h = 0;
        w = 0;
        style_block = "position:absolute; left: -1000px; top: -1000px; display:none;";
        styles = ['font-size', 'font-style', 'font-weight', 'font-family', 'line-height', 'text-transform', 'letter-spacing'];
        for (_i = 0, _len = styles.length; _i < _len; _i++) {
          style = styles[_i];
          style_block += style + ":" + this.search_field.css(style) + ";";
        }
        div = $('<div />', {
          'style': style_block
        });
        div.text(this.search_field.val());
        $('body').append(div);
        w = div.width() + 25;
        div.remove();
        if (w > this.f_width - 10) {
          w = this.f_width - 10;
        }
        this.search_field.css({
          'width': w + 'px'
        });
        dd_top = this.container.height();
        return this.dropdown.css({
          "top": dd_top + "px"
        });
      }
    };
    Chosen.prototype.generate_field_id = function() {
      var new_id;
      new_id = this.generate_random_id();
      this.form_field.id = new_id;
      return new_id;
    };
    Chosen.prototype.generate_random_id = function() {
      var string;
      string = "sel" + this.generate_random_char() + this.generate_random_char() + this.generate_random_char();
      while ($("#" + string).length > 0) {
        string += this.generate_random_char();
      }
      return string;
    };
    Chosen.prototype.generate_random_char = function() {
      var chars, newchar, rand;
      chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
      rand = Math.floor(Math.random() * chars.length);
      return newchar = chars.substring(rand, rand + 1);
    };
    return Chosen;
  })();
  get_side_border_padding = function(elmt) {
    var side_border_padding;
    return side_border_padding = elmt.outerWidth() - elmt.width();
  };
  root.get_side_border_padding = get_side_border_padding;
}).call(this);
(function() {
  var SelectParser;
  SelectParser = (function() {
    function SelectParser() {
      this.options_index = 0;
      this.parsed = [];
    }
    SelectParser.prototype.add_node = function(child) {
      if (child.nodeName === "OPTGROUP") {
        return this.add_group(child);
      } else {
        return this.add_option(child);
      }
    };
    SelectParser.prototype.add_group = function(group) {
      var group_position, option, _i, _len, _ref, _results;
      group_position = this.parsed.length;
      this.parsed.push({
        array_index: group_position,
        group: true,
        label: group.label,
        children: 0,
        disabled: group.disabled
      });
      _ref = group.childNodes;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        option = _ref[_i];
        _results.push(this.add_option(option, group_position, group.disabled));
      }
      return _results;
    };
    SelectParser.prototype.add_option = function(option, group_position, group_disabled) {
      if (option.nodeName === "OPTION") {
        if (option.text !== "") {
          if (group_position != null) {
            this.parsed[group_position].children += 1;
          }
          this.parsed.push({
            array_index: this.parsed.length,
            options_index: this.options_index,
            value: option.value,
            text: option.text,
            html: option.innerHTML,
            selected: option.selected,
            disabled: group_disabled === true ? group_disabled : option.disabled,
            group_array_index: group_position,
            classes: option.className,
            style: option.style.cssText
          });
        } else {
          this.parsed.push({
            array_index: this.parsed.length,
            options_index: this.options_index,
            empty: true
          });
        }
        return this.options_index += 1;
      }
    };
    return SelectParser;
  })();
  SelectParser.select_to_array = function(select) {
    var child, parser, _i, _len, _ref;
    parser = new SelectParser();
    _ref = select.childNodes;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      child = _ref[_i];
      parser.add_node(child);
    }
    return parser.parsed;
  };
  this.SelectParser = SelectParser;
}).call(this);


var ccm_totalAdvancedSearchFields = 0;
var ccm_alLaunchType = new Array();
var ccm_alActiveAssetField = "";
var ccm_alProcessorTarget = "";
var ccm_alDebug = false;

ccm_triggerSelectFile = function(fID, af) {
	if (af == null) {
		var af = ccm_alActiveAssetField;
	}
	//alert(af);
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	dobj.hide();
	obj.show();
	obj.load(CCM_TOOLS_PATH + '/files/selector_data?fID=' + fID + '&ccm_file_selected_field=' + af, function() {
		/*
		$(this).find('a.ccm-file-manager-clear-asset').click(function(e) {
			var field = $(this).attr('ccm-file-manager-field');
			ccm_clearFile(e, field);
		});
		*/
		obj.attr('fID', fID);
		obj.attr('ccm-file-manager-can-view', obj.children('div').attr('ccm-file-manager-can-view'));
		obj.attr('ccm-file-manager-can-edit', obj.children('div').attr('ccm-file-manager-can-edit'));
		obj.attr('ccm-file-manager-can-admin', obj.children('div').attr('ccm-file-manager-can-admin'));
		obj.attr('ccm-file-manager-can-replace', obj.children('div').attr('ccm-file-manager-can-replace'));
		
		obj.click(function(e) {
			e.stopPropagation();
			ccm_alActivateMenu($(this),e);
		});
		
		if (typeof(ccm_triggerSelectFileComplete)  == 'function') {
			ccm_triggerSelectFileComplete(fID, af);
		}
	});
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', fID);
	ccm_alSetupFileProcessor();
}

ccm_alGetFileData = function(fID, onComplete) {
	$.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?fID=' + fID, function(resp) {
		onComplete(resp);
	});
}

ccm_clearFile = function(e, af) {
	e.stopPropagation();
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}

ccm_activateFileManager = function(altype, searchInstance) {
	//delegate event handling to table container so clicks
	//to our star don't interfer with clicks to our rows
	ccm_alSetupSelectFiles(searchInstance);
	
	$(document).click(function(e) {		
		e.stopPropagation();
		ccm_alSelectNone();
	});

	ccm_setupAdvancedSearch(searchInstance);
	
	if (altype == 'DASHBOARD') {
		$(".dialog-launch").dialog();
	}
	
	ccm_alLaunchType[searchInstance] = altype;
	
	ccm_alSetupCheckboxes(searchInstance);
	ccm_alSetupFileProcessor();
	ccm_alSetupSingleUploadForm();
	
	$("form#ccm-" + searchInstance + "-advanced-search select[name=fssID]").change(function() {
		if (altype == 'DASHBOARD') { 
			window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/files/search?fssID=' + $(this).val();
		} else {
			jQuery.fn.dialog.showLoader();
			var url = $("div#ccm-" + searchInstance + "-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + $(this).val();
			$.get(url, function(resp) {
				jQuery.fn.dialog.hideLoader();
				$("div#ccm-" + searchInstance + "-overlay-wrapper").html(resp);
				$("div#ccm-" + searchInstance + "-overlay-wrapper a.dialog-launch").dialog();
			});
		}
	});

	ccm_searchActivatePostFunction[searchInstance] = function() {
		ccm_alSetupCheckboxes(searchInstance);
		ccm_alSetupSelectFiles();
	}
	
	
	// setup upload form
}

ccm_alSetupSingleUploadForm = function() {
	$(".ccm-file-manager-submit-single").submit(function() {  
		$(this).attr('target', ccm_alProcessorTarget);
		ccm_alSubmitSingle($(this).get(0));	 
	});
}

ccm_activateFileSelectors = function() {
	$(".ccm-file-manager-launch").unbind();
	$(".ccm-file-manager-launch").click(function() {
		ccm_alLaunchSelectorFileManager($(this).parent().attr('ccm-file-manager-field'));	
	});
}

ccm_alLaunchSelectorFileManager = function(selector) {
	ccm_alActiveAssetField = selector;
	var filterStr = "";
	
	var types = $('#' + selector + '-fm-display input.ccm-file-manager-filter');
	if (types.length) {
		for (i = 0; i < types.length; i++) {
			filterStr += '&' + $(types[i]).attr('name') + '=' + $(types[i]).attr('value');		
		}
	}
	
	ccm_launchFileManager(filterStr);
}

// public method - do not remove or rename
ccm_launchFileManager = function(filters) {
	$.fn.dialog.open({
		width: '90%',
		height: '70%',
		appendButtons: true,
		modal: false,
		href: CCM_TOOLS_PATH + "/files/search_dialog?ocID=" + CCM_CID + "&search=1" + filters,
		title: ccmi18n_filemanager.title
	});
}

ccm_launchFileSetPicker = function(fsID) {
	$.fn.dialog.open({
		width: 500,
		height: 160,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/pick_set?oldFSID=' + fsID,
		title: ccmi18n_filemanager.sets				
	});
}

ccm_alSubmitSetsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery.fn.dialog.showLoader();
	$("#ccm-" + searchInstance + "-add-to-set-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();		
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			$("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
				$(".chosen-select").chosen();
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alSubmitPasswordForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-password-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitStorageForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-storage-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitPermissionsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-permissions-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

		
ccm_alSetupSetsForm = function(searchInstance) {
	// activate file set search
	$('#fsAddToSearchName').liveUpdate('ccm-file-search-add-to-sets-list', 'fileset');

	// Setup the tri-state checkboxes
	$(".ccm-file-set-add-cb a").each(function() {
		var cb = $(this);
		var startingState = cb.attr("ccm-tri-state-startup");
		$(this).click(function() {
			var selectedState = $(this).attr("ccm-tri-state-selected");
			var toSetState = 0;
			switch(selectedState) {
				case '0':
					if (startingState == '1') {
						toSetState = '1';
					} else {
						toSetState = '2';
					}
					break;
				case '1':
					toSetState = '2';
					break;
				case '2':
					toSetState = '0';
					break;
			}
			
			$(this).attr('ccm-tri-state-selected', toSetState);
			$(this).find('input').val(toSetState);
			$(this).find('img').attr('src', CCM_IMAGE_PATH + '/checkbox_state_' + toSetState + '.png');
		});
	});
	$("#ccm-" + searchInstance + "-add-to-set-form input[name=fsNew]").click(function() {
		if (!$(this).prop('checked')) {
			$("#ccm-" + searchInstance + "-add-to-set-form input[name=fsNewText]").val('');
		}
	});
	$("#ccm-" + searchInstance + "-add-to-set-form").submit(function() {
		ccm_alSubmitSetsForm(searchInstance);
		return false;
	});
}

ccm_alSetupPasswordForm = function() {
	$("#ccm-file-password-form").submit(function() {
		ccm_alSubmitPasswordForm();
		return false;
	});
}	
ccm_alRescanFiles = function() {
	var turl = CCM_TOOLS_PATH + '/files/rescan?';
	var files = arguments;
	for (i = 0; i < files.length; i++) {
		turl += 'fID[]=' + files[i] + '&';
	}
	$.fn.dialog.open({
		title: ccmi18n_filemanager.rescan,
		href: turl,
		width: 350,
		modal: false,
		height: 200,
		onClose: function() {
			if (files.length == 1) {
				$('#ccm-file-properties-wrapper').html('');
				jQuery.fn.dialog.showLoader();
				
				// open the properties window for this bad boy.
				$("#ccm-file-properties-wrapper").load(CCM_TOOLS_PATH + '/files/properties?fID=' + files[0] + '&reload=1', false, function() {
					jQuery.fn.dialog.hideLoader();
					$(this).find(".dialog-launch").dialog();

				});				
			}
		}
	});
}

	
ccm_alSelectPermissionsEntity = function(selector, id, name) {
	var html = $('#ccm-file-permissions-entity-base').html();
	$('#ccm-file-permissions-entities-wrapper').append('<div class="ccm-file-permissions-entity">' + html + '<\/div>');
	var p = $('.ccm-file-permissions-entity');
	var ap = p[p.length - 1];
	$(ap).find('h2 span').html(name);
	$(ap).find('input[type=hidden]').val(selector + '_' + id);
	$(ap).find('input[type=radio]').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id);
	});
	$(ap).find('div.ccm-file-access-extensions input[type=checkbox]').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id + '[]');
	});
	
	ccm_alActivateFilePermissionsSelector();	
}

ccm_alActivateFilePermissionsSelector = function() {
	$("tr.ccm-file-access-add input").unbind();
	$("tr.ccm-file-access-add input").click(function() {
		var p = $(this).parents('div.ccm-file-permissions-entity')[0];
		if ($(this).val() == ccmi18n_filemanager.PTYPE_CUSTOM) {
			$(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			$(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});
	$("tr.ccm-file-access-file-manager input").click(function() {
		var p = $(this).parents('div.ccm-file-permissions-entity')[0];
		if ($(this).val() != ccmi18n_filemanager.PTYPE_NONE) {
			$(p).find('tr.ccm-file-access-add').show();				
			$(p).find('tr.ccm-file-access-edit').show();				
			$(p).find('tr.ccm-file-access-admin').show();
			//$(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			$(p).find('tr.ccm-file-access-add').hide();				
			$(p).find('tr.ccm-file-access-edit').hide();				
			$(p).find('tr.ccm-file-access-admin').hide();				
			$(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});


	$("a.ccm-file-permissions-remove").click(function() {
		$(this).parent().parent().fadeOut(100, function() {
			$(this).remove();
		});
	});
	$("input[name=toggleCanAddExtension]").unbind();
	$("input[name=toggleCanAddExtension]").click(function() {
		var ext = $(this).parent().parent().find('div.ccm-file-access-extensions');
		
		if ($(this).prop('checked') == 1) {
			ext.find('input').attr('checked', true);
		} else {
			ext.find('input').attr('checked', false);
		}
	});
}

ccm_alSetupVersionSelector = function() {
	$("#ccm-file-versions-grid input[type=radio]").click(function() {
		$('#ccm-file-versions-grid tr').removeClass('ccm-file-versions-grid-active');
		
		var trow = $(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=approve_version&fID=' + fID + '&fvID=' + fvID;
		$.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.addClass('ccm-file-versions-grid-active');
			trow.find('td').show('highlight', {
				color: '#FFF9BB'
			});
		});
	});
	
	$(".ccm-file-versions-remove").click(function() {
		var trow = $(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=delete_version&fID=' + fID + '&fvID=' + fvID;
		$.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.fadeOut(200, function() {
				trow.remove();
			});
		});
		return false;
	});
}

ccm_alDeleteFiles = function(searchInstance) {
	$("#ccm-" + searchInstance + "-delete-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alDuplicateFiles = function(searchInstance) {
	$("#ccm-" + searchInstance + "-duplicate-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			var r = eval('(' + resp + ')');

			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
				var highlight = new Array();
				for (i = 0; i < r.fID.length; i++ ){
					fID = r.fID[i];
					ccm_uploadedFiles.push(fID);
					highlight.push(fID);
				}
				ccm_alRefresh(highlight, searchInstance);
				ccm_filesUploadedDialog(searchInstance);				
			});
		});
	});
}

ccm_alSetupSelectFiles = function() {
	$('.ccm-file-list').unbind();
	/*
	$('.ccm-file-list').click(function(e){
		e.stopPropagation();
		if ($(e.target).is('img.ccm-star')) {	
			var fID = $(e.target).parents('tr.ccm-list-record')[0].id;
			fID = fID.substring(3);
			ccm_starFile(e.target,fID);
		}
		else{
			$(e.target).parents('tr.ccm-list-record').each(function(){
				ccm_alActivateMenu($(this), e);		
			});
		}
	});
	*/
	
	$('.ccm-file-list tr.ccm-list-record').click(function(e) {
		e.stopPropagation();
		ccm_alActivateMenu($(this), e);
	});
	$('.ccm-file-list img.ccm-star').click(function(e) {
		e.stopPropagation();
		var fID = $(e.target).parents('tr.ccm-list-record')[0].id;
		fID = fID.substring(3);
		ccm_starFile(e.target,fID);
	});
	$(".ccm-file-list-thumbnail").hover(function(e) { 
		var fID = $(this).attr('fID');
		var obj = $('#fID' + fID + 'hoverThumbnail'); 
		if (obj.length > 0) { 
			var tdiv = obj.find('div');
			var pos = obj.position();
			tdiv.css('top', pos.top);
			tdiv.css('left', pos.left);
			tdiv.show();
		}
	}, function() {
		var fID = $(this).attr('fID');
		var obj = $('#fID' + fID + 'hoverThumbnail');
		var tdiv = obj.find('div');
		tdiv.hide(); 
	});
}

ccm_alSetupCheckboxes = function(searchInstance) {
	$("#ccm-" + searchInstance + "-list-cb-all").unbind();	
	$("#ccm-" + searchInstance + "-list-cb-all").click(function() {
		ccm_hideMenus();
		if ($(this).prop('checked') == true) {
			$('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
		} else {
			$('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
		}
	});
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		$(this).find('input[type=checkbox]').click();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu
	if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
		var chooseText = ccmi18n_filemanager.select;
		$("#ccm-" + searchInstance + "-list-multiple-operations option:eq(0)").after("<option value=\"choose\">" + chooseText + "</option>");
	}
	$("#ccm-" + searchInstance + "-list-multiple-operations").change(function() {
		var action = $(this).val();
		var fIDstring = ccm_alGetSelectedFileIDs(searchInstance);
		switch(action) {
			case 'choose':
				var fIDs = new Array();
				$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
					fIDs.push($(this).val());
				});
				ccm_alSelectFile(fIDs, true);
				break;
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/delete?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.deleteFile				
				});
				break;
			case "duplicate":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/duplicate?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.duplicateFile				
				});
				break;
			case "sets":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/add_to?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.sets				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 690,
					height: 440,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/bulk_properties?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n.properties				
				});
				break;				
			case "rescan":
				jQuery.fn.dialog.open({
					width: 350,
					height: 200,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/rescan?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.rescan,
					onClose: function() {
						$("#ccm-" + searchInstance + "-advanced-search").submit();			
					}
				});
				break;
			case "download":
				window.frames[ccm_alProcessorTarget].location = CCM_TOOLS_PATH + '/files/download?' + fIDstring;
				break;
		}
		
		$(this).get(0).selectedIndex = 0;
	});

	// activate the file sets checkboxes
	ccm_alSetupFileSetSearch(searchInstance);
}

ccm_alSetupFileSetSearch = function(searchInstance) {
	$("#ccm-" + searchInstance + "-sets-search-wrapper select").chosen().unbind();
	$("#ccm-" + searchInstance + "-sets-search-wrapper select").chosen().change(function() {
		var sel = $("#ccm-" + searchInstance + "-sets-search-wrapper option:selected");
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});

	/*
	$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").unbind();
	$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").click(function() {
		$("input[name=fsIDNone][instance=" + searchInstance + "]").attr('checked', false);
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});
	
	// activate file set search
	$('div.ccm-file-sets-search-wrapper-input input').liveUpdate('ccm-file-search-advanced-sets-list', 'fileset');
	
	$("input[name=fsIDNone][instance=" + searchInstance + "]").unbind();
	$("input[name=fsIDNone][instance=" + searchInstance + "]").click(function() {
		if ($(this).prop('checked')) {
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('checked', false);
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', true);
		} else {
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', false);
		}
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});
	*/
}


ccm_alGetSelectedFileIDs = function(searchInstance) {
	var fidstr = '';
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
		fidstr += 'fID[]=' + $(this).val() + '&';
	});
	return fidstr;
}

ccm_alRescanMultiFileMenu = function(searchInstance) {
	if ($("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").length > 0) {
		$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
	} else {
		$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
	}
}

ccm_alSetupFileProcessor = function() {
	if (ccm_alProcessorTarget != '') {
		return false;
	}
	
	var ts = parseInt(new Date().getTime().toString().substring(0, 10)); 
	var ifr; 
	try { //IE7 hack
	  ifr = document.createElement('<iframe name="ccm-al-upload-processor'+ts+'">');
	} catch (ex) {
	  ifr = document.createElement('iframe');
	}	
	ifr.id = 'ccm-al-upload-processor' + ts;
	ifr.name = 'ccm-al-upload-processor' + ts;
	ifr.style.border='0px';
	ifr.style.width='0px';
	ifr.style.height='0px';
	ifr.style.display = "none";
	document.body.appendChild(ifr);
	
	if (ccm_alDebug) {
		ccm_alProcessorTarget = "_blank";
	} else {
		ccm_alProcessorTarget = 'ccm-al-upload-processor' + ts;
	}
}

ccm_alSubmitSingle = function(form) {
	if ($(form).find(".ccm-al-upload-single-file").val() == '') { 
		alert(ccmi18n_filemanager.uploadErrorChooseFile);
		return false;
	} else { 
		$(form).find('.ccm-al-upload-single-submit').hide();
		$(form).find('.ccm-al-upload-single-loader').show();
	}
}

ccm_alResetSingle = function () {
	$('.ccm-al-upload-single-file').val('');
	$('.ccm-al-upload-single-loader').hide();
	$('.ccm-al-upload-single-submit').show();
}

var ccm_uploadedFiles=[];
ccm_filesUploadedDialog = function(searchInstance) { 
	if(document.getElementById('ccm-file-upload-multiple-tab')) 
		jQuery.fn.dialog.closeTop()
	var fIDstring='';
	for( var i=0; i< ccm_uploadedFiles.length; i++ )
		fIDstring=fIDstring+'&fID[]='+ccm_uploadedFiles[i];
	jQuery.fn.dialog.open({
		width: 690,
		height: 440,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/bulk_properties/?'+fIDstring + '&uploaded=true&searchInstance=' + searchInstance,
		onClose: function() {
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		},
		title: ccmi18n_filemanager.uploadComplete				
	});
	ccm_uploadedFiles=[];
}

ccm_alSetupUploadDetailsForm = function(searchInstance) {
	$("#ccm-" + searchInstance + "-update-uploaded-details-form").submit(function() {
		ccm_alSubmitUploadDetailsForm(searchInstance);
		return false;
	});
}

ccm_alSubmitUploadDetailsForm = function(searchInstance) {
	jQuery.fn.dialog.showLoader();
	$("#ccm-" + searchInstance + "-update-uploaded-details-form").ajaxSubmit(function(r1) {
		var r1a = eval('(' + r1 + ')');
		var form = $("#ccm-" + searchInstance + "-advanced-search");
		if (form.length > 0) {
			form.ajaxSubmit(function(resp) {
				$("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
					jQuery.fn.dialog.hideLoader();
					jQuery.fn.dialog.closeTop();
					ccm_parseAdvancedSearchResponse(resp, searchInstance);
					ccm_alHighlightFileIDArray(r1a);
				});
			});
		} else {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
}

ccm_alRefresh = function(highlightFIDs, searchInstance, fileSelector) {
	var ids = highlightFIDs;
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-search-results").load(CCM_TOOLS_PATH + '/files/search_results', {
		'ccm_order_by': 'fvDateAdded',
		'ccm_order_dir': 'desc', 
		'fileSelector': fileSelector,
		'searchInstance': searchInstance
	}, function() {
		ccm_activateSearchResults(searchInstance);
		if (ids != false) {
			ccm_alHighlightFileIDArray(ids);
		}
		ccm_alSetupSelectFiles();

	});
}

ccm_alHighlightFileIDArray = function(ids) {
	for (i = 0; i < ids.length; i++) {
		var td = $('tr[fID=' + ids[i] + '] td');
		var oldBG = td.css('backgroundColor');
		td.animate({ backgroundColor: '#FFF9BB'}, { queue: true, duration: 1000 }).animate( {backgroundColor: oldBG}, 500);
	}
}

ccm_alSelectFile = function(fID) {
	
	if (typeof(ccm_chooseAsset) == 'function') {
		var qstring = '';
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				qstring += 'fID[]=' + fID[i] + '&';
			}
		} else {
			qstring += 'fID=' + fID;
		}
		
		$.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?' + qstring, function(resp) {
			ccm_parseJSON(resp, function() {
				for(i = 0; i < resp.length; i++) {
					ccm_chooseAsset(resp[i]);
				}
				jQuery.fn.dialog.closeTop();
			});
		});
		
	} else {
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				ccm_triggerSelectFile(fID[i]);
			}
		} else {
			ccm_triggerSelectFile(fID);
		}
		jQuery.fn.dialog.closeTop();	
	}

}

ccm_alActivateMenu = function(obj, e) {
	
	// Is this a file that's already been chosen that we're selecting?
	// If so, we need to offer the reset switch
	
	var selectedFile = $(obj).find('div[ccm-file-manager-field]');
	var selector = '';
	if(selectedFile.length > 0) {
		selector = selectedFile.attr('ccm-file-manager-field');
	}
	ccm_hideMenus();
	
	var fID = $(obj).attr('fID');
	var searchInstance = $(obj).attr('ccm-file-manager-instance');

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-al-menu" + fID + searchInstance + selector);
	
	// This immediate click mode has promise, but it's annoying more than it's helpful
	/*
	if (ccm_alLaunchType != 'DASHBOARD' && selector == '') {
		// then we are in file list mode in the site, which means we 
		// we don't give out all the options in the list
		ccm_alSelectFile(fID);
		return;
	}
	*/
	
	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-al-menu" + fID + searchInstance + selector;
		el.className = "ccm-menu ccm-ui";
		el.style.display = "none";
		document.body.appendChild(el);
		
		var filepath = $(obj).attr('al-filepath'); 
		bobj = $("#ccm-al-menu" + fID + searchInstance + selector);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
		html += '<ul>';
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
			// if we're launching this at the selector level, that means we've already chosen a file, and this should instead launch the library
			var onclick = (selectedFile.length > 0) ? 'ccm_alLaunchSelectorFileManager(\'' + selector + '\')' : 'ccm_alSelectFile(' + fID + ')';
			var chooseText = (selectedFile.length > 0) ? ccmi18n_filemanager.chooseNew : ccmi18n_filemanager.select;
			html += '<li><a class="ccm-menu-icon ccm-icon-choose-file-menu" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.select + '" id="menuSelectFile' + fID + '" href="javascript:void(0)" onclick="' + onclick + '">'+ chooseText + '<\/a><\/li>';
		}
		if (selectedFile.length > 0) {
			html += '<li><a class="ccm-menu-icon ccm-icon-clear-file-menu" href="javascript:void(0)" id="menuClearFile' + fID + searchInstance + selector + '">'+ ccmi18n_filemanager.clear + '<\/a><\/li>';
		}
		
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD'  && ccm_alLaunchType[searchInstance] != 'BROWSE' && selectedFile.length > 0) {
			html += '<li class="ccm-menu-separator"></li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-view') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-view dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" id="menuView' + fID + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=' + fID + '">'+ ccmi18n_filemanager.view + '<\/a><\/li>';
		} else {
			html += '<li><a class="ccm-menu-icon ccm-icon-download-menu" id="menuDownload' + fID + '" target="' + ccm_alProcessorTarget + '" href="' + CCM_TOOLS_PATH + '/files/download?fID=' + fID + '">'+ ccmi18n_filemanager.download + '<\/a><\/li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-edit') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-edit-menu dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.edit + '" id="menuEdit' + fID + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=' + fID + '">'+ ccmi18n_filemanager.edit + '<\/a><\/li>';
		}
		html += '<li><a class="ccm-menu-icon ccm-icon-properties-menu dialog-launch" dialog-modal="false" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + fID + '" href="' + CCM_TOOLS_PATH + '/files/properties?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.properties + '<\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-replace') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-replace dialog-launch" dialog-modal="false" dialog-width="300" dialog-height="250" dialog-title="' + ccmi18n_filemanager.replace + '" id="menuFileReplace' + fID + '" href="' + CCM_TOOLS_PATH + '/files/replace?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.replace + '<\/a><\/li>';
		}
		if ($(obj).attr('ccm-file-manager-can-duplicate') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-copy-menu" id="menuFileDuplicate' + fID + '" href="javascript:void(0)" onclick="ccm_alDuplicateFile(' + fID + ',\'' + searchInstance + '\')">'+ ccmi18n_filemanager.duplicate + '<\/a><\/li>';
		}
		html += '<li><a class="ccm-menu-icon ccm-icon-sets dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" id="menuFileSets' + fID + '" href="' + CCM_TOOLS_PATH + '/files/add_to?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.sets + '<\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-admin') == '1' || $(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li class="ccm-menu-separator"></li>';
		}
		if ($(obj).attr('ccm-file-manager-can-admin') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-access-permissions dialog-launch" dialog-modal="false" dialog-width="400" dialog-height="380" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + fID + '" href="' + CCM_TOOLS_PATH + '/files/permissions?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.permissions + '<\/a><\/li>';
		}
		if ($(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li><a class="ccm-icon-delete-menu ccm-menu-icon dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + fID + '" href="' + CCM_TOOLS_PATH + '/files/delete?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.deleteFile + '<\/a><\/li>';
		}
		html += '</ul>';
		html += '</div></div></div>';
		bobj.append(html);

		$(bobj).find('a').bind('click.hide-menu', function(e) {
			ccm_hideMenus();
			return false;	
		});
		
		$("#ccm-al-menu" + fID + searchInstance + selector + " a.dialog-launch").dialog();
		
		$('a#menuClearFile' + fID + searchInstance + selector).click(function(e) {
			ccm_clearFile(e, selector);
			ccm_hideMenus();
		});

	} else {
		bobj = $("#ccm-al-menu" + fID + searchInstance + selector);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_alSelectNone = function() {
	ccm_hideMenus();
}

var checkbox_status = false;
toggleCheckboxStatus = function(form) {
	if(checkbox_status) {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = false;
			}
		}	
		checkbox_status = false;
	}
	else {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = true;
			}
		}	
		checkbox_status = true;	
	}
}	

ccm_alDuplicateFile = function(fID, searchInstance) {
	var postStr = 'fID=' + fID + '&searchInstance=' + searchInstance;
	
	$.post(CCM_TOOLS_PATH + '/files/duplicate', postStr, function(resp) {
		var r = eval('(' + resp + ')');
		
		if (r.error == 1) {
		 	ccmAlert.notice(ccmi18n.error, r.message);		
		 	return false;
		 }
		
		
		var highlight = new Array();
		if (r.fID) {
			highlight.push(r.fID);
			ccm_alRefresh(highlight, searchInstance);
			ccm_uploadedFiles.push(r.fID);
			ccm_filesUploadedDialog(searchInstance);
		}
	});
}

ccm_alSelectMultipleIncomingFiles = function(obj) {
	if ($(obj).prop('checked')) {
		$("input.ccm-file-select-incoming").attr('checked', true);
	} else {
		$("input.ccm-file-select-incoming").attr('checked', false);
	}
}

ccm_starFile = function (img,fID) {				
	var action = '';
	if ($(img).attr('src').indexOf(CCM_STAR_STATES.unstarred) != -1) {
		$(img).attr('src',$(img).attr('src').replace(CCM_STAR_STATES.unstarred,CCM_STAR_STATES.starred));
		action = 'star';
	}
	else {
		$(img).attr('src',$(img).attr('src').replace(CCM_STAR_STATES.starred,CCM_STAR_STATES.unstarred));
		action = 'unstar';
	}
	
	$.post(CCM_TOOLS_PATH + '/' + CCM_STAR_ACTION,{'action':action,'file-id':fID},function(data, textStatus){
		//callback, in case we want to do some post processing
	});
}



// use as an object: 
// var myLayout = new ccmLayout();

function ccmLayout( cvalID, layout_id, area, locked ){
	
	this.layout_id = layout_id;
	this.cvalID = cvalID;
	this.locked = locked;
	this.area = area;
	
	this.init = function(){ 
	
		//ccmAlert.hud( 'test3', 2000, 'add', 'test2');
	
		var layoutObj=this;
		this.layoutWrapper = $('#ccm-layout-wrapper-'+this.cvalID); 
		this.ccmControls = this.layoutWrapper.find("#ccm-layout-controls-"+this.cvalID);
		this.ccmControls.get(0).layoutObj=this;
		/*
		this.layoutWrapper.mouseover(function(){
			layoutObj.ccmControls.show(200);
		})
		
		this.ccmControls.mouseout(function(){
			layoutObj.ccmControls.hide(200).delay(5000);
		});
		*/
		
		this.ccmControls.mouseover(function(){ layoutObj.dontUpdateTwins=0; layoutObj.highlightAreas(1); });
		
		this.ccmControls.mouseout(function(){ if(!layoutObj.moving) layoutObj.highlightAreas(0); });
	 	
		this.ccmControls.find('.ccm-layout-menu-button').click(function(e){ 
			layoutObj.optionsMenu(e);
		})
	
		this.gridSizing();
	}
	
	this.highlightAreas=function(show){
		var els=this.layoutWrapper.find('.ccm-add-block');
		if(show) els.addClass('ccm-layout-area-highlight'); 
		else els.removeClass('ccm-layout-area-highlight'); 
	} 
	
	this.optionsMenu=function(e){ 
		
		ccm_hideMenus();
		e.stopPropagation();
		ccm_menuActivated = true;  
		
		// now, check to see if this menu has been made
		var aobj = document.getElementById("ccm-layout-options-menu-" + this.cvalID);
		
		if (!aobj) {
			// create the 1st instance of the menu
			el = document.createElement("DIV");
			el.id = "ccm-layout-options-menu-" + this.cvalID;
			el.className = "ccm-menu";
			el.style.display = "none";
			document.body.appendChild(el);
			
			aobj = $(el);
			aobj.css("position", "absolute");
			
			//contents  of menu
			var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
			html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
			html += '<ul>';
			
			//the arHandle here should be encoded with encodeURIComponent(), but it leads to a double encoding issue in ccm.dialog.js 
			html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.editAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="280" id="menuEditLayout' + this.cvalID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + this.layout_id + '&cvalID=' + this.cvalID +  '&atask=layout"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/layout_small.png)">' + ccmi18n.editAreaLayout + '</span></a></li>';

			html += '<li><a class="ccm-icon" id="menuAreaLayoutMoveUp' + this.cvalID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/icon_move_up.png)">' + ccmi18n.moveLayoutUp + '</span></a></li>';
						
			html += '<li><a class="ccm-icon" id="menuAreaLayoutMoveDown' + this.cvalID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/icon_move_down.png)">' + ccmi18n.moveLayoutDown + '</span></a></li>';
			
			var lockText = (this.locked) ? ccmi18n.unlockAreaLayout : ccmi18n.lockAreaLayout ; 
			html += '<li><a class="ccm-icon" id="menuAreaLayoutLock' + this.cvalID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + lockText + '</span></a></li>';
			
			html += '<li><a class="ccm-icon" id="menuAreaLayoutDelete' + this.cvalID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n.deleteLayout + '</span></a></li>';
			
			html += '</ul>';
			html += '</div></div>';
			html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
			aobj.append(html);
			
			var aJQobj = $(aobj);
			var layoutObj=this;
			
			aJQobj.find('#menuEditLayout' + this.cvalID).dialog(); 
			
			aJQobj.find('#menuAreaLayoutMoveUp' + this.cvalID).click(function(){ layoutObj.moveLayout('up'); }); 
			
			aJQobj.find('#menuAreaLayoutMoveDown' + this.cvalID).click(function(){ layoutObj.moveLayout('down'); }); 
			
			//lock click 
			aJQobj.find('#menuAreaLayoutLock' + this.cvalID).click( function(){ layoutObj.lock(); } ); 
			
			//delete click
			aJQobj.find('#menuAreaLayoutDelete' + this.cvalID).click(function(){ layoutObj.deleteLayoutOptions(); }); 
			
			
		
		} else {
			aobj = $("#ccm-layout-options-menu-" + this.cvalID);
		}

		ccm_fadeInMenu(aobj, e);		
	}
	
	this.moveLayout=function(direction){ 
	
		this.moving=1;
		ccm_hideHighlighter();
		this.highlightAreas(1);
		this.servicesAjax = $.ajax({  
			url: CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + this.layout_id + '&cvalID=' + this.cvalID +  '&task=move&direction=' + direction,
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
					ccm_mainNavDisableDirectExit();  
				}
			}
		});		
		
		var el = $('#ccm-layout-wrapper-'+this.cvalID);
		var layoutObj = this;
		if(direction=='down'){
			var nextLayout = el.next();
			if( nextLayout.hasClass('ccm-layout-wrapper') ){
				el.slideUp(600,function(){
					el.insertAfter(nextLayout);
					el.slideDown(600,function(){ layoutObj.highlightAreas(0); layoutObj.moving=0; }); 
				})
				return;
			} 
			//at boundry
			ccmAlert.hud( ccmi18n.moveLayoutAtBoundary, 4000, 'icon_move_down', ccmi18n.moveLayoutDown); 
			
		}else if(direction=='up'){
			var previousLayout = el.prev();
			if( previousLayout.hasClass('ccm-layout-wrapper') ){ 
				el.slideUp(600,function(){
					el.insertBefore(previousLayout);
					el.slideDown(600,function(){ layoutObj.highlightAreas(0); layoutObj.moving=0; }); 
				})
				return;
			} 
			//at boundry
			ccmAlert.hud( ccmi18n.moveLayoutAtBoundary, 4000, 'icon_move_up', ccmi18n.moveLayoutUp); 
		}
	}
	
	this.lock=function(lock,twinLock){  
		var a = $('#menuAreaLayoutLock' + this.cvalID); 
		this.locked = !this.locked;
		if( this.locked ){ 
			a.find('span').html(ccmi18n.unlockAreaLayout);
			if(this.s) this.s.slider( 'disable' ); 
		}else{ 
			a.find('span').html(ccmi18n.lockAreaLayout);
			if(this.s) this.s.slider( 'enable');
		}
		
		var lock = (this.locked) ? 1 : 0;
		if(!twinLock){
			
			this.servicesAjax = $.ajax({ 
				url: CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + this.layout_id +  '&task=lock&lock=' + lock,
				success: function(response){  
					eval('var jObj='+response); 
					if(parseInt(jObj.success)!=1){ 
						alert(jObj.msg);
					}else{    
						//success
					}
				}
			});	
			
			this.getTwins();
			for(var i=0;i<this.layoutTwinObjs.length;i++) 
				this.layoutTwinObjs[i].lock(lock,1);
		}
	}
	
	this.hasBeenQuickSaved=0;
	this.quickSaveLayoutId=0;
	this.quickSave=function(){  
		var breakPoints=this.ccmControls.find('#layout_col_break_points_'+this.cvalID).val().replace(/%/g,''); 
		clearTimeout(this.secondSavePauseTmr);
		if(!this.hasBeenQuickSaved && this.quickSaveInProgress){
			quickSaveLayoutObj=this;
			this.secondSavePauseTmr=setTimeout('quickSaveLayoutObj.quickSave()',100);
			return;
		}
		this.quickSaveInProgress=1;
		var layoutObj = this; 
		var modifyLayoutId = (this.quickSaveLayoutId) ? this.quickSaveLayoutId : this.layout_id; 
		this.quickSaveAjax  = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + modifyLayoutId +  '&task=quicksave&breakpoints='+encodeURIComponent(breakPoints), 
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success
					layoutObj.hasBeenQuickSaved=1;
					layoutObj.quickSaveInProgress=0;
					if(jObj.layoutID){
						layoutObj.quickSaveLayoutId = jObj.layoutID;
					}
					ccm_mainNavDisableDirectExit(); 
				}
			}
		}); 
	}
	
	this.deleteLayoutOptions=function(){ 
		var hasBlocks=0;
		deleteLayoutObj=this;
		this.layoutWrapper.find('.ccm-block').each(function(i,el){
			if(el.style.display!='none')  hasBlocks=1;													
		})
		var dialogHeight=(hasBlocks)?'110px':'50px';
		
		$.fn.dialog.open({
			title: ccmi18n.deleteLayoutOptsTitle,
			href:  CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + this.layout_id +  '&task=deleteOpts&hasBlocks='+hasBlocks,
			width: '340px',
			modal: false,
			height: dialogHeight
		});	
			
	}
	
	this.deleteLayout=function(deleteBlocks){   
															
		ccm_hideMenus();   
		
		jQuery.fn.dialog.closeTop();
		
		this.layoutWrapper.slideUp(300); 
		
		jQuery.fn.dialog.showLoader(); 
		 
		var cvalID = this.cvalID;
		this.servicesAjax = $.ajax({ 
			url: CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(this.area) + '&layoutID=' + this.layout_id +  '&task=delete&deleteBlocks='+parseInt(deleteBlocks),
			success: function(response){  
				eval('var jObj='+response); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
					jQuery.fn.dialog.hideLoader();
				}else{    
					//success
					$('#ccm-layout-wrapper-'+cvalID).remove();
					ccm_hideHighlighter();
					ccm_mainNavDisableDirectExit(); 
					
					if(jObj.refreshPage) window.location = window.location;
					else jQuery.fn.dialog.hideLoader(); 
				}
			}
		});	
		
	}	


	this.gridSizing = function(){
		this.ccmGrid=$("#ccm-layout-"+this.layout_id); 
		
		//append layout id to start of all selectors
		var cols=parseInt( this.ccmControls.find('.layout_column_count').val() );  
		
		if(cols>1){ 
			var startPoints=this.ccmControls.find('#layout_col_break_points_'+this.cvalID).val().replace(/%/g,'').split('|');  
			
			this.s = this.ccmControls.find(".ccm-layout-controls-slider");
			
			this.s.get(0).layoutObj=this;
			this.s.get(0).ccmGrid=this.ccmGrid;
			
			this.s.slider( { 
				step: 1, 
				values: startPoints,
				change: function(){  
					if(this.layoutObj.dontUpdateTwins) return;
					this.layoutObj.resizeGrid(this.childNodes); 
					var breakPoints=[];			
					for(var z=0;z<this.childNodes.length;z++)
						breakPoints.push( parseFloat(this.childNodes[z].style.left.replace('%','')) );
						
					breakPoints.sort( function(a, b){ return (a-b); } );
						
					this.layoutObj.ccmControls.find('.layout_col_break_points').val( breakPoints.join('%|')+'%' ); 
					this.layoutObj.quickSave(); 
					ccm_arrangeMode=0;
					this.layoutObj.moving=0;
					this.layoutObj.highlightAreas(0);
				},
				slide:function(){ 	
					ccm_arrangeMode=1;
					this.layoutObj.moving=1;
					if(this.layoutObj.dontUpdateTwins) return; 
					this.layoutObj.resizeGrid(this.childNodes);  
				}
			}); 
			
			if( parseInt(this.ccmControls.find('.layout_locked').val()) ) this.s.slider( 'disable' );
		}	
	}
		
	this.getTwins=function(){
		if(!this.layoutTwins){ 
			this.layoutTwins = $('.ccm-layout-controls-layoutID-'+this.layout_id).not(this.ccmControls);
			this.layoutTwinObjs=[]; 
			for(var q=0;q<this.layoutTwins.length;q++){  
				this.layoutTwinObjs.push( this.layoutTwins[q].layoutObj );  
				this.layoutTwins[q].handles = $(this.layoutTwins[q]).find('.ui-slider-handle');  
			}
		}  
		return this.layoutTwins;
	}
		
	this.resizeGrid=function(childNodes){	 
	
		var positions=[];
	
		this.getTwins();
		 					
		for(var y=0;y<childNodes.length;y++){ 
			var pos=parseFloat(childNodes[y].style.left.replace('%',''));
			positions.push(pos); 
			if(!this.dontUpdateTwins) for(var w=0;w<this.layoutTwinObjs.length;w++){ 
				this.layoutTwinObjs[w].dontUpdateTwins=1;
				this.layoutTwinObjs[w].s.slider('values',y,pos); 
			}
		}
		positions.sort( function(a, b){ return (a-b); } ); 
	
		var prevW=0;
		var i; 					
		for(i=0;i<positions.length;i++){ 
			var pos=positions[i];
			var w=pos-prevW;
			prevW+=w;
			$('.ccm-layout-'+this.layout_id+'-col-'+(i+1)).css('width',w+'%');	
			
			if(!this.dontUpdateTwins) for(j=0;j<this.layoutTwins.length;j++)
				this.layoutTwins[j].handles[i].style.left=pos+'%'; 
		}
		$('.ccm-layout-'+this.layout_id+'-col-'+(i+1)).css('width',(100-prevW)+'%'); 
	}
	
} 

var quickSaveLayoutObj;
var deleteLayoutObj;


var ccmLayoutEdit = {
	
	init:function(){
		
		this.showPresetDeleteIcon();
		
		//change preset selector
		$('#ccmLayoutPresentIdSelector').change(function(){
			//ccmLayoutEdit.showPresetDeleteIcon();
			
			var lpID = parseInt($(this).val());
			var layoutID = $('#ccmAreaLayoutForm_layoutID').val();
			
			jQuery.fn.dialog.showLoader();
			if (lpID > 0) {
				var action = $('#ccm-layout-refresh-action').val() + '&lpID=' + lpID;
			} else {
				var action = $('#ccm-layout-refresh-action').val() + '&layoutID=' + layoutID;
			}
			
			$.get(action, function(r) {
				$("#ccm-layout-edit-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
				ccmLayoutEdit.showPresetDeleteIcon();
			});
		})
		
		$('#layoutPresetActionNew input[name=layoutPresetAction]').click(function() {
			if ($(this).val() == 'create_new_preset' && $(this).prop('checked')) {
				$('input[name=layoutPresetName]').attr('disabled', false).focus();
			} else {
				$('input[name=layoutPresetName]').val('').attr('disabled', true);
			}
		});
		
		$('#layoutPresetActions input[name=layoutPresetAction]').click(function() {
			if ($(this).val() == 'create_new_preset' && $(this).prop('checked')) {
				$('input[name=layoutPresetNameAlt]').attr('disabled', false).focus();
			} else {
				$('input[name=layoutPresetNameAlt]').val('').attr('disabled', true);
			}
		});		
		
		if ($("#layoutPresetActions").length > 0) {
			$("#ccmLayoutConfigOptions input, #ccmLayoutConfigOptions select").bind('change click', function(){
				//if( $('#ccmLayoutPresentIdSelector').val() > 0 ){ 
					$("#layoutPresetActions").show();
					$("#layoutPresetActionNew").hide();
					$("#ccmLayoutConfigOptions input, #ccmLayoutConfigOptions select").unbind('change click'); 
				//}
			});		
		}		
	},
	
	showPresetDeleteIcon: function() {
		if ($('#ccmLayoutPresentIdSelector').val() > 0) {
			$("#ccm-layout-delete-preset").show();		
		} else {
			$("#ccm-layout-delete-preset").hide();
		}	
	},
	
	deletePreset: function() {
		var lpID = parseInt($('#ccmLayoutPresentIdSelector').val());
		if (lpID > 0) { 
			if( !confirm(ccmi18n.confirmLayoutPresetDelete) ) return false;
			
			jQuery.fn.dialog.showLoader();
			var area=$('#ccmAreaLayoutForm_arHandle').val(); 
			var url = CCM_TOOLS_PATH + '/layout_services/?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(area) + '&task=deletePreset&lpID=' + lpID
			$.get(url, function(r) {
				eval('var jObj='+r); 
				if(parseInt(jObj.success)!=1){ 
					alert(jObj.msg);
				}else{    
					//success 
					$("#ccmLayoutPresentIdSelector option[value='"+lpID+"']").remove();
				}
				jQuery.fn.dialog.hideLoader();
			});  
			
		}
	}
}

$.widget.bridge( "jqdialog", $.ui.dialog );

// wrap our old dialog function in the new dialog() function.
jQuery.fn.dialog = function() {
	// Pass this over to jQuery UI Dialog in a few circumstances
	if (arguments.length > 0) {
		$(this).jqdialog(arguments[0], arguments[1], arguments[2]);
		return;
	} else if ($(this).is('div')) {
		$(this).jqdialog();
		return;
	}
	// LEGACY SUPPORT
	return $(this).each(function() {
		$(this).click(function(e) {
			var href = $(this).attr('href');
			var width = $(this).attr('dialog-width');
			var height =$(this).attr('dialog-height');
			var title = $(this).attr('dialog-title');
			var onOpen = $(this).attr('dialog-on-open');
			var onDestroy = $(this).attr('dialog-on-destroy');
			var appendButtons = $(this).attr('dialog-append-buttons');
			var onClose = $(this).attr('dialog-on-close');
			obj = {
				modal: true,
				href: href,
				width: width,
				height: height,
				title: title,
				appendButtons: appendButtons,
				onOpen: onOpen,
				onDestroy: onDestroy,
				onClose: onClose
			}
			jQuery.fn.dialog.open(obj);
			return false;
		});	
	});
}

jQuery.fn.dialog.close = function(num) {
	num++;
	$("#ccm-dialog-content" + num).jqdialog('close');
}

jQuery.fn.dialog.open = function(obj) {
	jQuery.fn.dialog.showLoader();
	if (ccm_uiLoaded) {
		ccm_hideMenus();
	}
	var nd = $(".ui-dialog").length;
	nd++;
	$('body').append('<div id="ccm-dialog-content' + nd + '" style="display: none"></div>');
	
	if (typeof(obj.width) == 'string') {
		if (obj.width.indexOf('%', 0) > 0) {
			w = obj.width.replace('%', '');
			h = obj.height.replace('%', '');
			h = $(window).height() * (h / 100);
			w = $(window).width() * (w / 100);
			h = h + 100;
			w = w + 50;
		} else {
			w = parseInt(obj.width) + 50;
			h = parseInt(obj.height) + 100;
		}
	} else {
		w = parseInt(obj.width) + 50;
		h = parseInt(obj.height) + 100;
	}
	if (obj.appendButtons) {
		var buttons = [{}];
	} else {
		var buttons = false;
	}
	$("#ccm-dialog-content" + nd).jqdialog({
		'modal': true,
		'height': h,
		'width': w,
		show:{
		effect:"fade", 
		duration:150, 
		easing:"easeInExpo"
		},
		hide:{
			effect:"drop", 
			direction:"down", 
			distance:60, 
			duration:500, 
			easing:"easeOutExpo"
		},
		'escapeClose': true,
		'buttons': buttons,
		'title': obj.title,
		'open': function() {
			$("body").css("overflow", "hidden");
		},
		'beforeClose': function() {
			$("body").css("overflow", "auto");		
		},
		'close': function(ev, u) {
			$(this).jqdialog('destroy').remove();
			$("#ccm-dialog-content" + nd).remove();
			if (typeof obj.onClose != "undefined") {
				if ((typeof obj.onClose) == 'function') {
					obj.onClose();
				} else {
					eval(obj.onClose);
				}
			}
			if (typeof obj.onDestroy != "undefined") {
				if ((typeof obj.onDestroy) == 'function') {
					obj.onDestroy();
				} else {
					eval(obj.onDestroy);
				}
			}
			nd--;
		}
	});		
	
	if (!obj.element) {
		$.ajax({
			type: 'GET',
			url: obj.href,
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				$("#ccm-dialog-content" + nd).html(r);
				$("#ccm-dialog-content" + nd + " .dialog-launch").dialog();
				$("#ccm-dialog-content" + nd + " .ccm-dialog-close").click(function() {
					jQuery.fn.dialog.closeTop();
				});
				if ($("#ccm-dialog-content" + nd + " .dialog-buttons").length > 0) {
					$("#ccm-dialog-content" + nd).parent().addClass('ccm-ui');
					$("#ccm-dialog-content" + nd + " .dialog-buttons").appendTo($("#ccm-dialog-content" + nd).parent().find('.ui-dialog-buttonpane').html(''));
					$("#ccm-dialog-content" + nd + " .dialog-buttons").remove();
				}
				if (typeof obj.onOpen != "undefined") {
					if ((typeof obj.onOpen) == 'function') {
						obj.onOpen();
					} else {
						eval(obj.onOpen);
					}
				}
				
			}
		});			
	} else {
		jQuery.fn.dialog.hideLoader();
		$("#ccm-dialog-content" + nd).append($(obj.element));
		if ($(obj.element).css('display') == 'none') {
			$(obj.element).show();
		}
		$("#ccm-dialog-content" + nd + " .dialog-launch").dialog();
		$("#ccm-dialog-content" + nd + " .ccm-dialog-close").click(function() {
			jQuery.fn.dialog.closeTop();
		});
		if (typeof obj.onOpen != "undefined") {
			if ((typeof obj.onOpen) == 'function') {
				obj.onOpen();
			} else {
				eval(obj.onOpen);
			}
		}
	}
		
}

jQuery.fn.dialog.replaceTop = function(h) {
	var nd = $(".ui-dialog").length;
	$("#ccm-dialog-content" + nd).html(h);
}

jQuery.fn.dialog.showLoader = function(text) {
	if (typeof(imgLoader)=='undefined' || !imgLoader || !imgLoader.src) return false; 
	if ($('#ccm-dialog-loader').length < 1) {
		$("body").append("<div id='ccm-dialog-loader-wrapper' class='ccm-ui'><img id='ccm-dialog-loader' src='"+imgLoader.src+"' /></div>");//add loader to the page
	}
	if (text != null) {
		$("<div />").attr('id', 'ccm-dialog-loader-text').html(text).prependTo($("#ccm-dialog-loader-wrapper"));
	}

	var w = $("#ccm-dialog-loader-wrapper").width();
	var h = $("#ccm-dialog-loader-wrapper").height();
	var tw = $(window).width();
	var th = $(window).height();
	var _left = (tw - w) / 2;
	var _top = (th - h) / 2;
	$("#ccm-dialog-loader-wrapper").css('left', _left + 'px').css('top', _top + 'px');
	$('#ccm-dialog-loader-wrapper').show();//show loader
	//$('#ccm-dialog-loader-wrapper').fadeTo('slow', 0.2);
}

jQuery.fn.dialog.hideLoader = function() {
	$("#ccm-dialog-loader-wrapper").hide();
	$("#ccm-dialog-loader-text").remove();
}

jQuery.fn.dialog.closeTop = function() {
	var nd = $(".ui-dialog").length;
	$("#ccm-dialog-content" + nd).jqdialog('close');
}

jQuery.fn.dialog.closeAll = function() {
	$(".ui-dialog-content").jqdialog('close');
}


var imgLoader;
var ccm_dialogOpen = 0;
jQuery.fn.dialog.loaderImage = CCM_IMAGE_PATH + "/throbber_white_32.gif";

var ccmAlert = {  
    notice : function(title, message, onCloseFn) {
        $.fn.dialog.open({
            href: CCM_TOOLS_PATH + '/alert',
            title: title,
            width: 320,
            height: 160,
            modal: false, 
			onOpen: function () {
        		$("#ccm-popup-alert-message").html(message);
			},
			onDestroy: onCloseFn
        }); 
    },
    
    hud: function(message, time, icon, title) {
    	if ($('#ccm-notification-inner').length == 0) { 
    		$(document.body).append('<div id="ccm-notification"><div id="ccm-notification-inner"></div></div>');
    	}
    	
    	if (icon == null) {
    		icon = 'edit_small';
    	}
    	
    	if (title == null) {	
	    	var messageText = message;
	    } else {
	    	var messageText = '<h3>' + title + '</h3>' + message;
	    }
    	$('#ccm-notification-inner').html('<table border="0" cellspacing="0" cellpadding="0"><tr><td valign="top"><img id="ccm-notification-icon" src="' + CCM_IMAGE_PATH + '/icons/' + icon + '.png" width="16" height="16" /></td><td valign="top">' + messageText + '</td></tr></table>');
		
		$('#ccm-notification').show();
		
    	if (time > 0) {
    		setTimeout(function() {
    			$('#ccm-notification').fadeOut({easing: 'easeOutExpo', duration: 300});
    		}, time);
    	}
    	
    }
}      

$(document).ready(function(){   
	imgLoader = new Image();// preload image
	imgLoader.src = jQuery.fn.dialog.loaderImage;

});

ccm_setNewsflowOverlayDimensions = function() {
	var w = $("#newsflow-overlay").width();
	var tw = $(window).width();
	var _left = (tw - w) / 2;
	_left = _left + "px";
	$("#newsflow-overlay").css('left', _left);
}

ccm_closeNewsflow = function() {
	$("#newsflow-overlay").fadeOut(300, 'easeOutExpo', function() {
		$("#newsflow-overlay").remove();
	});
	$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
		$(this).remove();
	});
}

ccm_showNewsflow = function() {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	jQuery.fn.dialog.showLoader(ccmi18n.newsflowLoading);	
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/home?_ccm_dashboard_external=1', function() {
		jQuery.fn.dialog.hideLoader();
		ccm_setNewsflowOverlayDimensions();
		$("#newsflow-overlay").css('top', '90px').fadeIn('300', 'easeOutExpo');
	});
}

ccm_showAppIntroduction = function() {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/welcome?_ccm_dashboard_external=1', function() {
		ccm_setNewsflowOverlayDimensions();
		$("#newsflow-overlay").css('top', '90px').fadeIn('300', 'easeOutExpo');
	});
}
// qs_score - Quicksilver Score
// 
// A port of the Quicksilver string ranking algorithm
// 
// "hello world".score("axl") //=> 0.0
// "hello world".score("ow") //=> 0.6
// "hello world".score("hello world") //=> 1.0
//
// Tested in Firefox 2 and Safari 3
//
// The Quicksilver code is available here
// http://code.google.com/p/blacktree-alchemy/
// http://blacktree-alchemy.googlecode.com/svn/trunk/Crucible/Code/NSString+BLTRRanking.m
//
// The MIT License
// 
// Copyright (c) 2008 Lachie Cox
// 
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.


String.prototype.score = function(abbreviation,offset) {
  offset = offset || 0 // TODO: I think this is unused... remove
 
  if(abbreviation.length == 0) return 0.9
  if(abbreviation.length > this.length) return 0.0

  for (var i = abbreviation.length; i > 0; i--) {
    var sub_abbreviation = abbreviation.substring(0,i)
    var index = this.indexOf(sub_abbreviation)


    if(index < 0) continue;
    if(index + abbreviation.length > this.length + offset) continue;

    var next_string       = this.substring(index+sub_abbreviation.length)
    var next_abbreviation = null

    if(i >= abbreviation.length)
      next_abbreviation = ''
    else
      next_abbreviation = abbreviation.substring(i)
 
    var remaining_score   = next_string.score(next_abbreviation,offset+index)
 
    if (remaining_score > 0) {
      var score = this.length-next_string.length;

      if(index != 0) {
        var j = 0;

        var c = this.charCodeAt(index-1)
        if(c==32 || c == 9) {
          for(var j=(index-2); j >= 0; j--) {
            c = this.charCodeAt(j)
            score -= ((c == 32 || c == 9) ? 1 : 0.15)
          }

          // XXX maybe not port this heuristic
          // 
          //          } else if ([[NSCharacterSet uppercaseLetterCharacterSet] characterIsMember:[self characterAtIndex:matchedRange.location]]) {
          //            for (j = matchedRange.location-1; j >= (int) searchRange.location; j--) {
          //              if ([[NSCharacterSet uppercaseLetterCharacterSet] characterIsMember:[self characterAtIndex:j]])
          //                score--;
          //              else
          //                score -= 0.15;
          //            }
        } else {
          score -= index
        }
      }
   
      score += remaining_score * next_string.length
      score /= this.length;
      return score
    }
  }
  return 0.0
}
ccm_marketplaceLauncherOpenPost = function() {

	jQuery.fn.dialog.hideLoader();
	ccm_setNewsflowOverlayDimensions();
	$("#newsflow-overlay").css('top', '90px').fadeIn('300', 'easeOutExpo');
	$(".ccm-pagination a").click(function() {
		jQuery.fn.dialog.showLoader(false);
		$('#newsflow-overlay').load($(this).attr('href'), function() {
			ccm_marketplaceLauncherOpenPost();			
		});
		return false;
	});
	$("#ccm-marketplace-browser-form").ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader(false);
		},
		success: function(r) {
			$('#newsflow-overlay').html(r);
			ccm_marketplaceLauncherOpenPost();
		}
	});
}

ccm_openThemeLauncher = function(mpID) {
	jQuery.fn.dialog.closeTop();
	
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	jQuery.fn.dialog.showLoader(ccmi18n.themeBrowserLoading);
	var mpIDstr = '';
	if (mpID) {
		mpIDstr = '&mpID=' + mpID;
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/extend/themes?_ccm_dashboard_external=1' + mpIDstr, function() {
		ccm_marketplaceLauncherOpenPost();
	});
}

ccm_openAddonLauncher = function(mpID) {
	$("#ccm-nav-intelligent-search").val('');
	$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');

	jQuery.fn.dialog.closeTop();
	
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	jQuery.fn.dialog.showLoader(ccmi18n.addonBrowserLoading);	
	var mpIDstr = '';
	if (mpID) {
		mpIDstr = '&mpID=' + mpID;
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/extend/add-ons?_ccm_dashboard_external=1' + mpIDstr, function() {
		ccm_marketplaceLauncherOpenPost();
	});
}
ccm_marketplaceBrowserInit = function(mpID, autoSelect) {
	
	$(".ccm-marketplace-item").click(function() {
		window.scrollTo(0,0);
		$("#newsflow-paging-previous").hide();
		$("#newsflow-paging-next").hide();
		$("#ccm-marketplace-detail-inner").hide();
		$('.ccm-marketplace-detail-loading').show();	

		var mpID = $(this).attr('mpID');
		$('.ccm-marketplace-item-selected').removeClass('ccm-marketplace-item-selected').addClass('ccm-marketplace-item-unselected');
		$(this).removeClass('ccm-marketplace-item-unselected').addClass('ccm-marketplace-item-selected');
		$('#ccm-marketplace-detail').show();
		$('#ccm-marketplace-detail-inner').load(CCM_TOOLS_PATH + '/marketplace/details', {
			'mpID': mpID
		}, function() {
			ccm_marketplaceGetDetailPost();
		});
	});

	if (mpID) {
		$("#ccm-marketplace-detail-inner").hide();
		$('.ccm-marketplace-detail-loading').show();	
		$('#ccm-marketplace-detail').show();
		$('#ccm-marketplace-detail-inner').load(CCM_TOOLS_PATH + '/marketplace/details', {
			'mpID': mpID
		}, function() {
			ccm_marketplaceGetDetailPost();
		});
	} else {
		if (autoSelect == 'last') { 
			$("div.ccm-marketplace-results-info").last().parent().click();
		} else {
			$("div.ccm-marketplace-results-info").first().parent().click();
		}
	}
}

ccm_marketplaceBrowserSelectPrevious = function() {
	var items = $('.ccm-marketplace-item');
	var doSelect = false;
	var foundSomething = false;
	$(items.get().reverse()).each(function() {
		if (doSelect) {
			$(this).click();
			doSelect = false;
			foundSomething = true;
		} else { 
			if ($(this).hasClass('ccm-marketplace-item-selected')) {
				doSelect = true;
			}
		}
	});
	if (!foundSomething) {
		var href = $("#ccm-marketplace-browse-footer .ccm-page-left a").first().attr('href');
		href = href + '&prev=1';
		if ($('#newsflow').length > 0) { 
			jQuery.fn.dialog.showLoader(false);
			$('#newsflow-overlay').load(href, function() {
				ccm_marketplaceLauncherOpenPost();			
			});
		} else { 
			window.location.href = href;
		}
	}
}

ccm_marketplaceBrowserSelectNext = function() {
	var items = $('.ccm-marketplace-item');
	var doSelect = false;
	var foundSomething = false;
	items.each(function() {
		if (doSelect) {
			$(this).click();
			doSelect = false;
			foundSomething = true;
		} else { 
			if ($(this).hasClass('ccm-marketplace-item-selected')) {
				doSelect = true;
			}
		}
	});
	
	// if we make it down here...
	if (!foundSomething) {
		var href = $("#ccm-marketplace-browse-footer .ccm-page-right a").first().attr('href');
		if ($('#newsflow').length > 0) { 
			jQuery.fn.dialog.showLoader(false);
			$('#newsflow-overlay').load(href, function() {
				ccm_marketplaceLauncherOpenPost();			
			});
		} else { 
			window.location.href = href;
		}
	}
}

ccm_marketplaceBrowserSetupNextAndPrevious = function() {

	if ($('.ccm-marketplace-item-selected').attr('mpID') == $('.ccm-marketplace-item').first().attr('mpID') 
	&& $('#ccm-marketplace-browse-footer span.ccm-page-left a').length == 0) { 
		$("#newsflow-paging-previous").hide();
	} else {
		$("#newsflow-paging-previous").show();
	}

	if ($('.ccm-marketplace-item-selected').attr('mpID') == $('.ccm-marketplace-item').last().attr('mpID')
	&& $('#ccm-marketplace-browse-footer span.ccm-page-right a').length == 0) { 
		$("#newsflow-paging-next").hide();
	} else {
		$("#newsflow-paging-next").show();
	}
	
}


ccm_marketplaceGetDetailPost = function() {
	var h = $('#ccm-marketplace-detail').height();
	h = h + 40;
	$("#newsflow-paging-previous span, #newsflow-paging-next span").css('height', h + 'px');
	$("#newsflow-paging-previous, #newsflow-paging-next").css('height', h + 'px');
	$('.ccm-marketplace-detail-loading').hide();
	$("#ccm-marketplace-detail-inner").show();
	if ($(".ccm-marketplace-item-information-inner").height() < 325) {
		$(".ccm-marketplace-item-information-more").hide();
	}
	$("#ccm-marketplace-item-screenshots").nivoSlider({
		'controlNav': false,
		'effect': 'fade',
		'pauseOnHover': false,
		'directionNav': false
	});
	ccm_marketplaceBrowserSetupNextAndPrevious();
}

ccm_getMarketplaceItem = function(args) {
	var mpID = args.mpID;
	var closeTop = args.closeTop;
	
	this.onComplete = function() { }

	if (args.onComplete) {
		ccm_getMarketplaceItem.onComplete = args.onComplete;
	}
	
	if (closeTop) {
		jQuery.fn.dialog.closeTop(); // this is here due to a weird safari behavior
	}
	jQuery.fn.dialog.showLoader();
	// first, we check our local install to ensure that we're connected to the
	// marketplace, etc..
	params = {'mpID': mpID};
	$.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
		jQuery.fn.dialog.hideLoader();
		if (resp.isConnected) {
			if (!resp.purchaseRequired) {
				$.fn.dialog.open({
					title: ccmi18n.community,
					href:  CCM_TOOLS_PATH + '/marketplace/download?install=1&mpID=' + mpID,
					width: 350,
					modal: false,
					height: 240
				});
			} else {
				$.fn.dialog.open({
					title: ccmi18n.community,
					iframe: true,
					href:  CCM_TOOLS_PATH + '/marketplace/checkout?mpID=' + mpID,
					width: '90%',
					modal: false,
					height: '70%'
				});
			}

		} else {
			$.fn.dialog.open({
				title: ccmi18n.community,
				href:  CCM_TOOLS_PATH + '/marketplace/frame?mpID=' + mpID,
				width: '90%',
				modal: false,
				height: '70%'
			});
		}
	});
}
var ccm_searchActivatePostFunction = new Array();

ccm_setupAdvancedSearchFields = function(searchType) {
	ccm_totalAdvancedSearchFields = $('.ccm-search-request-field-set').length;
	$("#ccm-" + searchType + "-search-add-option").unbind();
	$("#ccm-" + searchType + "-search-add-option").click(function() {
		ccm_totalAdvancedSearchFields++;
		if ($("#ccm-search-fields-wrapper").length > 0) { 
			$("#ccm-search-fields-wrapper").append('<div class="ccm-search-field" id="ccm-' + searchType + '-search-field-set' + ccm_totalAdvancedSearchFields + '">' + $("#ccm-search-field-base").html() + '<\/div>');
		} else {
			$("#ccm-" + searchType + "-search-advanced-fields").append('<tr class="ccm-search-field" id="ccm-' + searchType + '-search-field-set' + ccm_totalAdvancedSearchFields + '">' + $("#ccm-search-field-base").html() + '<\/tr>');
		}
		ccm_activateAdvancedSearchFields(searchType, ccm_totalAdvancedSearchFields);
	});
	
	// we have to activate any of the fields that were here based on the request
	// these fields show up after a page is reloaded but we want to keep showing the request fields
	var i = 1;
	$('.ccm-search-request-field-set').each(function() {
		ccm_activateAdvancedSearchFields(searchType, i);
		i++;
	});
}

ccm_setupAdvancedSearch = function(searchType) {
	ccm_setupAdvancedSearchFields(searchType);
	$("#ccm-" + searchType + "-advanced-search").ajaxForm({
		beforeSubmit: function() {
			ccm_deactivateSearchResults(searchType);
		},
		
		success: function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchType);
		}
	});
	ccm_setupInPagePaginationAndSorting(searchType);
	ccm_setupSortableColumnSelection(searchType);
	
}

ccm_parseAdvancedSearchResponse = function(resp, searchType) {
	var obj = $("#ccm-" + searchType + "-search-results");
	if (obj.length == 0 || searchType == null) {
		obj = $("#ccm-search-results");
	}
	obj.html(resp);
	ccm_activateSearchResults(searchType);
}

ccm_deactivateSearchResults = function(searchType) {
	var obj = $("#ccm-" + searchType + "-search-fields-submit");
	if (obj.length == 0 || searchType == null) {
		obj = $("#ccm-search-fields-submit");
	}
	obj.attr('disabled', true);
	var obj = $("#ccm-" + searchType + "-search-loading");
	if (obj.length == 0 || searchType == null) {
		obj = $("#ccm-search-loading");
	}
	obj.show();
}

ccm_activateSearchResults = function(searchType) {
	if ($('a[name=ccm-' + searchType + '-list-wrapper-anchor]').length > 0) {
		window.location.hash = 'ccm-' + searchType + '-list-wrapper-anchor';
	}
	var obj = $("#ccm-" + searchType + "-search-loading");
	if (obj.length == 0 || searchType == null) {
		obj = $("#ccm-search-loading");
	}
	obj.hide();
	var obj = $("#ccm-" + searchType + "-search-fields-submit");
	if (obj.length == 0 || searchType == null) {
		obj = $("#ccm-search-fields-submit");
	}
	obj.attr('disabled', false);
	ccm_setupInPagePaginationAndSorting(searchType);
	ccm_setupSortableColumnSelection(searchType);
	if(typeof(ccm_searchActivatePostFunction[searchType]) == 'function') {
		ccm_searchActivatePostFunction[searchType]();
	}
}

ccm_setupInPagePaginationAndSorting = function(searchType) {
	$(".ccm-results-list th a").click(function() {
		ccm_deactivateSearchResults(searchType);
		var obj = $("#ccm-" + searchType + "-search-results");
		if (obj.length == 0) {
			obj = $("#ccm-search-results");
		}
		obj.load($(this).attr('href'), false, function() {
			ccm_activateSearchResults(searchType);
		});
		return false;
	});
	$("div.ccm-pagination a").click(function() {
		ccm_deactivateSearchResults(searchType);
		var obj = $("#ccm-" + searchType + "-search-results");
		if (obj.length == 0) {
			obj = $("#ccm-search-results");
		}
		obj.load($(this).attr('href'), false, function() {
			ccm_activateSearchResults(searchType);
			$("div.ccm-dialog-content").attr('scrollTop', 0);
		});
		return false;
	});
	$(".ccm-pane-dialog-pagination").each(function() {
		$(this).closest('.ui-dialog').find('.ui-dialog-buttonpane').html('');
		$(this).appendTo($(this).closest('.ui-dialog').find('.ui-dialog-buttonpane'));
	});
	
}

ccm_setupSortableColumnSelection = function(searchType) {
	$("#ccm-search-add-column").unbind();
	$("#ccm-search-add-column").click(function() {
		jQuery.fn.dialog.open({
			width: 550,
			height: 350,
			modal: false,
			href: $(this).attr('href'),
			title: ccmi18n.customizeSearch				
		});
		return false;
	});
}

ccm_checkSelectedAdvancedSearchField = function(searchType, fieldset) {
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").each(function() {
		if ($(this).attr('id') == 'date_from') {
			$(this).attr('id', 'date_from' + fieldset);
		} else if ($(this).attr('id') == 'date_to') {
			$(this).attr('id', 'date_to' + fieldset);
		}
	});

	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").each(function() {
		$(this).attr('id', $(this).attr('id') + fieldset);
	});
	
	
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").datepicker({
		showAnim: 'fadeIn'
	});
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-rating input").rating();		
}

ccm_activateAdvancedSearchFields = function(searchType, fieldset) {
	var selTag = $("#ccm-" + searchType + "-search-field-set" + fieldset + " select:first");
	selTag.unbind();
	selTag.change(function() {
		var selected = $(this).find(':selected').val(); 
		$(this).parent().parent().find('input.ccm-' + searchType + '-selected-field').val(selected);
		
		var itemToCopy = $('#ccm-' + searchType + '-search-field-base-elements span[search-field=' + selected + ']');
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content").html('');
		itemToCopy.clone().appendTo("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content");
		
		$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-selected-field-content .ccm-search-option").show();
		ccm_checkSelectedAdvancedSearchField(searchType, fieldset);
	});

	
	// add the initial state of the latest select menu
	/*
	var lastSelect = $("#ccm-" + searchType + "-search-field-set" + fieldset + " select[ccm-advanced-search-selector=1]").eq($(".ccm-" + searchType + "-search-field select[ccm-advanced-search-selector=1]").length-1);
	var selected = lastSelect.find(':selected').val();
	lastSelect.next('input.ccm-" + searchType + "-selected-field').val(selected);
	*/
	
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-remove-option").unbind();
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-remove-option").click(function() {
		$(this).parents('div.ccm-search-field').remove();
		$(this).parents('tr.ccm-search-field').remove();
		
		//ccm_totalAdvancedSearchFields--;
	});
	
	ccm_checkSelectedAdvancedSearchField(searchType, fieldset);
	
}


ccm_activateEditablePropertiesGrid = function() {
	$("tr.ccm-attribute-editable-field").each(function() {
		var trow = $(this);
		$(this).find('a').click(function() {
			trow.find('.ccm-attribute-editable-field-text').hide();
			trow.find('.ccm-attribute-editable-field-clear-button').hide();
			trow.find('.ccm-attribute-editable-field-form').show();
			trow.find('.ccm-attribute-editable-field-save-button').show();
		});
		
		trow.find('form').submit(function() {
			ccm_submitEditablePropertiesGrid(trow);
			return false;
		});
		
		trow.find('.ccm-attribute-editable-field-save-button').parent().click(function() {
			ccm_submitEditablePropertiesGrid(trow);
		});

		trow.find('.ccm-attribute-editable-field-clear-button').parent().unbind();
		trow.find('.ccm-attribute-editable-field-clear-button').parent().click(function() {
			trow.find('form input[name=task]').val('clear_extended_attribute');
			ccm_submitEditablePropertiesGrid(trow);
			return false;
		});

	});
}

ccm_submitEditablePropertiesGrid = function(trow) {

	trow.find('.ccm-attribute-editable-field-save-button').hide();
	trow.find('.ccm-attribute-editable-field-clear-button').hide();
	trow.find('.ccm-attribute-editable-field-loading').show();
	try {
		tinyMCE.triggerSave(true, true);
	} catch(e) { }

	trow.find('form').ajaxSubmit(function(resp) {
		// resp is new HTML to display in the div
		trow.find('.ccm-attribute-editable-field-loading').hide();
		trow.find('.ccm-attribute-editable-field-save-button').show();
		trow.find('.ccm-attribute-editable-field-text').html(resp);
		trow.find('.ccm-attribute-editable-field-form').hide();
		trow.find('.ccm-attribute-editable-field-save-button').hide();
		trow.find('.ccm-attribute-editable-field-text').show();
		trow.find('.ccm-attribute-editable-field-clear-button').show();
		trow.find('td').show('highlight', {
			color: '#FFF9BB'
		});

	});
}



var tr_activeNode = false;
//var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
if (typeof(tr_doAnim) == 'undefined') {
	var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
}
var tr_parseSubnodes = true;
var tr_reorderMode = false;
var	tr_moveCopyMode = false;

showPageMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	/* now, check to see if this menu has been made */
	var bobj = $("#ccm-page-menu" + obj.cID);
	
	if (!bobj.get(0)) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-page-menu" + obj.cID;
		el.className = "ccm-menu ccm-ui";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = $("#ccm-page-menu" + obj.cID);
		bobj.css("position", "absolute");
		
		/* contents  of menu */
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
		html += "<ul>";
		
		if (obj.cAlias == 'LINK' || obj.cAlias == 'POINTER') {
		
			html += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + obj.cID + '" href="javascript:void(0)" onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '\'">' + ccmi18n_sitemap.visitExternalLink + '<\/a><\/li>';
			if (obj.cAlias == 'LINK') {
				html += '<li><a class="ccm-menu-icon ccm-icon-edit-external-link" dialog-width="350" dialog-height="300" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_external">' + ccmi18n_sitemap.editExternalLink + '<\/a><\/li>';
			}

			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-append-buttons="true" id="menuDelete' + obj.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&display_mode=' + obj.display_mode + '&instance_id=' + obj.instance_id + '&select_mode=' + obj.select_mode + '&ctask=delete">' + ccmi18n_sitemap.deleteExternalLink + '<\/a><\/li>';

		
		} else if (obj.canWrite == 'false') {
		
			html += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + obj.cID + '" href="javascript:void(0)" onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '\'">' + ccmi18n_sitemap.visitPage + '<\/a><\/li>';

		
		} else {
		
			html += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + obj.cID + '" href="javascript:void(0)" onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '\'">' + ccmi18n_sitemap.visitPage + '<\/a><\/li>';
			if (obj.canCompose) {
				html += '<li><a class="ccm-menu-icon ccm-icon-edit-in-composer-menu" id="menuComposer' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/composer/write/-/edit/' + obj.cID + '">' + ccmi18n_sitemap.editInComposer + '<\/a><\/li>';
			}
			html += '<li class=\"ccm-menu-separator\"><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-properties-menu" dialog-width="640" dialog-height="360" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pagePropertiesTitle + '" id="menuProperties' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_metadata">' + ccmi18n_sitemap.pageProperties + '<\/a><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-speed-settings-menu" dialog-width="550" dialog-height="280" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.speedSettingsTitle + '" id="menuSpeedSettings' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_speed_settings">' + ccmi18n_sitemap.speedSettings + '<\/a><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-permissions-menu" dialog-width="640" dialog-height="310" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" id="menuPermissions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_permissions">' + ccmi18n_sitemap.setPagePermissions + '<\/a><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-design-menu" dialog-width="610" dialog-append-buttons="true" dialog-height="405" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" id="menuDesign' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=set_theme">' + ccmi18n_sitemap.pageDesign + '<\/a><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-versions-menu" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" id="menuVersions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/versions.php?rel=SITEMAP&cID=' + obj.cID + '">' + ccmi18n_sitemap.pageVersions + '<\/a><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-append-buttons="true" id="menuDelete' + obj.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deletePage + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&display_mode=' + obj.display_mode + '&instance_id=' + obj.instance_id + '&select_mode=' + obj.select_mode + '&ctask=delete">' + ccmi18n_sitemap.deletePage + '<\/a><\/li>';
			html += '<li class=\"ccm-menu-separator\"><\/li>';
			if (obj.display_mode == 'explore' || obj.display_mode == 'search') {
				html += '<li><a class="ccm-menu-icon ccm-icon-move-copy-menu" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.moveCopyPage + '" id="menuMoveCopy' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/sitemap_overlay?instance_id=' + obj.instance_id + '&display_mode=' + obj.display_mode + '&select_mode=move_copy_delete&cID=' + obj.cID + '" id="menuMoveCopy' + obj.cID + '">' + ccmi18n_sitemap.moveCopyPage + '<\/a><\/li>';
				if (obj.display_mode == 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-move-up" id="menuSendToStop' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=' + obj.cID + '&task=send_to_top">' + ccmi18n_sitemap.sendToTop + '<\/a><\/li>';
					html += '<li><a class="ccm-menu-icon ccm-icon-move-down" id="menuSendToBottom' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=' + obj.cID + '&task=send_to_bottom">' + ccmi18n_sitemap.sendToBottom + '<\/a><\/li>';
			}
				if (obj.cNumChildren == 0) {
					html += '<li class=\"ccm-menu-separator\"><\/li>';
				}
			}
			if (obj.cNumChildren > 0) {
				//var searchURL = (obj.display_mode == 'explore') ? CCM_REL + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=' + obj.cID : 'javascript:searchSubPages(' + obj.cID + ')';
				var searchURL = CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=' + obj.cID;
				
				if (obj.display_mode == 'full' || obj.display_mode == '' || obj.display_mode == 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-search-pages" id="menuSearch' + obj.cID + '" href="' + searchURL + '">' + ccmi18n_sitemap.searchPages + '<\/a><\/li>';
				}
				if (obj.display_mode != 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-flat-view" id="menuExplore' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore/-/' + obj.cID + '">' + ccmi18n_sitemap.explorePages + '<\/a><\/li>';
				}
				html += '<li class=\"ccm-menu-separator\"><\/li>';
				
			}
			html += '<li><a class="ccm-menu-icon ccm-icon-add-page-menu" dialog-width="680" dialog-modal="false" dialog-height="440" dialog-title="' + ccmi18n_sitemap.addPage + '" id="menuSubPage' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&mode=' + obj.display_mode + '&cID=' + obj.cID + '&ctask=add">' + ccmi18n_sitemap.addPage + '<\/a><\/li>';
			if (obj.display_mode != 'search') {
				html += '<li><a class="ccm-menu-icon ccm-icon-add-external-link-menu" dialog-width="350" dialog-modal="false" dialog-height="160" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add_external">' + ccmi18n_sitemap.addExternalLink + '<\/a><\/li>';
			}

		} 
		
		html += '<\/ul>';

		html += '</div></div></div>';

		bobj.append(html);

		$(bobj).find('a').bind('click.hide-menu', function(e) {
			ccm_hideMenus();
		});

		$("#menuProperties" + obj.cID).dialog();
		$("#menuSpeedSettings" + obj.cID).dialog();
		$("#menuSubPage" + obj.cID).dialog();
		$("#menuDesign" + obj.cID).dialog();
		$("#menuLink" + obj.cID).dialog();
		$("#menuVersions" + obj.cID).dialog();
		$("#menuPermissions" + obj.cID).dialog();
		$("#menuMoveCopy" + obj.cID).dialog();
		$("#menuDelete" + obj.cID).dialog();

	} else {
		bobj = $("#ccm-page-menu" + obj.cID);
	}
	
	ccm_fadeInMenu(bobj, e);
	
}

hideBranch = function(nodeID) {
	// hides branch and its drop zone
	$("#tree-node" + nodeID).hide();
	$("#tree-dz" + nodeID).hide();
}

cancelReorder = function() {
	if (tr_reorderMode) {
		//$('img.handle').removeClass('moveable');
		tr_reorderMode = false;
		$('li.tree-node').draggable('destroy');
		if (!tr_moveCopyMode) {
			hideSitemapMessage();
		}
	}
}

searchSubPages = function(cID) {
	$("#ccm-tree-search-trigger" + cID).hide();
	if (ccm_animEffects) {
		$("#ccm-tree-search" + cID).fadeIn(200, function() {
			$("#ccm-tree-search" + cID + " input").get(0).focus();
		});
	} else {
		$("#ccm-tree-search" + cID).show();
		$("#ccm-tree-search" + cID + " input").get(0).focus();
	}
}

activateReorder = function() {
	tr_reorderMode = true;
	
	/*
	
	$('div.tree-label').droppable({
		accept: '.tree-node',
		hoverClass: 'on-drop',
		drop: function(e, ui) {
			var orig = ui.draggable;
			var destCID = $(this).attr('id').substring(10);
			var origCID = $(orig).attr('id').substring(9);
			if(destCID==origCID) return false;
			var dialog_url=CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?origCID=' + origCID + '&destCID=' + destCID;
			//prevent window from opening twice
			if(SITEMAP_LAST_DIALOGUE_URL==dialog_url) return false;
			else SITEMAP_LAST_DIALOGUE_URL=dialog_url;
			$.fn.dialog.open({
				title: ccmi18n_sitemap.moveCopyPage,
				href: dialog_url,
				width: 350,
				modal: false,
				height: 350, 
				onClose: function() {
					showBranch(origCID);
				}
			});
			hideBranch(origCID);
		}
	}); 
	*/
	
	$('li.tree-node').draggable({
		handle: 'img.handle',
		opacity: 0.5,
		revert: false,
		helper: 'clone',
		start: function() {
			$(document.body).css('overflowX', 'hidden');
		},
		stop: function() {
			$(document.body).css('overflowX', 'auto');
		}
	});
	fixResortingDroppables();
	//showSitemapMessage(ccmi18n_sitemap.reorderPageMessage);
}

deleteBranchFade = function(nodeID) {
	// hides branch and its drop zone
	if (ccm_animEffects) {
		$("#tree-node" + nodeID).fadeOut(300, function() {
			$("#tree-node" + nodeID).remove();
		});
		$("#tree-dz" + nodeID).fadeOut(300, function() {
			$("#tree-dz" + nodeID).remove();
		});
	} else {
		deleteBranchDirect(nodeID);
	}	
}

deleteBranchDirect = function(nodeID) {
	// hides branch and its drop zone
	$("#tree-node" + nodeID).remove();
	$("#tree-dz" + nodeID).remove();
}

showBranch = function(nodeID) {
	var orig = $("#tree-node" + nodeID);
	$("#tree-node" + nodeID).show();
	$("#tree-dz" + nodeID).show();
}

rescanDisplayOrder = function(nodeID) {
	setLoading(nodeID);
	var queryString = "?foo=1";
	var nodes = $('#tree-root' + nodeID).children('li.tree-node');
	for (i = 0; i < nodes.length; i++) {
		if( $(nodes[i]).hasClass('ui-draggable-dragging') ) continue;
		queryString += "&cID[]=" + $(nodes[i]).attr('id').substring(9);
	}
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
		ccm_parseJSON(resp, function() {});
		removeLoading(nodeID);	
	});
}

var SITEMAP_LAST_DIALOGUE_URL='';
var ccm_sitemap_html = '';

parseSitemapResponse = function(instanceID, display_mode, select_mode, nodeID, resp) { 
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");
	container.html(resp);
	container.slideDown(150, 'easeOutExpo');
}

selectMoveCopyTarget = function(instanceID, display_mode, select_mode, destCID, origCID) {
	if (!origCID) {
		var origCID = CCM_CID;
	}
	var dialog_title = ccmi18n_sitemap.moveCopyPage;
	var dialog_url = CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?instance_id=' + instanceID + '&display_mode=' + display_mode + '&select_mode=' + select_mode + '&origCID=' + origCID + '&destCID=' + destCID;
	var dialog_height = 350;
	var dialog_width = 350;
	
	try {
		if (CCM_NODE_ACTION == '<none>') {
			if (CCM_TARGET_ID != '') {
				$('#'+CCM_TARGET_ID).val(destCID);
			}
			$.fn.dialog.closeTop();
			return;
		}
	
		if (CCM_NODE_ACTION != '')
			dialog_url = CCM_NODE_ACTION+'?destCID='+destCID;
		if (CCM_DIALOG_TITLE != '')
			dialog_title = CCM_DIALOG_TITLE;
		if (CCM_DIALOG_HEIGHT != '')
			dialog_height = CCM_DIALOG_HEIGHT;
		if (CCM_DIALOG_WIDTH != '')
			dialog_width = CCM_DIALOG_WIDTH;
	} catch(e) {
	
	}
	
	$.fn.dialog.open({
		title: dialog_title,
		href: dialog_url,
		width: dialog_width,
		appendButtons: true,
		modal: false,
		height: dialog_height,
		onClose: function() {
			//$("#tree").fadeIn(200);
			if (typeof(CCM_TARGET_ID) != "undefined" && CCM_TARGET_ID != '') {
				$('#'+CCM_TARGET_ID).val(destCID);
			}
			if (tr_moveCopyMode == true) {
				deactivateMoveCopy();
			}
		}

	});
}

selectLabel = function(e, node) {
	var cNumChildren = node.attr('tree-node-children');
	if (node.attr('sitemap-select-mode') == "move_copy_delete" || tr_moveCopyMode == true) {
		var destCID = node.attr('id').substring(10);
		var origCID = node.attr('selected-page-id');
		selectMoveCopyTarget(node.attr('sitemap-instance-id'), node.attr('sitemap-display-mode'), node.attr('sitemap-select-mode'), destCID, origCID);
	} else if (node.attr('sitemap-select-mode') == 'select_page') {
		var callback = node.parents('[sitemap-wrapper=1]').attr('sitemap-select-callback');
		if (callback == null || callback == '' || typeof(callback) == 'undefined') {
			callback = 'ccm_selectSitemapNode';
		}
		eval(callback + '(node.attr(\'id\').substring(10), unescape(node.attr(\'tree-node-title\')));');
		jQuery.fn.dialog.closeTop();
	} else {
		node.addClass('tree-label-selected');
		if (tr_activeNode != false) {
			if (tr_activeNode.attr('id') != node.attr('id')) {
				tr_activeNode.removeClass('tree-label-selected');
			}
		}
		params = {'cID': node.attr('id').substring(10), 'display_mode': node.attr('sitemap-display-mode'), 'select_mode': node.attr('sitemap-select-mode'), 'instance_id': node.attr('sitemap-instance-id'), 'canCompose': node.attr('tree-node-cancompose'), 'canWrite': node.attr('tree-node-canwrite'), 'cNumChildren': node.attr('tree-node-children'), 'cAlias': node.attr('tree-node-alias')};
		
		showPageMenu(params, e);
		tr_activeNode = node;
	}
}

ccmSitemapHighlightPageLabel = function(cID, name) {
	var sp = $("#tree-label" + cID + " span");

	if (sp.length == 0) {
		var sp = $("tr.ccm-list-record[cID=" + cID + "]");
		if (sp.length > 0) {
			$("#ccm-page-advanced-search").submit();
			
		}
	} else {
		if (name != null) {
			sp.html(name);
		}
	}
	
	sp.show('highlight');

}

activateLabels = function(instance_id, display_mode, select_mode) {
	var smwrapper = $("ul[sitemap-instance-id=" + instance_id + "]");
	smwrapper.find('div.tree-label span').unbind();
	smwrapper.find('div.tree-label span').click(function(e) {
		selectLabel(e, $(this).parent())
	}); 
	
	// now we make sure that all the items that are open are registered as open
	//if ($(this).parent().attr('sitemap-display-mode') != 'explore') {
	smwrapper.find("ul[tree-root-state=closed]").each(function() {
		var container = $(this);
		var nodeID = $(this).attr('tree-root-node-id');
		if ($(this).find('li').length > 0) {
			container.attr('tree-root-state', 'open');
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/minus.jpg');
		}
	});

	//}
	
	if (select_mode == 'select_page' || select_mode == 'move_copy_delete') {
		smwrapper.find("li.ccm-sitemap-explore-paging a").each(function() {
			$(this).click(function() {
				var treeRootNode = $(this).parentsUntil('ul').parent().attr('tree-root-node-id');
				jQuery.fn.dialog.showLoader();
				$.get($(this).attr('href'), function(r) {
					parseSitemapResponse(instance_id, display_mode, select_mode, treeRootNode, r);
					activateLabels(instance_id, display_mode, select_mode);
					jQuery.fn.dialog.hideLoader();
				});			
				
				return false;
			});
		});
	}
	if ((display_mode == 'explore' || display_mode == 'full') && (!select_mode)) {
		smwrapper.find('img.handle').addClass('moveable');
	}
	
	if (display_mode == 'full' && (!select_mode)) {
	
		//drop onto a page
		smwrapper.find('div.tree-label').droppable({
			accept: '.tree-node',
			hoverClass: 'on-drop',
			drop: function(e, ui) {
				var orig = ui.draggable;
				var destCID = $(this).attr('id').substring(10);
				var origCID = $(orig).attr('id').substring(9);
				if(destCID==origCID) return false;
				var dialog_url=CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?instance_id=' + instance_id + '&origCID=' + origCID + '&destCID=' + destCID;
				//prevent window from opening twice
				if(SITEMAP_LAST_DIALOGUE_URL==dialog_url) return false;
				else SITEMAP_LAST_DIALOGUE_URL=dialog_url;
				$.fn.dialog.open({
					title: ccmi18n_sitemap.moveCopyPage,
					href: dialog_url,
					width: 350,
					modal: false,
					height: 350, 
					appendButtons: true,
					onClose: function() {
						showBranch(origCID);
					}
				});
				//hideBranch(origCID);
			}
		}); 
		
		//addResortDroppable(nodeID);		

		smwrapper.find('li.tree-node[draggable=true]').draggable({
			handle: 'img.handle',
			opacity: 0.5,
			revert: false,
			helper: 'clone',
			start: function() {
				$(document.body).css('overflowX', 'hidden');
			},
			stop: function() {
				$(document.body).css('overflowX', 'auto');
			}
		});
	}
}

moveCopyAliasNode = function(reloadPage) {
	
	var origCID = $('#origCID').val();
	var destParentID = $('#destParentID').val();
	var destCID = $('#destCID').val();
	var ctask = $("input[name=ctask]:checked").val();
	var instance_id = $("input[name=instance_id]").val();
	var display_mode = $("input[name=display_mode]").val();
	var select_mode = $("input[name=select_mode]").val();
	var copyAll = $("input[name=copyAll]:checked").val();
	var saveOldPagePath = $("input[name=saveOldPagePath]:checked").val();
	// DO THE DEED

	params = {
	
		'origCID': origCID,
		'destCID': destCID,
		'ctask': ctask,
		'ccm_token': CCM_SECURITY_TOKEN,
		'copyAll': copyAll,
		'saveOldPagePath': saveOldPagePath
	};

	jQuery.fn.dialog.showLoader();

	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php', params, function(resp) {
		// parse response
		ccm_parseJSON(resp, function() {
			jQuery.fn.dialog.closeAll();
			jQuery.fn.dialog.hideLoader();
 			ccmAlert.hud(resp.message, 2000);
			if (reloadPage == true) {
				if (typeof(CCM_LAUNCHER_SITEMAP) != 'undefined') {
					if (CCM_LAUNCHER_SITEMAP == 'explore') {
						// we are in the dashboard and we need to actually go to the explore node
						window.location.href = CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore/-/" + destCID;
						return false;
					}
					if (CCM_LAUNCHER_SITEMAP == 'search') {
						ccm_deactivateSearchResults(CCM_SEARCH_INSTANCE_ID);
						$("#ccm-" + CCM_SEARCH_INSTANCE_ID + "-advanced-search").ajaxSubmit(function(resp) {
							ccm_parseAdvancedSearchResponse(resp, CCM_SEARCH_INSTANCE_ID);
						});
					}
				} else {
					setTimeout(function() {
						window.location.href = CCM_DISPATCHER_FILENAME + "?cID=" + resp.cID;
					}, 2000);
					return false;
				}
			}
			
			switch(ctask) {
				case "COPY":
				case "ALIAS":
					// since we're copying we show the original again
					showBranch(origCID);
					break;
				case "MOVE":
					deleteBranchDirect(origCID);
					break;
			}
			
			openSub(instance_id, destParentID, display_mode, select_mode, function() {openSub(instance_id, destCID, display_mode, select_mode)});
			jQuery.fn.dialog.closeTop();
			jQuery.fn.dialog.closeTop();
		});
	});	
}

/*
searchSitemapNode = function(cID) {
	var q = $('form#ccm-tree-search' + cID + ' input').val();
	openSubSearch(cID, q);
	return false;
}
*/

toggleSub = function(instanceID, nodeID, display_mode, select_mode) {
	ccm_hideMenus();
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");
	if (container.attr('tree-root-state') == 'closed') {
		openSub(instanceID, nodeID, display_mode, select_mode);
	} else {
		closeSub(instanceID, nodeID, display_mode, select_mode);
	}
}

setLoading = function(nodeID) {
	var listNode = $("#tree-node" + nodeID);
	listNode.removeClass('tree-node-' + listNode.attr('tree-node-type'));
	listNode.addClass('tree-node-loading');
}

removeLoading = function(nodeID) {
	var listNode = $("#tree-node" + nodeID);
	listNode.removeClass('tree-node-loading');
	listNode.addClass('tree-node-' + listNode.attr('tree-node-type'));
}

openSub = function(instanceID, nodeID, display_mode, select_mode, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	cancelReorder();
	ccm_sitemap_html = '';
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + instanceID + "&node=" + nodeID + "&display_mode=" + display_mode + "&select_mode=" + select_mode + "&selectedPageID=" + container.attr('selected-page-id'), function(resp) {
		parseSitemapResponse(instanceID, 'full', select_mode, nodeID, resp);
		activateLabels(instanceID, 'full', select_mode);
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			activateReorder();
		}

		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}

/*
openSubSearch = function(nodeID, query, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	ccm_sitemap_html = '';
	container.html('');
	container.addClass('ccm-sitemap-search-results');
	cancelReorder();
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID, {'keywords': query, 'mode': 'full'}, function(resp) {
		parseSitemapResponse('full', nodeID, resp);	
		activateLabels('full');
		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}
*/

closeSub = function(instanceID, nodeID, display_mode, select_mode) {
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");	
	if (tr_doAnim) {
		setLoading(nodeID);
		container.slideUp(150, 'easeOutExpo', function() {
			removeLoading(nodeID);
			container.attr('tree-root-state', 'closed');
			container.html('');
			$("#ccm-tree-search" + nodeID).hide();
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
			container.removeClass('ccm-sitemap-search-results');
		});
	} else {	
		container.hide();
		container.attr('tree-root-state', 'closed');
		container.removeClass('ccm-sitemap-search-results');
		$("#ccm-tree-search" + nodeID).hide();
		$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
	}

	if (tr_moveCopyMode == true) {
		$("#ccm-tree-search-trigger" + cID).show();
	}
	
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + instanceID + "&select_mode=" + select_mode + "&display_mode=" + display_mode + "&node=" + nodeID +'&display_mode=full&ctask=close-node');
}

toggleMove = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
		$("#saveOldPagePath").attr('disabled', false);
	}
}

toggleAlias = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
		$("#saveOldPagePath").attr('checked', false);
		$("#saveOldPagePath").attr('disabled', 'disabled');
	}
}

toggleCopy = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = false;
		$("#copyThisPage").get(0).checked = true;
		$("#copyChildren").get(0).disabled = false;
		$("#saveOldPagePath").attr('checked', false);
		$("#saveOldPagePath").attr('disabled', 'disabled');
	}
}

showSitemapMessage = function(msg) {
	$("#ccm-sitemap-message").addClass('message');
	$("#ccm-sitemap-message").html(msg);
	$("#ccm-sitemap-message").fadeIn(200);
}

hideSitemapMessage = function() {
	$("#ccm-sitemap-message").hide();
}

function fixResortingDroppables(){
	if (tr_reorderMode == false) {
		return false;
	}
	
	var DZs=$('.dropzone'); 
	for(var i=0;i<DZs.length;i++){ 
		var nodeID = $(DZs[i]).attr('id').substr(7); 
		if( nodeID.indexOf('-sub') > 0) {
			nodeID=nodeID.substr(0,(nodeID.length-4));
		}
		addResortDroppable(nodeID);
	}
}
//drop onto a dropzone - used only for reordering pages 
function addResortDroppable(nodeID){
		//ignore levels with only one branch
		if( $('.tree-branch' + nodeID).length<=1 ) return;
		//add reordering droppable targets
		$('div.tree-dz' + nodeID).droppable({
			accept: '.tree-branch' + nodeID,
			activeClass: 'dropzone-ready',
			hoverClass: 'dropzone-active', 
			drop: function(e, ui) {
				var node = ui.draggable;
				$(node).insertAfter(this);
				var dzNode = $(node).attr('id').substring(9);
				$("#tree-dz" + dzNode).insertAfter($(node));
				rescanDisplayOrder($(this).attr('tree-parent'));			
			}
		});
}

ccmSitemapExploreNode = function(instance_id, display_mode, select_mode, cID, selectedPageID) {
	jQuery.fn.dialog.showLoader();
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php", {'instance_id': instance_id, 'display_mode': display_mode, 'select_mode' : select_mode, 'node': cID, 'selectedPageID': selectedPageID}, function(resp) {  
		parseSitemapResponse(instance_id, 'explore', select_mode, 0, resp);
		activateLabels(instance_id, 'explore', select_mode);
		jQuery.fn.dialog.hideLoader();
		ccm_sitemap_html = '';
	});
}

ccmSitemapLoad = function(instance_id, display_mode, select_mode, node, selectedPageID, onComplete) {
	if (select_mode == 'move_copy_delete' || select_mode == 'select_page') {
		ccmSitemapExploreNode(instance_id, display_mode, select_mode, node, selectedPageID);
	} else if (display_mode == 'full') {

		ccm_hidePane = function() {
			// overrides the typically UI hidepane because we're only seeing these on thickbox elements
			jQuery.fn.dialog.closeTop();
		}

		activateLabels(instance_id, display_mode, select_mode);
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			activateReorder();
		}
		tr_doAnim = true;
		tr_parseSubnodes = false;
		ccm_sitemap_html = '';

	} else {
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			$("ul[sitemap-instance-id=" + instance_id + "]").sortable({
				cursor: 'move',
				items: 'li[draggable=true]',
				opacity: 0.5,
				stop: function(sor) {
					var ss = $("ul[sitemap-instance-id=" + instance_id + "]").sortable('toArray');
					var queryString = '';
					for (i = 0; i < ss.length; i++) {
						if (ss[i] != '') {
							queryString += '&cID[]=' + ss[i].substring(9);
						}
					}

					$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
						ccm_parseJSON(resp, function() {});
					});
				}
			});
		}
		activateLabels(instance_id, display_mode, select_mode);
	}
	
	if (onComplete) {
		onComplete();	
	}
}

ccm_sitemapSetupSearch = function(instance_id) {
	ccm_setupAdvancedSearch(instance_id); 
	ccm_sitemapSetupSearchPages(instance_id);
	ccm_searchActivatePostFunction[instance_id] = function() {
		ccm_sitemapSetupSearchPages(instance_id);
		ccm_sitemapSearchSetupCheckboxes(instance_id);	
	}
	ccm_sitemapSearchSetupCheckboxes(instance_id);	
}

ccm_sitemapSearchSetupCheckboxes = function(instance_id) {
	$("#ccm-" + instance_id + "-list-cb-all").click(function(e) {
		e.stopPropagation();
		if ($(this).prop('checked') == true) {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		if ($("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-" + instance_id + "-list-multiple-operations").change(function() {
		var action = $(this).val();
		cIDstring = '';
		$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").each(function() {
			cIDstring=cIDstring+'&cID[]='+$(this).val();
		});
		switch(action) {
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/delete?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.deletePages				
				});
				break;
			case "design":
				jQuery.fn.dialog.open({
					width: 610,
					height: 405,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/design?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.pageDesign				
				});
				break;
			case 'move_copy':
				jQuery.fn.dialog.open({
					width: 640,
					height: 340,
					modal: false,
					href: CCM_TOOLS_PATH + '/sitemap_overlay?instance_id=' + instance_id + '&select_mode=move_copy_delete&' + cIDstring,
					title: ccmi18n_sitemap.moveCopyPage				
				});
				break;
			case 'speed_settings':
				jQuery.fn.dialog.open({
					width: 610,
					height: 340,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/speed_settings?' + cIDstring,
					title: ccmi18n_sitemap.speedSettingsTitle				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/pages/bulk_metadata_update?' + cIDstring,
					title: ccmi18n_sitemap.pagePropertiesTitle				
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});
}

ccm_sitemapSetupSearchPages = function(instance_id) {
	$('#ccm-' + instance_id + '-list tr').click(function(e){
		var node = $(this);
		if (node.hasClass('ccm-results-list-header')) {
			return false;
		}
		
		if (node.attr('sitemap-select-mode') == 'select_page') {
			var callback = node.attr('sitemap-select-callback');
			if (callback == null || callback == '' || typeof(callback) == 'undefined') {
				callback = 'ccm_selectSitemapNode';
			}
			eval(callback + '(node.attr(\'cID\'), unescape(node.attr(\'cName\')));');
			jQuery.fn.dialog.closeTop();
		} else if (node.attr('sitemap-select-mode') == 'move_copy_delete') {
			var destCID = node.attr('cID');
			var origCID = node.attr('selected-page-id');
			selectMoveCopyTarget(node.attr('sitemap-instance-id'), node.attr('sitemap-display-mode'), node.attr('sitemap-select-mode'), destCID, origCID);
		} else {
			params = {'cID': node.attr('cID'), 'select_mode': node.attr('sitemap-select-mode'), 'display_mode': node.attr('sitemap-display-mode'), 'instance_id': node.attr('sitemap-instance-id'), 'canCompose': node.attr('tree-node-cancompose'), 'canWrite': node.attr('canWrite'), 'cNumChildren': node.attr('cNumChildren'), 'cAlias': node.attr('cAlias')};		
			showPageMenu(params, e);
		}
	});

}

ccm_sitemapSelectDisplayMode = function(instance_id, display_mode, select_mode, selectedPageID) {
	// finds the selector for the instance of the sitemap and reloads it to be this mode
	
	var ul = $("ul[sitemap-instance-id=" + instance_id + "]");
	ul.html('');
	ul.attr('sitemap-display-mode', display_mode);
	ul.attr('sitemap-select-mode', select_mode);
	ul.attr('sitemap-display-mode', display_mode);
	if (display_mode == 'explore') {
		var node =1;
	} else {
		var node = 0;
	}
	ccmSitemapLoad(instance_id, display_mode, select_mode, node, selectedPageID, function() {
		if (display_mode == 'explore') {
			$("div[sitemap-wrapper=1][sitemap-instance-id=" + instance_id + "]").addClass("ccm-sitemap-explore");
		} else {
			$("div[sitemap-wrapper=1][sitemap-instance-id=" + instance_id + "]").removeClass("ccm-sitemap-explore");
		}
	});
	
	// now we save the preference	
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?task=save_sitemap_display_mode&display_mode=" + display_mode);
}

ccm_sitemapDeletePages = function(searchInstance) {
	$("#ccm-" + searchInstance + "-delete-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_sitemapUpdateDesign = function(searchInstance) {
	$("#ccm-" + searchInstance + "-design-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

$(function() {
	/*
	$(document).ajaxError(function(event, request, settings) {
		ccmAlert.notice(ccmi18n_sitemap.loadErrorTitle, request.responseText);
	});
	*/
	
	$(document).click(function() {
		ccm_hideMenus();
		$("div.tree-label").removeClass('tree-label-selected');
	});

	$("#ccm-show-all-pages-cb").click(function() {
		var showSystemPages = $(this).get(0).checked == true ? 1 : 0;
		$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?show_system=" + showSystemPages, function(resp) {
			location.reload();
		});
	});
	

});

function ccm_previewInternalTheme(cID, themeID,themeName){
	var ctID=$("input[name=ctID]").val();
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeID="+themeID+'&previewCID='+cID+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});	
}

function ccm_previewMarketplaceTheme(cID, themeCID,themeName,themeHandle){
	var ctID=$("input[name=ctID]").val();
	
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeCID="+themeCID+'&previewCID='+cID+'&themeHandle='+encodeURIComponent(themeHandle)+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});
}

ccm_marketplaceDetailShowMore = function() {	
	$(".ccm-marketplace-item-information-more").hide();
	$(".ccm-marketplace-item-information-inner").css('max-height', 'none');
}

ccm_marketplaceUpdatesShowMore = function(obj) {	
	$(obj).parent().hide();
	$(obj).parent().parent().find('.ccm-marketplace-update-changelog').css('max-height', 'none');
}

ccm_enableDesignScrollers = function() {
	$("a.ccm-scroller-l").hover(function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_l_active.png');
	}, function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_l.png');
	});

	$("a.ccm-scroller-r").hover(function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_r_active.png');
	}, function() {
		$(this).find('img').attr('src', CCM_IMAGE_PATH + '/button_scroller_r.png');
	});
	
	var numThumbs = 4;	
	var thumbWidth = 132;
	
	$('a.ccm-scroller-r').unbind('click');
	$('a.ccm-scroller-l').unbind('click');
	
	$('a.ccm-scroller-r').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');

		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) - migratePos;
		currentPage++;
		
		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == numPages) {
			$(this).hide();
		}
		if (currentPage > 1) {
			$(this).siblings('a.ccm-scroller-l').show();
		}
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/
		
		$(item).css('left', currentPos + 'px');
		
		
	});

	$('a.ccm-scroller-l').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');
		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) + migratePos;
		currentPage--;

		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == 1) {
			$(this).hide();
		}
		
		if (currentPage < numPages) {
			$(this).siblings('a.ccm-scroller-r').show();
		}
		
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/

		$(item).css('left', currentPos + 'px');
		
		
	});
	$('a.ccm-scroller-l').hide();
	$('a.ccm-scroller-r').each(function() {
		if (parseInt($(this).parent().attr('num-pages')) == 1) {
			$(this).hide();
		}
	});

	$("#ccm-select-page-type a").click(function() {
		$("#ccm-select-page-type li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		$("input[name=ctID]").val($(this).attr('ccm-page-type-id'));
	});

	$("#ccm-select-theme a").click(function() {
		$("#ccm-select-theme li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		$("input[name=plID]").val($(this).attr('ccm-theme-id'));
	});



}

$(function() {
	ccm_intelligentSearchActivateResults();	
	ccm_intelligentSearchDoOffsite($('#ccm-nav-intelligent-search').val());
});

	var ccm_quickNavTimer = false;
	
	ccm_showQuickNav = function(callback) {
		clearTimeout(ccm_quickNavTimer);
		if ($('#ccm-quick-nav').is(':visible')) {
			if (typeof(callback) == 'function') {
				callback();
			}
		} else {
			$("#ccm-quick-nav").fadeIn(120, 'easeOutExpo', function() {
				if (typeof(callback) == 'function') {
					callback();
				}
			});
		}
	}
	
	ccm_hideQuickNav = function() {
		$("#ccm-quick-nav").fadeOut(120, 'easeInExpo');
		clearTimeout(ccm_quickNavTimer);
	}
	
	ccm_togglePageHelp = function(e) {
		if ($('#twipsy-holder .popover').is(':visible')) {
			$('#ccm-page-help').popover('hide');	
		} else {
			$('#ccm-page-help').popover('show');	
			e.stopPropagation();
			$(window).bind('click.popover', function() {
				$('#ccm-page-help').popover('hide');		
				$(window).unbind('click.popover');
			});
		}
	}
	
	ccm_toggleQuickNav = function(cID, token) {
		var l = $("#ccm-add-to-quick-nav");
		if (l.hasClass('ccm-icon-favorite-selected')) {
			l.removeClass('ccm-icon-favorite-selected').addClass('ccm-icon-favorite');
		} else {
			l.removeClass('ccm-icon-favorite').addClass('ccm-icon-favorite-selected');
		}
		ccm_showQuickNav(function() {
			$.getJSON(CCM_TOOLS_PATH + '/dashboard/add_to_quick_nav', {
				'cID': cID,
				'token': token
			}, function(r) {
				if (r.result == 'add') { 
					$("#ccm-quick-nav-favorites").append('<li />');
					var accepter = $("#ccm-quick-nav-favorites li:last-child");
					accepter.attr('id','ccm-quick-nav-page-' + cID).css('display','none');
					var title = l.parent().parent().parent().find('h3');
					title.css('display','inline');
					accepter.html(r.link).css('visibility','hidden').show();
					title.effect("transfer", { to: accepter, 'easing': 'easeOutExpo'}, 600, function() {
						accepter.hide().css('visibility','visible').fadeIn(240, 'easeInExpo');			
						title.css('display','block');
						ccm_quickNavTimer = setTimeout(function() {
							ccm_hideQuickNav();
						}, 1000);
					});
				} else {
					$("#ccm-quick-nav-page-" + cID).fadeOut(240, 'easeOutExpo');
					ccm_quickNavTimer = setTimeout(function() {
						ccm_hideQuickNav();
					}, 1000);
				}
			});
		});
	}
	
	ccm_activateToolbar = function() {
		$("#ccm-toolbar,#ccm-quick-nav").hover(function() {
			ccm_showQuickNav();
		}, function() {
			ccm_quickNavTimer = setTimeout(function() {
				ccm_hideQuickNav();
			}, 1000);
		});
		
		$("#ccm-dashboard-overlay").css('visibility','visible').hide();
	
		$("#ccm-nav-intelligent-search-wrapper").click(function() {
			$("#ccm-nav-intelligent-search").focus();
		});
		$("#ccm-nav-intelligent-search").focus(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass("ccm-system-nav-selected");
			if ($("#ccm-dashboard-overlay").is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			}
		});
		
		$("#ccm-nav-dashboard").click(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass('ccm-system-nav-selected');
			$("#ccm-nav-intelligent-search").val('');
			$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');
	
			if ($('#ccm-edit-overlay').is(':visible')) {
				$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.ccm-edit');
			}
	
			if ($('#ccm-dashboard-overlay').is(':visible')) {
				$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			} else {
				$("#ccm-dashboard-overlay").fadeIn(160, 'easeOutExpo');
				$(window).bind('click.dashboard-nav', function() {
					$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
					$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
					$(window).unbind('click.dashboard-nav');
				});
			}
			return false;
		});
	
		$("#ccm-nav-intelligent-search").bind('keydown.ccm-intelligent-search', function(e) {
			if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 38) {
				e.preventDefault();
				e.stopPropagation();
	
				if (e.keyCode == 13 && $("a.ccm-intelligent-search-result-selected").length > 0) {
					var href = $("a.ccm-intelligent-search-result-selected").attr('href');
					if (!href || href == '#' || href == 'javascript:void(0)') {
						$("a.ccm-intelligent-search-result-selected").click();
					} else {
						window.location.href = href;
					}
				}
				var visibleitems = $("#ccm-intelligent-search-results li:visible");
				var sel;
				
				if (e.keyCode == 40 || e.keyCode == 38) {
					$.each(visibleitems, function(i, item) {
						if ($(item).children('a').hasClass('ccm-intelligent-search-result-selected')) {
							if (e.keyCode == 38) {
								io = visibleitems[i-1];
							} else {
								io = visibleitems[i+1];
							}
							sel = $(io).find('a');
						}
					});
					if (sel && sel.length > 0) {
						$("a.ccm-intelligent-search-result-selected").removeClass();
						$(sel).addClass('ccm-intelligent-search-result-selected');				
					}
				}
			} 
		});
	
		$("#ccm-nav-intelligent-search").bind('keyup.ccm-intelligent-search', function(e) {
			ccm_intelligentSearchDoOffsite($(this).val());
		});
	
		$("#ccm-nav-intelligent-search").blur(function() {
			$(this).parent().removeClass("ccm-system-nav-selected");
		});
		
		
		$("#ccm-nav-intelligent-search").liveUpdate('ccm-intelligent-search-results', 'intelligent-search');
		$("#ccm-nav-intelligent-search").bind('click', function(e) { if ( this.value=="") { 
			$("#ccm-intelligent-search-results").hide();
		}});
		
		$("#ccm-toolbar-nav-properties").dialog();
		$("#ccm-toolbar-add-subpage").dialog();
		$("#ccm-toolbar-nav-versions").dialog();
		$("#ccm-toolbar-nav-design").dialog();
		$("#ccm-toolbar-nav-permissions").dialog();
		$("#ccm-toolbar-nav-speed-settings").dialog();
		$("#ccm-toolbar-nav-move-copy").dialog();
		$("#ccm-toolbar-nav-delete").dialog();
	
		$("#ccm-nav-edit").click(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass('ccm-system-nav-selected');
			$("#ccm-nav-intelligent-search").val('');
			$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');
	
			if ($('#ccm-dashboard-overlay').is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			}
	
			if ($('#ccm-edit-overlay').is(':visible')) {
				$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
				$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.ccm-edit');
			} else {
				$("#ccm-edit-overlay").click(function(e) {
					e.stopPropagation();
				});
				setTimeout("$('#ccm-check-in-comments').focus();",300);
				$("#ccm-check-in-preview").click(function() {
					$("#ccm-approve-field").val('PREVIEW');
					$("#ccm-check-in").submit();
				});
			
				$("#ccm-check-in-discard").click(function() {
					$("#ccm-approve-field").val('DISCARD');
					$("#ccm-check-in").submit();
				});
			
				$("#ccm-check-in-publish").click(function() {
					$("#ccm-approve-field").val('APPROVE');
					$("#ccm-check-in").submit();
				});
				var posX = $(this).position().left;
				if (posX > 0) {
					posX = posX - 20; // BACK it up!
				}
				$("#ccm-edit-overlay").css('left', posX + "px");
				$("#ccm-edit-overlay").fadeIn(160, 'easeOutExpo', function() {
					$(this).find('a').click(function() {
						ccm_toolbarCloseEditMenu();
					});
				});
				$(window).bind('click.ccm-edit', function() {
					ccm_toolbarCloseEditMenu();				
				});
			}
			return false;
		});

	}
	var ajaxtimer = null;
	var ajaxquery = null;

	ccm_toolbarCloseEditMenu = function() {
		$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
		$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
		$(window).unbind('click.ccm-edit');
	}
	
	ccm_intelligentSearchActivateResults = function() {
		if ($("#ccm-intelligent-search-results div:visible").length == 0) {
			$("#ccm-intelligent-search-results").hide();
		}
		$("#ccm-intelligent-search-results a").hover(function() {
			$('a.ccm-intelligent-search-result-selected').removeClass();
			$(this).addClass('ccm-intelligent-search-result-selected');
		}, function() {
			$(this).removeClass('ccm-intelligent-search-result-selected');
		});
	}

	ccm_intelligentSearchDoOffsite = function(query) {	
		if (!query) {
			return;
		}
		if (query.trim().length > 2) {
			if (query.trim() == ajaxquery) {
				return;
			}
			
			if (ajaxtimer) {
				window.clearTimeout(ajaxtimer);
			}
			ajaxquery = query.trim();
			ajaxtimer = window.setTimeout(function() {
				ajaxtimer = null;
				$("#ccm-intelligent-search-results-list-marketplace").parent().show();
				$("#ccm-intelligent-search-results-list-help").parent().show();
				$("#ccm-intelligent-search-results-list-marketplace").parent().addClass('ccm-intelligent-search-results-module-loading');
				$("#ccm-intelligent-search-results-list-help").parent().addClass('ccm-intelligent-search-results-module-loading');
	
				$.getJSON(CCM_TOOLS_PATH + '/marketplace/intelligent_search', {
					'q': ajaxquery
				},
				function(r) {
					$("#ccm-intelligent-search-results-list-marketplace").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-marketplace").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						var _onclick = "ccm_openAddonLauncher(" + rr.mpID + ")";
						$("#ccm-intelligent-search-results-list-marketplace").append('<li><a href="javascript:void(0)" onclick="' + _onclick + '"><img src="' + rr.img + '" />' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					ccm_intelligentSearchActivateResults();
				}).error(function() {
					$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
				});
	
				$.getJSON(CCM_TOOLS_PATH + '/get_remote_help', {
					'q': ajaxquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-help").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-help").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-help").append('<li><a href="' + rr.href + '">' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-help").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					ccm_intelligentSearchActivateResults();

				}).error(function() {
					$("#ccm-intelligent-search-results-list-help").parent().hide();
				});
	
			}, 500);
		}
	}
var ccm_arrangeMode = false;
var ccm_selectedDomID = false;
var ccm_isBlockError = false;
var ccm_activeMenu = false;
var ccm_blockError = false;

ccm_menuInit = function(obj) {
	
	if (CCM_EDIT_MODE && (!CCM_ARRANGE_MODE)) {
		switch(obj.type) {
			case "BLOCK":
				$("#b" + obj.bID + "-" + obj.aID).mouseover(function(e) {
					ccm_activate(obj, "#b" + obj.bID + "-" + obj.aID);
				});
				break;
			case "AREA":
				$("#a" + obj.aID + "controls").mouseover(function(e) {
					ccm_activate(obj, "#a" + obj.aID + "controls");
				});
				break;
		}
	}	
}

ccm_showBlockMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	ccm_activeMenu = true;
	
	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-block-menu" + obj.bID + "-" + obj.aID);

	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-block-menu" + obj.bID + "-" + obj.aID;
		el.className = "ccm-menu ccm-ui";
		el.style.display = "block";
		el.style.visibility = "hidden";
		document.body.appendChild(el);
		
		bobj = $("#ccm-block-menu" + obj.bID + "-" + obj.aID);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
		html += '<ul>';
		//html += '<li class="header"></li>';
		if (obj.canWrite) {
			html += (obj.editInline) ? '<li><a class="ccm-menu-icon ccm-icon-edit-menu" onclick="ccm_hideMenus()" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=edit#_edit' + obj.bID + '">' + ccmi18n.editBlock + '</a></li>'
				: '<li><a class="ccm-menu-icon ccm-icon-edit-menu" onclick="ccm_hideMenus()" dialog-title="' + ccmi18n.editBlock + ' ' + obj.btName + '" dialog-append-buttons="true" dialog-modal="false" dialog-on-close="ccm_blockWindowAfterClose()" dialog-width="' + obj.width + '" dialog-height="' + obj.height + '" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=edit">' + ccmi18n.editBlock + '</a></li>';
		}
		if (obj.canWriteStack) {
			html += '<li><a class="ccm-menu-icon ccm-icon-edit-menu" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/blocks/stacks/-/view_details/' + obj.stID + '">' + ccmi18n.editStackContents + '</a></li>'
			html += '<li class="header"></li>';
			
		}
		if (obj.canCopyToScrapbook) {
			html += '<li><a class="ccm-menu-icon ccm-icon-clipboard-menu" id="menuAddToScrapbook' + obj.bID + '-' + obj.aID + '" href="#" onclick="javascript:ccm_addToScrapbook(' + obj.cID + ',' + obj.bID + ',\'' + encodeURIComponent(obj.arHandle) + '\');return false;">' + ccmi18n.copyBlockToScrapbook + '</a></li>';
		}

		if (obj.canArrange) {
			html += '<li><a class="ccm-menu-icon ccm-icon-move-menu" id="menuArrange' + obj.bID + '-' + obj.aID + '" href="javascript:ccm_arrangeInit()">' + ccmi18n.arrangeBlock + '</a></li>';
		}
		if (obj.canDelete) {
			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" id="menuDelete' + obj.bID + '-' + obj.aID + '" href="#" onclick="javascript:ccm_deleteBlock(' + obj.cID + ',' + obj.bID + ',' + obj.aID + ', \'' + encodeURIComponent(obj.arHandle) + '\', \'' + obj.deleteMessage + '\');return false;">' + ccmi18n.deleteBlock + '</a></li>';
		} 		
		if (obj.canDesign || obj.canWrite) {
			html += '<li class="ccm-menu-separator"></li>';
		}
		if (obj.canDesign) {
			html += '<li><a class="ccm-menu-icon ccm-icon-design-menu" onclick="ccm_hideMenus()" dialog-modal="false" dialog-title="' + ccmi18n.changeBlockBaseStyle + '" dialog-width="450" dialog-height="420" id="menuChangeCSS' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=block_css&modal=true&width=300&height=100" title="' + ccmi18n.changeBlockCSS + '">' + ccmi18n.changeBlockCSS + '</a></li>';
		}
		if (obj.canWrite) {
			html += '<li><a class="ccm-menu-icon ccm-icon-custom-template-menu" onclick="ccm_hideMenus()" dialog-modal="false" dialog-title="' + ccmi18n.changeBlockTemplate + '" dialog-width="300" dialog-height="100" id="menuChangeTemplate' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=template&modal=true&width=300&height=100" title="' + ccmi18n.changeBlockTemplate + '">' + ccmi18n.changeBlockTemplate + '</a></li>';
		}

		if (obj.canModifyGroups || obj.canAliasBlockOut || obj.canSetupComposer) {
			html += '<li class="ccm-menu-separator"></li>';
		}

		if (obj.canModifyGroups) {
			html += '<li><a title="' + ccmi18n.setBlockPermissions + '" onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-permissions-menu" dialog-width="400" dialog-height="380" id="menuBlockGroups' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=groups" dialog-title="' + ccmi18n.setBlockPermissions + '">' + ccmi18n.setBlockPermissions + '</a></li>';
		}
		if (obj.canAliasBlockOut) {
			html += '<li><a class="ccm-menu-icon ccm-icon-setup-child-pages-menu" onclick="ccm_hideMenus()" dialog-width="550" dialog-height="450" id="menuBlockAliasOut' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=child_pages" dialog-title="' + ccmi18n.setBlockAlias + '">' + ccmi18n.setBlockAlias + '</a></li>';
		}
		if (obj.canSetupComposer) {
			html += '<li><a class="ccm-menu-icon ccm-icon-setup-composer-menu" onclick="ccm_hideMenus()" dialog-width="300" dialog-modal="false" dialog-height="150" id="menuBlockSetupComposer' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + obj.cID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=composer" dialog-title="' + ccmi18n.setBlockComposerSettings + '">' + ccmi18n.setBlockComposerSettings + '</a></li>';
		}
		

		html += '</ul>';
		html += '</div></div></div>';
		bobj.append(html);
		
		// add dialog elements where necessary
		if (obj.canWrite && (!obj.editInline)) {
			$('a#menuEdit' + obj.bID + '-' + obj.aID).dialog();
			$('a#menuChangeTemplate' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canDesign) {
			$('a#menuChangeCSS' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canAliasBlockOut) {
			$('a#menuBlockAliasOut' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canSetupComposer) {
			$('a#menuBlockSetupComposer' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canModifyGroups) {
			$("#menuBlockGroups" + obj.bID + '-' + obj.aID).dialog();
		}

	} else {
		bobj = $("#ccm-block-menu" + obj.bID + '-' + obj.aID);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_openAreaAddBlock = function(arHandle, addOnly, cID) {
	if (!addOnly) {	
		addOnly = 0;
	}
	
	if (!cID) {
		cID = CCM_CID;
	}
	
	$.fn.dialog.open({
		title: ccmi18n.blockAreaMenu,
		href: CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + cID + '&atask=add&arHandle=' + arHandle + '&addOnly=' + addOnly,
		width: 550,
		modal: false,
		height: 380
	});
}

ccm_showAreaMenu = function(obj, e) {
	var addOnly = (obj.addOnly)?1:0;
	ccm_activeMenu = true;
	if (e.shiftKey) {
		ccm_openAreaAddBlock(obj.arHandle, addOnly);
	} else {
		e.stopPropagation();
		
		// now, check to see if this menu has been made
		var aobj = document.getElementById("ccm-area-menu" + obj.aID);
		
		if (!aobj) {
			// create the 1st instance of the menu
			el = document.createElement("DIV");
			el.id = "ccm-area-menu" + obj.aID;
			el.className = "ccm-menu ccm-ui";
			el.style.display = "none";
			document.body.appendChild(el);
			
			aobj = $("#ccm-area-menu" + obj.aID);
			aobj.css("position", "absolute");
			
			//contents  of menu
			var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
			html += '<ul>';
			//html += '<li class="header"></li>';
			if (obj.canAddBlocks) {
				html += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-block-menu" dialog-title="' + ccmi18n.addBlockNew + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewBlock' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=add&addOnly=' + addOnly + '">'+ ccmi18n.addBlockNew + '</a></li>';
				html += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-stack-menu" dialog-title="' + ccmi18n.addBlockStack + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewStack' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=add_from_stack&addOnly=' + addOnly + '">' + ccmi18n.addBlockStack + '</a></li>';
				html += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-clipboard-menu" dialog-title="' + ccmi18n.addBlockPaste + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddPaste' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=paste&addOnly=' + addOnly + '">' + ccmi18n.addBlockPaste + '</a></li>';
			}
			if (obj.canAddBlocks && (obj.canDesign || obj.canLayout)) {
				html += '<li class="ccm-menu-separator"></li>';
			}
			if (obj.canLayout) {
				html += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-layout-menu" dialog-title="' + ccmi18n.addAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="280" id="menuAreaLayout' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=layout">' + ccmi18n.addAreaLayout + '</a></li>';
			}
			if (obj.canDesign) {
				html += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-design-menu" dialog-title="' + ccmi18n.changeAreaCSS + '" dialog-modal="false" dialog-width="450" dialog-height="420" id="menuAreaStyle' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=design">' + ccmi18n.changeAreaCSS + '</a></li>';
			}
			if (obj.canWrite && obj.canModifyGroups) { 
				html += '<li class="ccm-menu-separator"></li>';			
			}
			if (obj.canModifyGroups) {
				html += '<li><a onclick="ccm_hideMenus()" title="' + ccmi18n.setAreaPermissions + '" dialog-modal="false" class="ccm-menu-icon ccm-icon-permissions-menu" dialog-width="580" dialog-height="420" id="menuAreaGroups' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=groups" dialog-title="' + ccmi18n.setAreaPermissions + '">' + ccmi18n.setAreaPermissions + '</a></li>';
			}
			
			html += '</ul>';
			html += '</div></div></div>';
			aobj.append(html);
			
			// add dialog elements where necessary
			if (obj.canAddBlocks) {
				$('a#menuAddNewBlock' + obj.aID).dialog();
				$('a#menuAddNewStack' + obj.aID).dialog();
				$('a#menuAddPaste' + obj.aID).dialog(); 
			}
			if (obj.canWrite) {
				$('a#menuAreaStyle' + obj.aID).dialog();
				$('a#menuAreaLayout' + obj.aID).dialog();
			}
			if (obj.canModifyGroups) {
				$('a#menuAreaGroups' + obj.aID).dialog();
			}
		
		} else {
			aobj = $("#ccm-area-menu" + obj.aID);
		}

		ccm_fadeInMenu(aobj, e);		

	}
}

ccm_hideHighlighter = function() {
	$("#ccm-highlighter").css('display', 'none');
	$('div.ccm-menu-hotspot-active').removeClass('ccm-menu-hotspot-active');
}

ccm_addError = function(err) {
	if (!ccm_isBlockError) {
		ccm_blockError += '<ul>';
	}
	
	ccm_isBlockError = true;
	ccm_blockError += "<li>" + err + "</li>";;
}

ccm_resetBlockErrors = function() {
	ccm_isBlockError = false;
	ccm_blockError = "";
}

ccm_addToScrapbook = function(cID, bID, arHandle) {
	ccm_mainNavDisableDirectExit();
	// got to grab the message too, eventually
	ccm_hideHighlighter();
	$.ajax({
	type: 'POST',
	url: CCM_TOOLS_PATH + '/pile_manager.php',
	data: 'cID=' + cID + '&bID=' + bID + '&arHandle=' + arHandle + '&btask=add&scrapbookName=userScrapbook',
	success: function(resp) {
		ccm_hideHighlighter();
		ccmAlert.hud(ccmi18n.copyBlockToScrapbookMsg, 2000, 'add', ccmi18n.copyBlockToScrapbook);
	}});		

}

ccm_deleteBlock = function(cID, bID, aID, arHandle, msg) {
	if (confirm(msg)) {
		ccm_mainNavDisableDirectExit();
		// got to grab the message too, eventually
		ccm_hideHighlighter();
		$d = $("#b" + bID + '-' + aID);
		$d.hide();
		ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle
		})
	}	
}

ccm_hideMenus = function() {
	/* 1st, hide all items w/the css menu class */
	ccm_activeMenu = false;
	$("div.ccm-menu").hide();
	$("div.ccm-menu").css('visibility', 'hidden');
	$("div.ccm-menu").show();
}

ccm_parseBlockResponse = function(r, currentBlockID, task) {
	try { 
		r = r.replace(/(<([^>]+)>)/ig,""); // because some plugins add bogus HTML after our JSON requests and screw everything up
		resp = eval('(' + r + ')');
		if (resp.error == true) {
			var message = '<ul>'
			for (i = 0; i < resp.response.length; i++) {						
				message += '<li>' + resp.response[i] + '<\/li>';
			}
			message += '<\/ul>';
			ccmAlert.notice(ccmi18n.error, message);
		} else {
			ccm_blockWindowClose();
			if (resp.cID) {
				cID = resp.cID; 
			} else {
				cID = CCM_CID;
			}
			var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';	 
			$.get(action, 		
				function(r) { 
					if (task == 'add') {
						if ($("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).length > 0) {
							$("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).append(r);
						} else {
							$("#a" + resp.aID).append(r);
						}
					} else {
						$('#b' + currentBlockID + '-' + resp.aID).before(r).remove();
					}
					jQuery.fn.dialog.hideLoader();
					ccm_mainNavDisableDirectExit();
					if (task == 'add') {
						ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
						jQuery.fn.dialog.closeAll();
					} else {
						ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
					}
					if (typeof window.ccm_parseBlockResponsePost == 'function') {
						ccm_parseBlockResponsePost(resp);
					}
				}
			);
		}
	} catch(e) { 
		ccmAlert.notice(ccmi18n.error, r); 
	}
}

ccm_mainNavDisableDirectExit = function(disableShow) {
	// make sure that exit edit mode is enabled
	$("#ccm-exit-edit-mode-direct").hide();
	if (!disableShow) {
		$("#ccm-exit-edit-mode-comment").show();
	}
}

ccm_setupBlockForm = function(form, currentBlockID, task) {
	form.ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			ccm_hideHighlighter();
			$('input[name=ccm-block-form-method]').val('AJAX');
			jQuery.fn.dialog.showLoader();
			return ccm_blockFormSubmit();
		},
		success: function(r) {
			ccm_parseBlockResponse(r, currentBlockID, task);
		}
	});
	
}



ccm_activate = function(obj, domID) { 
	if (ccm_arrangeMode || ccm_activeMenu) {
		return false;
	}
	

	
	if (ccm_selectedDomID) {
		$(ccm_selectedDomID).removeClass('ccm-menu-hotspot-active');
	}
	
	aobj = $(domID);
	aobj.addClass('ccm-menu-hotspot-active');
	ccm_selectedDomID = domID;
	
	offs = aobj.offset();

	$("#ccm-highlighter").hide();
	
	$("#ccm-highlighter").css("width", aobj.outerWidth());
	$("#ccm-highlighter").css("height", aobj.outerHeight());
	$("#ccm-highlighter").css("top", offs.top);
	$("#ccm-highlighter").css("left", offs.left);
	$("#ccm-highlighter").fadeIn(120, 'easeOutExpo');
	/*
	$("#ccmMenuHighlighter").mouseover(
		function() {clearTimeout(ccm_deactivateTimer)}
	);
	*/
	$("#ccm-highlighter").mouseout(function(e) {
		if (!ccm_activeMenu) {
			if (!e.target) {
				ccm_hideHighlighter();
			} else if ($(e.toElement).parents('div.ccm-menu').length == 0) {
				ccm_hideHighlighter();
			}
		}
	});
	
	$("#ccm-highlighter").unbind('click');
	$("#ccm-highlighter").click(
		function(e) {
			switch(obj.type) {
				case "BLOCK":
					ccm_showBlockMenu(obj, e);
					break;
				case "AREA":
					ccm_showAreaMenu(obj,e);
					break;
			}
		}
	);
}

ccm_editInit = function() {

	document.write = function() {
		// stupid javascript in html blocks
		void(0);
	}

	$(document.body).append('<div style="position: absolute; display:none" id="ccm-highlighter">&nbsp;</div>');
	$(document).click(function() {ccm_hideMenus();});

	$("div.ccm-menu a").bind('click.hide-menu', function(e) {
		ccm_hideMenus();
		return false;	
	});
	

		
}

ccm_triggerSelectUser = function(uID, uName, uEmail) {
	alert(uID);
	alert(uName);
	alert(uEmail);
}

ccm_setupUserSearch = function() {
	$("#ccm-user-list-cb-all").click(function() {
		if ($(this).prop('checked') == true) {
			$('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-user-list-cb input[type=checkbox]").click(function(e) {
		if ($("td.ccm-user-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-user-list-multiple-operations").change(function() {
		var action = $(this).val();
		switch(action) {
			case 'choose':
				var idstr = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					ccm_triggerSelectUser($(this).val(), $(this).attr('user-name'), $(this).attr('user-email'));
				});
				jQuery.fn.dialog.closeTop();
				break;
			case "properties": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_properties?' + uIDstring,
					title: ccmi18n.properties				
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});

	$("div.ccm-user-search-advanced-groups-cb input[type=checkbox]").unbind();
	$("div.ccm-user-search-advanced-groups-cb input[type=checkbox]").click(function() {
		$("#ccm-user-advanced-search").submit();
	});

}

ccm_triggerSelectGroup = function(gID, gName) {
	alert(gID);
	alert(gName);
}

ccm_setupGroupSearch = function() {
	$('div.ccm-group a').unbind();
	$('div.ccm-group a').each(function(i) {
		var gla = $(this);
		$(this).click(function() {
			ccm_triggerSelectGroup(gla.attr('group-id'), gla.attr('group-name'));
			$.fn.dialog.closeTop();
			return false;
		});
	});	
	$("#ccm-group-search").ajaxForm({
		beforeSubmit: function() {
			$("#ccm-group-search-wrapper").html("");	
		},
		success: function(resp) {
			$("#ccm-group-search-wrapper").html(resp);	
		}
	});
	
	/* setup paging */
	$("div#ccm-group-paging a").click(function() {
		$("#ccm-group-search-wrapper").html("");	
		$.ajax({
			type: "GET",
			url: $(this).attr('href'),
			success: function(resp) {
				//$("#ccm-dialog-throbber").css('visibility','hidden');
				$("#ccm-group-search-wrapper").html(resp);
			}
		});
		return false;
	});
}

ccm_saveArrangement = function(cID) {
	
	if (!cID) {
		cID = CCM_CID;
	}

	ccm_mainNavDisableDirectExit();
	var serial = '';
	$('div.ccm-area').each(function() {
		areaStr = '&area[' + $(this).attr('id').substring(1) + '][]=';
		
		bArray = $(this).sortable('toArray');

		for (i = 0; i < bArray.length; i++ ) {
			if (bArray[i] != '' && bArray[i].substring(0, 1) == 'b') {
				// make sure to only go from b to -, meaning b28-9 becomes "28"
				var bID = bArray[i].substring(1, bArray[i].indexOf('-'));
				var bObj = $('#' + bArray[i]);
				if (bObj.attr('custom-style')) {
					bID += '-' + bObj.attr('custom-style');
				}
				serial += areaStr + bID;
			}
		}
	});

 	$.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial,
 		success: function(msg) {
 			$("div.ccm-area").removeClass('ccm-move-mode');
			$('div.ccm-block-arrange').each(function() {
				$(this).addClass('ccm-block');
				$(this).removeClass('ccm-block-arrange');
			});
			ccm_arrangeMode = false;
			$(".ccm-main-nav-edit-option").fadeIn(300, function() {
				ccm_removeHeaderLoading();
			});
 			ccmAlert.hud(ccmi18n.arrangeBlockMsg, 2000, 'up_down', ccmi18n.arrangeBlock);
 		}});
}

ccm_arrangeInit = function() {
	//$(document.body).append('<img src="' + CCM_IMAGE_PATH + '/topbar_throbber.gif" width="16" height="16" id="ccm-topbar-loader" />');
	
	ccm_arrangeMode = true;
	
	ccm_hideHighlighter();
	
	$('div.ccm-block').each(function() {
		$(this).addClass('ccm-block-arrange');
		$(this).removeClass('ccm-block');
	});
	
	$(".ccm-main-nav-edit-option").fadeOut(300, function() {
		$(".ccm-main-nav-arrange-option").fadeIn(300);
	});
	
	$("div.ccm-area").each(function() {
		$(this).addClass('ccm-move-mode');
		$(this).sortable({
			items: 'div.ccm-block-arrange',
			connectWith: $("div.ccm-area"),
			accept: 'div.ccm-block-arrange',
			opacity: 0.5,
			stop: function() {
				ccm_saveArrangement();
			}
		});
	});
}

if (typeof(ccm_selectSitemapNode) != 'function') {
	ccm_selectSitemapNode = function(cID, cName) {
		alert(cID);
		alert(cName);
	}
}

ccm_goToSitemapNode = function(cID, cName) {
	window.location.href= CCM_DISPATCHER_FILENAME + '?cID=' + cID;
}

ccm_fadeInMenu = function(bobj, e) {
	var mwidth = bobj.find('div.popover div.inner').width();
	var mheight = bobj.find('div.popover').height();
	bobj.hide();
	bobj.css('visibility', 'visible');
	
	var posX = e.pageX + 2;
	var posY = e.pageY + 2;

	if ($(window).height() < e.clientY + mheight) {
		posY = posY - mheight - 10;
		posX = posX - (mwidth / 2);
		bobj.find('div.popover').removeClass('below');
		bobj.find('div.popover').addClass('above');
	} else {
		posX = posX - (mwidth / 2);
		posY = posY + 10;
		bobj.find('div.popover').removeClass('above');
		bobj.find('div.popover').addClass('below');
	}	
	
	bobj.css("top", posY + "px");
	bobj.css("left", posX + "px");
	bobj.fadeIn(60);
	
}

ccm_blockWindowClose = function() {
	jQuery.fn.dialog.closeTop();
	ccm_blockWindowAfterClose();
}

ccm_blockWindowAfterClose = function() {
	ccmValidateBlockForm = function() {return true;}
}

ccm_blockFormSubmit = function() {
	if (typeof window.ccmValidateBlockForm == 'function') {
		r = window.ccmValidateBlockForm();
		if (!r) {
			jQuery.fn.dialog.hideLoader();
		}
		if (ccm_isBlockError) {
			if(ccm_blockError) {
				ccmAlert.notice(ccmi18n.error, ccm_blockError + '</ul>');
			}
			ccm_resetBlockErrors();
			return false;
		}
	}
	return true;
}

ccm_paneToggleOptions = function(obj) {
	var pane = $(obj).parent().find('div.ccm-pane-options-content');
	if ($(obj).hasClass('ccm-icon-option-closed')) {
		$(obj).removeClass('ccm-icon-option-closed').addClass('ccm-icon-option-open');
		pane.slideDown('fast', 'easeOutExpo');
	} else {
		$(obj).removeClass('ccm-icon-option-open').addClass('ccm-icon-option-closed');
		pane.slideUp('fast', 'easeOutExpo');
	}
}



ccm_setupGridStriping = function(tbl) {
	$("#" + tbl + " tr").removeClass();
	var j = 0;
	$("#" + tbl + " tr").each(function() {
		if ($(this).css('display') != 'none') {					
			if (j % 2 == 0) {
				$(this).addClass('ccm-row-alt');
			}
			j++;
		}
	});
}

ccm_dashboardRequestRemoteInformation = function() {
	$.get(CCM_TOOLS_PATH + '/dashboard/get_remote_information');
}

/** 
 * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
 */
ccm_t = function(key) {
	return $("input[name=ccm-string-" + key + "]").val();
}

/* Block Styles Customization Popup */
var ccmCustomStyle = {   
	tabs:function(aLink,tab){
		$('.ccm-styleEditPane').hide();
		$('#ccm-styleEditPane-'+tab).show();
		$(aLink.parentNode.parentNode).find('li').removeClass('ccm-nav-active');
		$(aLink.parentNode).addClass('ccm-nav-active');
		return false;
	},
	resetAll:function(){
		if (!confirm( ccmi18n.confirmCssReset)) {  
			return false;
		}
		jQuery.fn.dialog.showLoader();

		$('#ccm-reset-style').val(1);
		$('#ccmCustomCssForm').get(0).submit();
		return true;
	},
	showPresetDeleteIcon: function() {
		if ($('select[name=cspID]').val() > 0) {
			$("#ccm-style-delete-preset").show();		
		} else {
			$("#ccm-style-delete-preset").hide();
		}	
	},
	deletePreset: function() {
		var cspID = $('select[name=cspID]').val();
		if (cspID > 0) {
			
			if( !confirm(ccmi18n.confirmCssPresetDelete) ) return false;
			
			var action = $('#ccm-custom-style-refresh-action').val() + '&deleteCspID=' + cspID + '&subtask=delete_custom_style_preset';
			jQuery.fn.dialog.showLoader();
			
			$.get(action, function(r) {
				$("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
		}
	},
	initForm: function() {
		if ($("#cspFooterPreset").length > 0) {
			$("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select, #ccmCustomCssFormTabs textarea").bind('change click', function() {
				$("#cspFooterPreset").show();
				$("#cspFooterNoPreset").remove();
				$("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select").unbind('change click');
			});		
		}
		$('input[name=cspPresetAction]').click(function() {
			if ($(this).val() == 'create_new_preset' && $(this).prop('checked')) {
				$('input[name=cspName]').attr('disabled', false).focus();
			} else { 
				$('input[name=cspName]').val('').attr('disabled', true); 
			}
		});
		ccmCustomStyle.showPresetDeleteIcon();
		
		ccmCustomStyle.lastPresetID=parseInt($('select[name=cspID]').val());
		
		$('select[name=cspID]').change(function(){ 
			var cspID = parseInt($(this).val());
			var selectedCsrID = parseInt($('input[name=selectedCsrID]').val());
			
			if(ccmCustomStyle.lastPresetID==cspID) return false;
			ccmCustomStyle.lastPresetID=cspID;
			
			jQuery.fn.dialog.showLoader();
			if (cspID > 0) {
				var action = $('#ccm-custom-style-refresh-action').val() + '&cspID=' + cspID;
			} else {
				var action = $('#ccm-custom-style-refresh-action').val() + '&csrID=' + selectedCsrID;
			}
			
			
			$.get(action, function(r) {
				$("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
			
		});
		
		$('#ccmCustomCssForm').submit(function() {
			if ($('input[name=cspCreateNew]').prop('checked') == true) {
				if ($('input[name=cspName]').val() == '') { 
					$('input[name=cspName]').focus();
					alert(ccmi18n.errorCustomStylePresetNoName);
					return false;
				}
			}

			jQuery.fn.dialog.showLoader();		
			return true;
		});
		
		//IE bug fix 0 can't focus on txt fields if new block just added 
		if(!parseInt(ccmCustomStyle.lastPresetID))  
			setTimeout('$("#ccmCustomCssFormTabs input").attr("disabled", false).get(0).focus()',500);
	},
	validIdCheck:function(el,prevID){
		var selEl = $('#'+el.value); 
		if( selEl && selEl.get(0) && selEl.get(0).id!=prevID ){		
			$('#ccm-styles-invalid-id').css('display','block');
		}else{
			$('#ccm-styles-invalid-id').css('display','none');
		}
	}
};


