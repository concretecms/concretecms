package caurina.transitions.properties {

	/**
	 * properties.SoundShortcuts
	 * List of default special properties for Sounds
	 * The function names are strange/inverted because it makes for easier debugging (alphabetic order). They're only for internal use (on this class) anyways.
	 *
	 * @author		Zeh Fernando, Nate Chatellier, Arthur Debert
	 * @version		1.0.0
	 */

	import flash.media.SoundTransform;

	import caurina.transitions.Tweener;

	public class SoundShortcuts {

		/**
		 * There's no constructor.
		 */
		public function SoundShortcuts () {
			trace ("This is an static class and should not be instantiated.")
		}

		/**
		 * Registers all the special properties to the Tweener class, so the Tweener knows what to do with them.
		 */
		public static function init():void {

			// Normal properties
			Tweener.registerSpecialProperty("_sound_volume", _sound_volume_get, _sound_volume_set);
			Tweener.registerSpecialProperty("_sound_pan", _sound_pan_get, _sound_pan_set);

		}


		// ==================================================================================================================================
		// NORMAL SPECIAL PROPERTY functions ------------------------------------------------------------------------------------------------

		// ----------------------------------------------------------------------------------------------------------------------------------
		// _sound_volume

		/**
		 * Returns the current sound volume
		 *
		 * @param		p_obj				Object		SoundChannel object
		 * @return							Number		The current volume
		 */
		public static function _sound_volume_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			return p_obj.soundTransform.volume;
		}

		/**
		 * Sets the sound volume
		 *
		 * @param		p_obj				Object		SoundChannel object
		 * @param		p_value				Number		New volume
		 */
		public static function _sound_volume_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null):void {
			var sndTransform:SoundTransform = p_obj.soundTransform;
			sndTransform.volume = p_value;
			p_obj.soundTransform = sndTransform;
		}


		// ----------------------------------------------------------------------------------------------------------------------------------
		// _sound_pan

		/**
		 * Returns the current sound pan
		 *
		 * @param		p_obj				Object		SoundChannel object
		 * @return							Number		The current pan
		 */
		public static function _sound_pan_get (p_obj:Object, p_parameters:Array, p_extra:Object = null):Number {
			return p_obj.soundTransform.pan;
		}

		/**
		 * Sets the sound volume
		 *
		 * @param		p_obj				Object		SoundChannel object
		 * @param		p_value				Number		New pan
		 */
		public static function _sound_pan_set (p_obj:Object, p_value:Number, p_parameters:Array, p_extra:Object = null):void {
			var sndTransform:SoundTransform = p_obj.soundTransform;
			sndTransform.pan = p_value;
			p_obj.soundTransform = sndTransform;
		}

	}

}
