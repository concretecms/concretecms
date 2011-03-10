package caurina.transitions.properties {

	/**
	 * properties.CurveModifiers
	 * List of default special properties modifiers for the Tweener class
	 * The function names are strange/inverted because it makes for easier debugging (alphabetic order). They're only for internal use (on this class) anyways.
	 *
	 * @author		Zeh Fernando, Nate Chatellier, Arthur Debert
	 * @version		1.0.0
	 */

	import caurina.transitions.Tweener;

	public class CurveModifiers {

		/**
		 * There's no constructor.
		 */
		public function CurveModifiers () {
			trace ("This is an static class and should not be instantiated.")
		}

		/**
		 * Registers all the special properties to the Tweener class, so the Tweener knows what to do with them.
		 */
		public static function init(): void {

			// Bezier modifiers
			Tweener.registerSpecialPropertyModifier("_bezier", _bezier_modifier, _bezier_get);
		}


		// ==================================================================================================================================
		// SPECIAL PROPERTY MODIFIER functions ----------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// _bezier

		/**
		 * Given the parameter object passed to this special property, return an array listing the properties that should be modified, and their parameters
		 *
		 * @param		p_obj				Object		Parameter passed to this property
		 * @return							Array		Array listing name and parameter of each property
		 */
		public static function _bezier_modifier (p_obj:*):Array {
			var mList:Array = []; // List of properties to be modified
			var pList:Array; // List of parameters passed, normalized as an array
			if (p_obj is Array) {
				// Complex
				pList = p_obj;
			} else {
				pList = [p_obj];
			}

			var i:uint;
			var istr:String;
			var mListObj:Object = {}; // Object describing each property name and parameter

			for (i = 0; i < pList.length; i++) {
				for (istr in pList[i]) {
					if (mListObj[istr] == undefined) mListObj[istr] = [];
					mListObj[istr].push(pList[i][istr]);
				}
			}
			for (istr in mListObj) {
				mList.push({name:istr, parameters:mListObj[istr]});
			}
			return mList;
		}

		/**
		 * Given tweening specifications (beging, end, t), applies the property parameter to it, returning new t
		 *
		 * @param		b					Number		Beginning value of the property
		 * @param		e					Number		Ending (desired) value of the property
		 * @param		t					Number		Current t of this tweening (0-1), after applying the easing equation
		 * @param		p					Array		Array of parameters passed to this specific property
		 * @return							Number		New t, with the p parameters applied to it
		 */
		public static function _bezier_get (b:Number, e:Number, t:Number, p:Array):Number {
			// This is based on Robert Penner's code
			if (p.length == 1) {
				// Simple curve with just one bezier control point
				return b + t*(2*(1-t)*(p[0]-b) + t*(e - b));
			} else {
				// Array of bezier control points, must find the point between each pair of bezier points
				var ip:uint = Math.floor(t * p.length); // Position on the bezier list
				var it:Number = (t - (ip * (1 / p.length))) * p.length; // t inside this ip
				var p1:Number, p2:Number;
				if (ip == 0) {
					// First part: belongs to the first control point, find second midpoint
					p1 = b;
					p2 = (p[0]+p[1])/2;
				} else if (ip == p.length - 1) {
					// Last part: belongs to the last control point, find first midpoint
					p1 = (p[ip-1]+p[ip])/2;
					p2 = e;
				} else {
					// Any middle part: find both midpoints
					p1 = (p[ip-1]+p[ip])/2;
					p2 = (p[ip]+p[ip+1])/2;
				}
				return p1+it*(2*(1-it)*(p[ip]-p1) + it*(p2 - p1));
			}
		}

	}

}
