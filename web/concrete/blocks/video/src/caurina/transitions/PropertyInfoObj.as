package caurina.transitions {

	/**
	 * PropertyInfoObj
	 * An object containing the updating info for a given property (its starting value, and its final value)
	 *
	 * @author		Zeh Fernando
	 * @version		1.0.0
	 * @private
	 */

	public class PropertyInfoObj {
		
		public var valueStart				:Number;	// Starting value of the tweening (null if not started yet)
		public var valueComplete			:Number;	// Final desired value
		public var originalValueComplete	:Object;	// Final desired value as declared initially
		public var arrayIndex				:Number;	// Index (if this is an array item)
		public var extra					:Object;	// Additional parameters, used by some special properties
		public var isSpecialProperty		:Boolean;	// Whether or not this is a special property instead of a direct one
		public var hasModifier				:Boolean;	// Whether or not it has a modifier function
		public var modifierFunction		:Function;	// Modifier function, if any
		public var modifierParameters		:Array;		// Additional array of modifier parameters

		// ==================================================================================================================================
		// CONSTRUCTOR function -------------------------------------------------------------------------------------------------------------

		/**
		 * Initializes the basic PropertyInfoObj.
		 *
		 * @param	p_valueStart		Number		Starting value of the tweening (null if not started yet)
		 * @param	p_valueComplete		Number		Final (desired) property value
		 */
		function PropertyInfoObj(p_valueStart:Number, p_valueComplete:Number, p_originalValueComplete:Object, p_arrayIndex:Number, p_extra:Object, p_isSpecialProperty:Boolean, p_modifierFunction:Function, p_modifierParameters:Array) {
			valueStart			=	p_valueStart;
			valueComplete		=	p_valueComplete;
			originalValueComplete	=	p_originalValueComplete;
			arrayIndex				=	p_arrayIndex;
			extra					=	p_extra;
			isSpecialProperty		=	p_isSpecialProperty;
			hasModifier			=	Boolean(p_modifierFunction);
			modifierFunction 	=	p_modifierFunction;
			modifierParameters	=	p_modifierParameters;
		}


		// ==================================================================================================================================
		// OTHER functions ------------------------------------------------------------------------------------------------------------------

		/**
		 * Clones this property info and returns the new PropertyInfoObj
		 *
		 * @param	omitEvents		Boolean			Whether or not events such as onStart (and its parameters) should be omitted
		 * @return 					TweenListObj	A copy of this object
		 */
		public function clone():PropertyInfoObj {
			var nProperty:PropertyInfoObj = new PropertyInfoObj(valueStart, valueComplete, originalValueComplete, arrayIndex, extra, isSpecialProperty, modifierFunction, modifierParameters);
			return nProperty;
		}

		/**
		 * Returns this object described as a String.
		 *
		 * @return 					String		The description of this object.
		 */
		public function toString():String {
			var returnStr:String = "\n[PropertyInfoObj ";
			returnStr += "valueStart:" + String(valueStart);
			returnStr += ", ";
			returnStr += "valueComplete:" + String(valueComplete);
			returnStr += ", ";
			returnStr += "originalValueComplete:" + String(originalValueComplete);
			returnStr += ", ";
			returnStr += "arrayIndex:" + String(arrayIndex);
			returnStr += ", ";
			returnStr += "extra:" + String(extra);
			returnStr += ", ";
			returnStr += "isSpecialProperty:" + String(isSpecialProperty);
			returnStr += ", ";
			returnStr += "hasModifier:" + String(hasModifier);
			returnStr += ", ";
			returnStr += "modifierFunction:" + String(modifierFunction);
			returnStr += ", ";
			returnStr += "modifierParameters:" + String(modifierParameters);
			returnStr += "]\n";
			return returnStr;
		}
		
	}

}
