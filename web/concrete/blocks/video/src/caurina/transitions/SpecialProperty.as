package caurina.transitions {
	
	/**
	 * SpecialProperty
	 * A kind of a getter/setter for special properties
	 *
	 * @author		Zeh Fernando
	 * @version		1.0.0
	 * @private
	 */

	public class SpecialProperty {
	
		public var getValue:Function; // (p_obj:Object, p_parameters:Array, p_extra:Object): Number
		public var setValue:Function; // (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object): Void
		public var parameters:Array;
		public var preProcess:Function; // (p_obj:Object, p_parameters:Array, p_originalValueComplete:Object, p_extra:Object): Number

		/**
		 * Builds a new special property object.
		 *
		 * @param		p_getFunction		Function	Reference to the function used to get the special property value
		 * @param		p_setFunction		Function	Reference to the function used to set the special property value
		 */
		public function SpecialProperty (p_getFunction:Function, p_setFunction:Function, p_parameters:Array = null, p_preProcessFunction:Function = null) {
			getValue = p_getFunction;
			setValue = p_setFunction;
			parameters = p_parameters;
			preProcess = p_preProcessFunction;
		}
	
		/**
		 * Converts the instance to a string that can be used when trace()ing the object
		 */
		public function toString():String {
			var value:String = "";
			value += "[SpecialProperty ";
			value += "getValue:"+String(getValue);
			value += ", ";
			value += "setValue:"+String(setValue);
			value += ", ";
			value += "parameters:"+String(parameters);
			value += ", ";
			value += "preProcess:"+String(preProcess);
			value += "]";
			return value;
		}
	}
}
