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
