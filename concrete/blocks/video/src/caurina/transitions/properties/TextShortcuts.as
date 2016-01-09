package caurina.transitions.properties {

	/**
	 * properties.TextShortcuts
	 * Special properties for the Tweener class to handle MovieClip filters
	 * The function names are strange/inverted because it makes for easier debugging (alphabetic order). They're only for internal use (on this class) anyways.
	 *
	 * @author		Zeh Fernando, Nate Chatellier, Arthur Debert
	 * @version		1.0.0
	 */

	import caurina.transitions.Tweener;
	import caurina.transitions.AuxFunctions;

	import flash.text.TextFormat;

	public class TextShortcuts {

		/**
		 * There's no constructor.
		 */
		public function TextShortcuts () {
			trace ("This is an static class and should not be instantiated.")
		}

		/**
		 * Registers all the special properties to the Tweener class, so the Tweener knows what to do with them.
		 */
		public static function init(): void {
			// Normal properties
			Tweener.registerSpecialProperty("_text", _text_get, _text_set, null, _text_preProcess);
	//		Tweener.registerSpecialPropertyModifier("_text", _text_modifier, _text_get);

			// TextFormat-based properties
			Tweener.registerSpecialPropertySplitter("_text_color",		_generic_color_splitter, ["_text_color_r", "_text_color_g", "_text_color_b"]);
			Tweener.registerSpecialProperty("_text_color_r",			_textFormat_property_get,	_textFormat_property_set,	["color", true, "r"]);
			Tweener.registerSpecialProperty("_text_color_g",			_textFormat_property_get,	_textFormat_property_set,	["color", true, "g"]);
			Tweener.registerSpecialProperty("_text_color_b",			_textFormat_property_get,	_textFormat_property_set,	["color", true, "b"]);
			Tweener.registerSpecialProperty("_text_indent",				_textFormat_property_get,	_textFormat_property_set,	["indent"]);
			Tweener.registerSpecialProperty("_text_leading",			_textFormat_property_get,	_textFormat_property_set,	["leading"]);
			Tweener.registerSpecialProperty("_text_leftMargin",			_textFormat_property_get,	_textFormat_property_set,	["leftMargin"]);
			Tweener.registerSpecialProperty("_text_letterSpacing",		_textFormat_property_get,	_textFormat_property_set,	["letterSpacing"]);
			Tweener.registerSpecialProperty("_text_rightMargin",		_textFormat_property_get,	_textFormat_property_set,	["rightMargin"]);
			Tweener.registerSpecialProperty("_text_size",				_textFormat_property_get,	_textFormat_property_set,	["size"]);

		}


		// ==================================================================================================================================
		// NORMAL SPECIAL PROPERTY functions ------------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// _text

		/**
		 * Returns the current frame number from the movieclip timeline
		 *
		 * @param		p_obj				Object		MovieClip object
		 * @return							Number		The current frame
		 */
		public static function _text_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			//return p_obj._currentFrame;
			return -p_obj.text.length;
		}

		/**
		 * Sets the timeline frame
		 *
		 * @param		p_obj				Object		MovieClip object
		 * @param		p_value				Number		New frame number
		 */
		public static function _text_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			//p_obj.gotoAndStop(Math.round(p_value));
			//p_obj.text =
			if (p_value < 0) {
				// Old text
				p_obj.text = p_extra.oldText.substr(0, -Math.round(p_value));
			} else {
				// New text
				p_obj.text = p_extra.newText.substr(0, Math.round(p_value));
			}
		}

		public static function _text_preProcess (p_obj:Object, p_parameters:Array, p_originalValueComplete:Object, p_extra:Object): Number {
			p_extra.oldText = p_obj.text;
			p_extra.newText = p_originalValueComplete;
			return p_extra.newText.length;
		}

		// ==================================================================================================================================
		// PROPERTY GROUPING/SPLITTING functions --------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// generic splitters

		/**
		 * A generic color splitter - from 0xrrggbb to r, g, b with the name of the parameters passed
		 *
		 * @param		p_value				Number		The original _color value
		 * @return							Array		An array containing the .name and .value of all new properties
		 */
		public static function _generic_color_splitter (p_value:Number, p_parameters:Array):Array {
			var nArray:Array = new Array();
			nArray.push({name:p_parameters[0], value:AuxFunctions.numberToR(p_value)});
			nArray.push({name:p_parameters[1], value:AuxFunctions.numberToG(p_value)});
			nArray.push({name:p_parameters[2], value:AuxFunctions.numberToB(p_value)});
			return nArray;
		}


		// ==================================================================================================================================
		// NORMAL SPECIAL PROPERTY functions ------------------------------------------------------------------------------------------------

		/**
		 * Generic function for the textformat properties
		 */
		public static function _textFormat_property_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			var fmt:TextFormat = p_obj.getTextFormat();
			var propertyName:String = p_parameters[0];
			var isColor:Boolean = p_parameters[1];
			if (!isColor) {
				// Standard property
				return (fmt[propertyName]);
			} else {
				// Composite, color channel
				var colorComponent:String = p_parameters[2];
				if (colorComponent == "r") return AuxFunctions.numberToR(fmt[propertyName]);
				if (colorComponent == "g") return AuxFunctions.numberToG(fmt[propertyName]);
				if (colorComponent == "b") return AuxFunctions.numberToB(fmt[propertyName]);
			}

			return NaN;
		}

		public static function _textFormat_property_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null): void {
			var fmt:TextFormat = p_obj.getTextFormat();
			var propertyName:String = p_parameters[0];
			var isColor:Boolean = p_parameters[1];
			if (!isColor) {
				// Standard property
				fmt[propertyName] = p_value;
			} else {
				// Composite, color channel
				var colorComponent:String = p_parameters[2];
				if (colorComponent == "r") fmt[propertyName] = (fmt[propertyName] & 0xffff) | (p_value << 16);
				if (colorComponent == "g") fmt[propertyName] = (fmt[propertyName] & 0xff00ff) | (p_value << 8);
				if (colorComponent == "b") fmt[propertyName] = (fmt[propertyName] & 0xffff00) | p_value;
			}
			
			p_obj.defaultTextFormat = fmt;
			p_obj.setTextFormat(fmt);

		}

	}

}
