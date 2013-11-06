var c5 = {};
(function() {
  "use strict";
  c5 = {
    editMode: null,
    event: ccm_event,

    /**
     * Default c5.Object
     */
    Class: function object() {}
  };
}());

c5.Class.prototype = {

  /**
   * Default attribute setter
   * @param  {String}  key   The key
   * @param  {Unknown} value The value
   * @return {Unknown}       The value
   */
  setAttr: function(key, value) {
    this._attr[key] = value;
    return value;
  },

  /**
   * Default attribute getter
   * @param  {String}  key The key to get
   * @return {Unknown}     The value
   */
  getAttr: function(key) {
    return this._attr[key];
  },

  /**
   * Create the getter and setter for a key and add it to `this` if they don't exist
   * @param  {String}  key The key to create getters and setters for
   * @return {Boolean}     Success, always true.
   */
  createGetterSetter: function(key) {
    key += ""; // Make sure we always have a string.
    var get_method = "get" + key.substr(0, 1).toUpperCase() + key.substr(1),
        set_method = "set" + key.substr(0, 1).toUpperCase() + key.substr(1),
        defaults = {};

    defaults[get_method] = _.partial(this.getAttr, key);
    defaults[set_method] = _.partial(this.setAttr, key);
    _(this).defaults(defaults);
    return true;
  },

  /**
   * Loop through `this._attr` and create getters and setters for all
   * @return {Boolean} Success, always true.
   */
  createGetterSetters: function() {
    var my = this;
    _(my._attr).each(function(val, key){
      my.createGetterSetter(key);
      return val;
    });
    return true;
  }
};
