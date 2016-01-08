package caurina.transitions.properties {

	/**
	 * properties.ColorShortcuts
	 * List of default special color properties (normal and splitter properties) for the Tweener class
	 * The function names are strange/inverted because it makes for easier debugging (alphabetic order). They're only for internal use (on this class) anyways.
	 *
	 * @author		Zeh Fernando, Nate Chatellier, Arthur Debert
	 * @version		1.0.0
	 */

	import flash.geom.ColorTransform;
	import flash.filters.ColorMatrixFilter;

	import caurina.transitions.Tweener;
	import caurina.transitions.AuxFunctions;

	public class ColorShortcuts {

		// Sources:
		// http://www.graficaobscura.com/matrix/index.html
		// And mario Klingemann's ColorMatrix class as mentioned on the credits:
		// http://www.quasimondo.com/archives/000565.php
		
		// Defines luminance using sRGB luminance
		private static var LUMINANCE_R:Number = 0.212671;
		private static var LUMINANCE_G:Number = 0.715160;
		private static var LUMINANCE_B:Number = 0.072169;
		
		/**
		 * There's no constructor.
		 */
		public function ColorShortcuts () {
			trace ("This is an static class and should not be instantiated.")
		}

		/**
		 * Registers all the special properties to the Tweener class, so the Tweener knows what to do with them.
		 */
		public static function init(): void {

			// Normal properties
			Tweener.registerSpecialProperty("_color_ra", _oldColor_property_get,	_oldColor_property_set,	["redMultiplier"]);
			Tweener.registerSpecialProperty("_color_rb", _color_property_get,		_color_property_set,	["redOffset"]);
			Tweener.registerSpecialProperty("_color_ga", _oldColor_property_get,	_oldColor_property_set,	["greenMultiplier"]);
			Tweener.registerSpecialProperty("_color_gb", _color_property_get,		_color_property_set,	["greenOffset"]);
			Tweener.registerSpecialProperty("_color_ba", _oldColor_property_get,	_oldColor_property_set,	["blueMultiplier"]);
			Tweener.registerSpecialProperty("_color_bb", _color_property_get,		_color_property_set,	["blueOffset"]);
			Tweener.registerSpecialProperty("_color_aa", _oldColor_property_get,	_oldColor_property_set,	["alphaMultiplier"]);
			Tweener.registerSpecialProperty("_color_ab", _color_property_get,		_color_property_set,	["alphaOffset"]);

			Tweener.registerSpecialProperty("_color_redMultiplier", 	_color_property_get, _color_property_set, ["redMultiplier"]);
			Tweener.registerSpecialProperty("_color_redOffset",			_color_property_get, _color_property_set, ["redOffset"]);
			Tweener.registerSpecialProperty("_color_greenMultiplier",	_color_property_get, _color_property_set, ["greenMultiplier"]);
			Tweener.registerSpecialProperty("_color_greenOffset",		_color_property_get, _color_property_set, ["greenOffset"]);
			Tweener.registerSpecialProperty("_color_blueMultiplier",	_color_property_get, _color_property_set, ["blueMultiplier"]);
			Tweener.registerSpecialProperty("_color_blueOffset",		_color_property_get, _color_property_set, ["blueOffset"]);
			Tweener.registerSpecialProperty("_color_alphaMultiplier",	_color_property_get, _color_property_set, ["alphaMultiplier"]);
			Tweener.registerSpecialProperty("_color_alphaOffset",		_color_property_get, _color_property_set, ["alphaOffset"]);

			// Normal splitter properties
			Tweener.registerSpecialPropertySplitter("_color", _color_splitter);
			Tweener.registerSpecialPropertySplitter("_colorTransform", _colorTransform_splitter);

			// Color changes that depend on the ColorMatrixFilter
			Tweener.registerSpecialProperty("_brightness",		_brightness_get,	_brightness_set, [false]);
			Tweener.registerSpecialProperty("_tintBrightness",	_brightness_get,	_brightness_set, [true]);
			Tweener.registerSpecialProperty("_contrast",		_contrast_get,		_contrast_set);
			Tweener.registerSpecialProperty("_hue",				_hue_get,			_hue_set);
			Tweener.registerSpecialProperty("_saturation",		_saturation_get,	_saturation_set, [false]);
			Tweener.registerSpecialProperty("_dumbSaturation",	_saturation_get,	_saturation_set, [true]);

		}


		// ==================================================================================================================================
		// PROPERTY GROUPING/SPLITTING functions --------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// _color

		/**
		 * Splits the _color parameter into specific color variables
		 *
		 * @param		p_value				Number		The original _color value
		 * @return							Array		An array containing the .name and .value of all new properties
		 */
		public static function _color_splitter (p_value:*, p_parameters:Array):Array {
			var nArray:Array = new Array();
			if (p_value == null) {
				// No parameter passed, so just resets the color
				nArray.push({name:"_color_redMultiplier",	value:1});
				nArray.push({name:"_color_redOffset",		value:0});
				nArray.push({name:"_color_greenMultiplier",	value:1});
				nArray.push({name:"_color_greenOffset",		value:0});
				nArray.push({name:"_color_blueMultiplier",	value:1});
				nArray.push({name:"_color_blueOffset",		value:0});
			} else {
				// A color tinting is passed, so converts it to the object values
				nArray.push({name:"_color_redMultiplier",	value:0});
				nArray.push({name:"_color_redOffset",		value:AuxFunctions.numberToR(p_value)});
				nArray.push({name:"_color_greenMultiplier",	value:0});
				nArray.push({name:"_color_greenOffset",		value:AuxFunctions.numberToG(p_value)});
				nArray.push({name:"_color_blueMultiplier",	value:0});
				nArray.push({name:"_color_blueOffset",		value:AuxFunctions.numberToB(p_value)});
			}
			return nArray;
		}


		// ----------------------------------------------------------------------------------------------------------------------------------
		// _colorTransform

		/**
		 * Splits the _colorTransform parameter into specific color variables
		 *
		 * @param		p_value				Number		The original _colorTransform value
		 * @return							Array		An array containing the .name and .value of all new properties
		 */
		public static function _colorTransform_splitter (p_value:Object, p_parameters:Array):Array {
			var nArray:Array = new Array();
			if (p_value == null) {
				// No parameter passed, so just resets the color
				nArray.push({name:"_color_redMultiplier",	value:1});
				nArray.push({name:"_color_redOffset",		value:0});
				nArray.push({name:"_color_greenMultiplier",	value:1});
				nArray.push({name:"_color_greenOffset",		value:0});
				nArray.push({name:"_color_blueMultiplier",	value:1});
				nArray.push({name:"_color_blueOffset",		value:0});
			} else {
				// A color tinting is passed, so converts it to the object values
				if (p_value.ra != undefined) nArray.push({name:"_color_ra", value:p_value.ra});
				if (p_value.rb != undefined) nArray.push({name:"_color_rb", value:p_value.rb});
				if (p_value.ga != undefined) nArray.push({name:"_color_ba", value:p_value.ba});
				if (p_value.gb != undefined) nArray.push({name:"_color_bb", value:p_value.bb});
				if (p_value.ba != undefined) nArray.push({name:"_color_ga", value:p_value.ga});
				if (p_value.bb != undefined) nArray.push({name:"_color_gb", value:p_value.gb});
				if (p_value.aa != undefined) nArray.push({name:"_color_aa", value:p_value.aa});
				if (p_value.ab != undefined) nArray.push({name:"_color_ab", value:p_value.ab});
				if (p_value.redMultiplier != undefined)		nArray.push({name:"_color_redMultiplier", value:p_value.redMultiplier});
				if (p_value.redOffset != undefined)			nArray.push({name:"_color_redOffset", value:p_value.redOffset});
				if (p_value.blueMultiplier != undefined)	nArray.push({name:"_color_blueMultiplier", value:p_value.blueMultiplier});
				if (p_value.blueOffset != undefined)		nArray.push({name:"_color_blueOffset", value:p_value.blueOffset});
				if (p_value.greenMultiplier != undefined)	nArray.push({name:"_color_greenMultiplier", value:p_value.greenMultiplier});
				if (p_value.greenOffset != undefined)		nArray.push({name:"_color_greenOffset", value:p_value.greenOffset});
				if (p_value.alphaMultiplier != undefined)	nArray.push({name:"_color_alphaMultiplier", value:p_value.alphaMultiplier});
				if (p_value.alphaOffset != undefined)		nArray.push({name:"_color_alphaOffset", value:p_value.alphaOffset});
			}
			return nArray;
		}


		// ==================================================================================================================================
		// NORMAL SPECIAL PROPERTY functions ------------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// _color_*

		/**
		 * _color_*
		 * Generic function for the ra/rb/etc components of the deprecated colorTransform object
		 */
		public static function _oldColor_property_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			return p_obj.transform.colorTransform[p_parameters[0]] * 100;
		}
		public static function _oldColor_property_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			var tf:ColorTransform = p_obj.transform.colorTransform;
			tf[p_parameters[0]] = p_value / 100;
			p_obj.transform.colorTransform = tf;
		}

		/**
		 * _color_*
		 * Generic function for the redMultiplier/redOffset/etc components of the new colorTransform
		 */
		public static function _color_property_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			return p_obj.transform.colorTransform[p_parameters[0]];
		}
		public static function _color_property_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			var cfm:ColorTransform = p_obj.transform.colorTransform;
			cfm[p_parameters[0]] = p_value;
			p_obj.transform.colorTransform = cfm;
		}

		// ----------------------------------------------------------------------------------------------------------------------------------
		// Special coloring

		/**
		 * _brightness
		 * Brightness of an object: -1 -> [0] -> +1
		 */
		public static function _brightness_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {

			var isTint:Boolean = p_parameters[0];

			/*
			// Using ColorMatrix:
			
			var mtx:Array = getObjectMatrix(p_obj);
			
			var mc:Number = 1 - ((mtx[0] + mtx[6] + mtx[12]) / 3); // Brightness as determined by the main channels
			var co:Number = (mtx[4] + mtx[9] + mtx[14]) / 3; // Brightness as determined by the offset channels
			*/

			var cfm:ColorTransform = p_obj.transform.colorTransform;
			var mc:Number = 1 - ((cfm.redMultiplier + cfm.greenMultiplier + cfm.blueMultiplier) / 3); // Brightness as determined by the main channels
			var co:Number = (cfm.redOffset + cfm.greenOffset + cfm.blueOffset) / 3;

			if (isTint) {
				// Tint style
				//return (mc+(co/255))/2;
				return co > 0 ? co / 255 : -mc;
			} else {
				// Native, Flash "Adjust Color" and Photoshop style
				return co / 100;
			}
		}
		public static function _brightness_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			//var mtx:Array = getObjectMatrix(p_obj);

			var isTint:Boolean = p_parameters[0];

			var mc:Number; // Main channel
			var co:Number; // Channel offset

			if (isTint) {
				// Tint style
				mc = 1 - Math.abs(p_value);
				co = p_value > 0 ? Math.round(p_value*255) : 0;
			} else {
				// Native, Flash "Adjust Color" and Photoshop style
				mc = 1;
				co = Math.round(p_value*100);
			}

			/*
			// Using ColorMatrix:
			var mtx:Array = [
				mc, cc, cc, cc, co,
				cc, mc, cc, cc, co,
				cc, cc, mc, cc, co,
				0,  0,  0,  1,  0
			];
			setObjectMatrix(p_obj, mtx);
			*/
			var cfm:ColorTransform = new ColorTransform(mc, mc, mc, 1, co, co, co, 0);
			p_obj.transform.colorTransform = cfm;
		}

		/**
		 * _saturation
		 * Saturation of an object: 0 -> [1] -> 2
		 */
		public static function _saturation_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {

			var mtx:Array = getObjectMatrix(p_obj);

			var isDumb:Boolean = p_parameters[0];
			var rl:Number = isDumb ? 1/3 : LUMINANCE_R;
			var gl:Number = isDumb ? 1/3 : LUMINANCE_G;
			var bl:Number = isDumb ? 1/3 : LUMINANCE_B;

			var mc:Number = ((mtx[0]-rl)/(1-rl) + (mtx[6]-gl)/(1-gl) + (mtx[12]-bl)/(1-bl)) / 3;					// Color saturation as determined by the main channels
			var cc:Number = 1 - ((mtx[1]/gl + mtx[2]/bl + mtx[5]/rl + mtx[7]/bl + mtx[10]/rl + mtx[11]/gl) / 6);	// Color saturation as determined by the other channels
			return (mc + cc) / 2;
		}
		public static function _saturation_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			
			var isDumb:Boolean = p_parameters[0];
			var rl:Number = isDumb ? 1/3 : LUMINANCE_R;
			var gl:Number = isDumb ? 1/3 : LUMINANCE_G;
			var bl:Number = isDumb ? 1/3 : LUMINANCE_B;

			var sf:Number = p_value;
			var nf:Number = 1-sf;
			var nr:Number = rl * nf;
			var ng:Number = gl * nf;
			var nb:Number = bl * nf;

			var mtx:Array = [
				nr+sf,	ng,		nb,		0,	0,
				nr,		ng+sf,	nb,		0,	0,
				nr,		ng,		nb+sf,	0,	0,
				0,  	0, 		0,  	1,  0
			];
			setObjectMatrix(p_obj, mtx);
		}

		/**
		 * _contrast
		 * Contrast of an object: -1 -> [0] -> +1
		 */
		public static function _contrast_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {

			/*
			// Using ColorMatrix:
			var mtx:Array = getObjectMatrix(p_obj);

			var mc:Number = ((mtx[0] + mtx[6] + mtx[12]) / 3) - 1;		// Contrast as determined by the main channels
			var co:Number = (mtx[4] + mtx[9] + mtx[14]) / 3 / -128;		// Contrast as determined by the offset channel
			*/
			var cfm:ColorTransform = p_obj.transform.colorTransform;
			var mc:Number;	// Contrast as determined by the main channels
			var co:Number;	// Contrast as determined by the offset channel
			mc = ((cfm.redMultiplier + cfm.greenMultiplier + cfm.blueMultiplier) / 3) - 1;
			co = (cfm.redOffset + cfm.greenOffset + cfm.blueOffset) / 3 / -128;
			/*
			if (cfm.ra < 100) {
				// Low contrast
				mc = ((cfm.ra + cfm.ga + cfm.ba) / 300) - 1;
				co = (cfm.rb + cfm.gb + cfm.bb) / 3 / -128;
			} else {
				// High contrast
				mc = (((cfm.ra + cfm.ga + cfm.ba) / 300) - 1) / 37;
				co = (cfm.rb + cfm.gb + cfm.bb) / 3 / -3840;
			}
			*/

			return (mc+co)/2;
		}
		public static function _contrast_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			
			var mc:Number;	// Main channel
			var co:Number;	// Channel offset
			mc = p_value + 1;
			co = Math.round(p_value*-128);

			/*
			if (p_value < 0) {
				// Low contrast
				mc = p_value + 1;
				co = Math.round(p_value*-128);
			} else {
				// High contrast
				mc = (p_value * 37) + 1;
				co = Math.round(p_value*-3840);
			}
			*/
			
			// Flash: * 8, * -512

			/*
			// Using ColorMatrix:
			var mtx:Array = [
				mc,	0,	0, 	0, co,
				0,	mc,	0, 	0, co,
				0,	0,	mc,	0, co,
				0,  0, 	0, 	1,  0
			];
			setObjectMatrix(p_obj, mtx);
			*/
			var cfm:ColorTransform = new ColorTransform(mc, mc, mc, 1, co, co, co, 0);
			p_obj.transform.colorTransform = cfm;
		}

		/**
		 * _hue
		 * Hue of an object: -180 -> [0] -> 180
		 */
		public static function _hue_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {

			var mtx:Array = getObjectMatrix(p_obj);

			// Find the current Hue based on a given matrix.
			// This is a kind of a brute force method by sucessive division until a close enough angle is found.
			// Reverse-engineering the hue equation would be is a better choice, but it's hard to find material
			// on the correct calculation employed by Flash.
			// This code has to run only once (before the tween starts), so it's good enough.

			var hues:Array = [];
			var i:Number;

			hues[0] = {angle:-179.9, matrix:getHueMatrix(-179.9)};
			hues[1] = {angle:180, matrix:getHueMatrix(180)};
		
			for (i = 0; i < hues.length; i++) {
				hues[i].distance = getHueDistance(mtx, hues[i].matrix);
			}

			var maxTries:Number = 15;	// Number o maximum divisions until the hue is found
			var angleToSplit:Number;

			for (i = 0; i < maxTries; i++) {
				// Find the nearest angle
				if (hues[0].distance < hues[1].distance) {
					// First is closer
					angleToSplit = 1;
				} else {
					// Second is closer
					angleToSplit = 0;
				}
				hues[angleToSplit].angle = (hues[0].angle + hues[1].angle)/2;
				hues[angleToSplit].matrix = getHueMatrix(hues[angleToSplit].angle)
				hues[angleToSplit].distance = getHueDistance(mtx, hues[angleToSplit].matrix);
			}

			return hues[angleToSplit].angle;
		}

		public static function _hue_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			setObjectMatrix(p_obj, getHueMatrix(p_value));
		}

		public static function getHueDistance (mtx1:Array, mtx2:Array): Number {
			return (Math.abs(mtx1[0] - mtx2[0]) + Math.abs(mtx1[1] - mtx2[1]) + Math.abs(mtx1[2] - mtx2[2]));
		}

		public static function getHueMatrix (hue:Number): Array {
			var ha:Number = hue * Math.PI/180;		// Hue angle, to radians

			var rl:Number = LUMINANCE_R;
			var gl:Number = LUMINANCE_G;
			var bl:Number = LUMINANCE_B;

			var c:Number = Math.cos(ha);
			var s:Number = Math.sin(ha);

			var mtx:Array = [
				(rl + (c * (1 - rl))) + (s * (-rl)),
				(gl + (c * (-gl))) + (s * (-gl)),
				(bl + (c * (-bl))) + (s * (1 - bl)),
				0, 0,

				(rl + (c * (-rl))) + (s * 0.143),
				(gl + (c * (1 - gl))) + (s * 0.14),
				(bl + (c * (-bl))) + (s * -0.283),
				0, 0,

				(rl + (c * (-rl))) + (s * (-(1 - rl))),
				(gl + (c * (-gl))) + (s * gl),
				(bl + (c * (1 - bl))) + (s * bl),
				0, 0,

				0, 0, 0, 1, 0
			];
			
			return mtx;
		}


		// ==================================================================================================================================
		// AUXILIARY functions --------------------------------------------------------------------------------------------------------------

		private static function getObjectMatrix(p_obj:Object): Array {
			// Get the current color matrix of an object
			for (var i:Number = 0; i < p_obj.filters.length; i++) {
				if (p_obj.filters[i] is ColorMatrixFilter) {
					return p_obj.filters[i].matrix.concat();
				}
			}
			return [
				1, 0, 0, 0, 0,
				0, 1, 0, 0, 0,
				0, 0, 1, 0, 0,
				0, 0, 0, 1, 0
			];
		}

		private static function setObjectMatrix(p_obj:Object, p_matrix:Array): void {
			// Set the current color matrix of an object
			var objFilters:Array = p_obj.filters.concat();
			var found:Boolean = false;
			for (var i:Number = 0; i < objFilters.length; i++) {
				if (objFilters[i] is ColorMatrixFilter) {
					objFilters[i].matrix = p_matrix.concat();
					found = true;
				}
			}
			if (!found) {
				// Has to create a new color matrix filter
				var cmtx:ColorMatrixFilter = new ColorMatrixFilter(p_matrix);
				objFilters[objFilters.length] = cmtx;
			}
			p_obj.filters = objFilters;
		}

	}

}
