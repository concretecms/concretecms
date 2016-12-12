/*
 * KineticJS JavaScript Framework v4.7.2
 * http://www.kineticjs.com/
 * Copyright 2013, Eric Rowell
 * Licensed under the MIT or GPL Version 2 licenses.
 * Date: 2013-09-29
 *
 * Copyright (C) 2011 - 2013 by Eric Rowell
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * @namespace Kinetic
 */
var Kinetic = {};
(function() {
    Kinetic = {
        // public
        version: '4.7.2',

        // private
        stages: [],
        idCounter: 0,
        ids: {},
        names: {},
        shapes: {},
        listenClickTap: false,
        inDblClickWindow: false,

        // configurations
        enableTrace: false,
        traceArrMax: 100,
        dblClickWindow: 400,
        pixelRatio: undefined,

        // user agent
        UA: (function() {
            var ua = navigator.userAgent.toLowerCase(),
            // jQuery UA regex
                match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
                    /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
                    /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
                    /(msie) ([\w.]+)/.exec( ua ) ||
                    ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
                    [];

            return {
                browser: match[ 1 ] || '',
                version: match[ 2 ] || '0'
            };
        })(),

        /**
         * @namespace Filters
         * @memberof Kinetic
         */
        Filters: {},

        /**
         * Node constructor. Nodes are entities that can be transformed, layered,
         * and have bound events. The stage, layers, groups, and shapes all extend Node.
         * @constructor
         * @memberof Kinetic
         * @abstract
         * @param {Object} config
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         */
        Node: function(config) {
            this._init(config);
        },

        /**
         * Shape constructor.  Shapes are primitive objects such as rectangles,
         *  circles, text, lines, etc.
         * @constructor
         * @memberof Kinetic
         * @augments Kinetic.Node
         * @param {Object} config
         * @param {String} [config.fill] fill color
         * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
         * @param {Integer} [config.fillR] set fill red component
         * @param {Integer} [config.fillG] set fill green component
         * @param {Integer} [config.fillB] set fill blue component
         * @param {Image} [config.fillPatternImage] fill pattern image
         * @param {Number} [config.fillPatternX]
         * @param {Number} [config.fillPatternY]
         * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillPatternOffsetX]
         * @param {Number} [config.fillPatternOffsetY]
         * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillPatternScaleX]
         * @param {Number} [config.fillPatternScaleY]
         * @param {Number} [config.fillPatternRotation]
         * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
         * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillLinearGradientStartPointX]
         * @param {Number} [config.fillLinearGradientStartPointY]
         * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
         * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillLinearGradientEndPointX]
         * @param {Number} [config.fillLinearGradientEndPointY]
         * @param {Array} [config.fillLinearGradientColorStops] array of color stops
         * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillRadialGradientStartPointX]
         * @param {Number} [config.fillRadialGradientStartPointY]
         * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
         * @param {Number} [config.fillRadialGradientEndPointX]
         * @param {Number} [config.fillRadialGradientEndPointY]
         * @param {Number} [config.fillRadialGradientStartRadius]
         * @param {Number} [config.fillRadialGradientEndRadius]
         * @param {Array} [config.fillRadialGradientColorStops] array of color stops
         * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
         * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
         * @param {String} [config.stroke] stroke color
         * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
         * @param {Integer} [config.strokeR] set stroke red component
         * @param {Integer} [config.strokeG] set stroke green component
         * @param {Integer} [config.strokeB] set stroke blue component
         * @param {Number} [config.strokeWidth] stroke width
         * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
         * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
         * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
         *  is miter
         * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
         *  is butt
         * @param {String} [config.shadowColor]
         * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
         * @param {Integer} [config.shadowColorR] set shadowColor red component
         * @param {Integer} [config.shadowColorG] set shadowColor green component
         * @param {Integer} [config.shadowColorB] set shadowColor blue component
         * @param {Number} [config.shadowBlur]
         * @param {Object} [config.shadowOffset]
         * @param {Number} [config.shadowOffsetX]
         * @param {Number} [config.shadowOffsetY]
         * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
         *  between 0 and 1
         * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
         * @param {Array} [config.dashArray]
         * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         * @example
         * var customShape = new Kinetic.Shape({<br>
         *   x: 5,<br>
         *   y: 10,<br>
         *   fill: 'red',<br>
         *   // a Kinetic.Canvas renderer is passed into the drawFunc function<br>
         *   drawFunc: function(context) {<br>
         *     context.beginPath();<br>
         *     context.moveTo(200, 50);<br>
         *     context.lineTo(420, 80);<br>
         *     context.quadraticCurveTo(300, 100, 260, 170);<br>
         *     context.closePath();<br>
         *     context.fillStrokeShape(this);<br>
         *   }<br>
         *});
         */
        Shape: function(config) {
            this.__init(config);
        },

        /**
         * Container constructor.&nbsp; Containers are used to contain nodes or other containers
         * @constructor
         * @memberof Kinetic
         * @augments Kinetic.Node
         * @abstract
         * @param {Object} config
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         * @param {Function} [config.clipFunc] clipping function

         */
        Container: function(config) {
            this.__init(config);
        },

        /**
         * Stage constructor.  A stage is used to contain multiple layers
         * @constructor
         * @memberof Kinetic
         * @augments Kinetic.Container
         * @param {Object} config
         * @param {String|DomElement} config.container Container id or DOM element
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         * @param {Function} [config.clipFunc] clipping function

         * @example
         * var stage = new Kinetic.Stage({<br>
         *   width: 500,<br>
         *   height: 800,<br>
         *   container: 'containerId'<br>
         * });
         */
        Stage: function(config) {
            this.___init(config);
        },

        /**
         * Layer constructor.  Layers are tied to their own canvas element and are used
         * to contain groups or shapes
         * @constructor
         * @memberof Kinetic
         * @augments Kinetic.Container
         * @param {Object} config
         * @param {Boolean} [config.clearBeforeDraw] set this property to false if you don't want
         * to clear the canvas before each layer draw.  The default value is true.
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         * @param {Function} [config.clipFunc] clipping function

         * @example
         * var layer = new Kinetic.Layer();
         */
        Layer: function(config) {
            this.___init(config);
        },

        /**
         * Group constructor.  Groups are used to contain shapes or other groups.
         * @constructor
         * @memberof Kinetic
         * @augments Kinetic.Container
         * @param {Object} config
         * @param {Number} [config.x]
         * @param {Number} [config.y]
         * @param {Number} [config.width]
         * @param {Number} [config.height]
         * @param {Boolean} [config.visible]
         * @param {Boolean} [config.listening] whether or not the node is listening for events
         * @param {String} [config.id] unique id
         * @param {String} [config.name] non-unique name
         * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
         * @param {Object} [config.scale] set scale
         * @param {Number} [config.scaleX] set scale x
         * @param {Number} [config.scaleY] set scale y
         * @param {Number} [config.rotation] rotation in radians
         * @param {Number} [config.rotationDeg] rotation in degrees
         * @param {Object} [config.offset] offset from center point and rotation point
         * @param {Number} [config.offsetX] set offset x
         * @param {Number} [config.offsetY] set offset y
         * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
         *  the entire stage by dragging any portion of the stage
         * @param {Function} [config.dragBoundFunc]
         * @param {Function} [config.clipFunc] clipping function

         * @example
         * var group = new Kinetic.Group();
         */
        Group: function(config) {
            this.___init(config);
        },

        /**
         * returns whether or not drag and drop is currently active
         * @method
         * @memberof Kinetic
         */
        isDragging: function() {
            var dd = Kinetic.DD;

            // if DD is not included with the build, then
            // drag and drop is not even possible
            if (!dd) {
                return false;
            }
            // if DD is included with the build
            else {
                return dd.isDragging;
            }
        },
        /**
         * returns whether or not a drag and drop operation is ready, but may
         *  not necessarily have started
         * @method
         * @memberof Kinetic
         */
        isDragReady: function() {
            var dd = Kinetic.DD;

            // if DD is not included with the build, then
            // drag and drop is not even possible
            if (!dd) {
                return false;
            }
            // if DD is included with the build
            else {
                return !!dd.node;
            }
        },
        _addId: function(node, id) {
            if(id !== undefined) {
                this.ids[id] = node;
            }
        },
        _removeId: function(id) {
            if(id !== undefined) {
                delete this.ids[id];
            }
        },
        _addName: function(node, name) {
            if(name !== undefined) {
                if(this.names[name] === undefined) {
                    this.names[name] = [];
                }
                this.names[name].push(node);
            }
        },
        _removeName: function(name, _id) {
            if(name !== undefined) {
                var nodes = this.names[name];
                if(nodes !== undefined) {
                    for(var n = 0; n < nodes.length; n++) {
                        var no = nodes[n];
                        if(no._id === _id) {
                            nodes.splice(n, 1);
                        }
                    }
                    if(nodes.length === 0) {
                        delete this.names[name];
                    }
                }
            }
        }
    };
})();

// Uses Node, AMD or browser globals to create a module.

// If you want something that will work in other stricter CommonJS environments,
// or if you need to create a circular dependency, see commonJsStrict.js

// Defines a module "returnExports" that depends another module called "b".
// Note that the name of the module is implied by the file name. It is best
// if the file name and the exported global have matching names.

// If the 'b' module also uses this type of boilerplate, then
// in the browser, it will create a global .b that is used below.

// If you do not want to support the browser global path, then you
// can remove the `root` use and the passing `this` as the first arg to
// the top function.

// if the module has no dependencies, the above pattern can be simplified to
( function(root, factory) {
    if( typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like enviroments that support module.exports,
        // like Node.
        module.exports = factory();
    }
    else if( typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(factory);
    }
    else {
        // Browser globals (root is window)
        root.returnExports = factory();
    }
}(this, function() {

    // Just return a value to define the module export.
    // This example returns an object, but the module
    // can return a function as the exported value.
    return Kinetic;
}));
;(function() {
    /**
     * Collection constructor.  Collection extends
     *  Array.  This class is used in conjunction with {@link Kinetic.Container#get}
     * @constructor
     * @memberof Kinetic
     */
    Kinetic.Collection = function() {
        var args = [].slice.call(arguments), length = args.length, i = 0;

        this.length = length;
        for(; i < length; i++) {
            this[i] = args[i];
        }
        return this;
    };
    Kinetic.Collection.prototype = [];
    /**
     * iterate through node array and run a function for each node.
     *  The node and index is passed into the function
     * @method
     * @memberof Kinetic.Collection.prototype
     * @param {Function} func
     * @example
     * // get all nodes with name foo inside layer, and set x to 10 for each
     * layer.get('.foo').each(function(shape, n) {<br>
     *   shape.setX(10);<br>
     * });
     */
    Kinetic.Collection.prototype.each = function(func) {
        for(var n = 0; n < this.length; n++) {
            func(this[n], n);
        }
    };
    /**
     * convert collection into an array
     * @method
     * @memberof Kinetic.Collection.prototype
     */
    Kinetic.Collection.prototype.toArray = function() {
        var arr = [],
            len = this.length,
            n;

        for(n = 0; n < len; n++) {
            arr.push(this[n]);
        }
        return arr;
    };
    /**
     * convert array into a collection
     * @method
     * @memberof Kinetic.Collection
     * @param {Array} arr
     */
    Kinetic.Collection.toCollection = function(arr) {
        var collection = new Kinetic.Collection(),
            len = arr.length,
            n;

        for(n = 0; n < len; n++) {
            collection.push(arr[n]);
        }
        return collection;
    };

    Kinetic.Collection.mapMethods = function(arr) {
        var leng = arr.length,
            n;

        for(n = 0; n < leng; n++) {
            // induce scope
            (function(i) {
                var method = arr[i];
                Kinetic.Collection.prototype[method] = function() {
                    var len = this.length,
                        i;

                    args = [].slice.call(arguments);
                    for(i = 0; i < len; i++) {
                        this[i][method].apply(this[i], args);
                    }
                };
            })(n);
        }
    };

    /*
     * Last updated November 2011
     * By Simon Sarris
     * www.simonsarris.com
     * sarris@acm.org
     *
     * Free to use and distribute at will
     * So long as you are nice to people, etc
     */

    /*
     * The usage of this class was inspired by some of the work done by a forked
     * project, KineticJS-Ext by Wappworks, which is based on Simon's Transform
     * class.  Modified by Eric Rowell
     */

    /**
     * Transform constructor
     * @constructor
     * @memberof Kinetic
     */
    Kinetic.Transform = function() {
        this.m = [1, 0, 0, 1, 0, 0];
    };

    Kinetic.Transform.prototype = {
        /**
         * Apply translation
         * @method
         * @memberof Kinetic.Transform.prototype
         * @param {Number} x
         * @param {Number} y
         */
        translate: function(x, y) {
            this.m[4] += this.m[0] * x + this.m[2] * y;
            this.m[5] += this.m[1] * x + this.m[3] * y;
        },
        /**
         * Apply scale
         * @method
         * @memberof Kinetic.Transform.prototype
         * @param {Number} sx
         * @param {Number} sy
         */
        scale: function(sx, sy) {
            this.m[0] *= sx;
            this.m[1] *= sx;
            this.m[2] *= sy;
            this.m[3] *= sy;
        },
        /**
         * Apply rotation
         * @method
         * @memberof Kinetic.Transform.prototype
         * @param {Number} rad  Angle in radians
         */
        rotate: function(rad) {
            var c = Math.cos(rad);
            var s = Math.sin(rad);
            var m11 = this.m[0] * c + this.m[2] * s;
            var m12 = this.m[1] * c + this.m[3] * s;
            var m21 = this.m[0] * -s + this.m[2] * c;
            var m22 = this.m[1] * -s + this.m[3] * c;
            this.m[0] = m11;
            this.m[1] = m12;
            this.m[2] = m21;
            this.m[3] = m22;
        },
        /**
         * Returns the translation
         * @method
         * @memberof Kinetic.Transform.prototype
         * @returns {Object} 2D point(x, y)
         */
        getTranslation: function() {
            return {
                x: this.m[4],
                y: this.m[5]
            };
        },
        /**
         * Apply skew
         * @method
         * @memberof Kinetic.Transform.prototype
         * @param {Number} sx
         * @param {Number} sy
         */
        skew: function(sx, sy) {
            var m11 = this.m[0] + this.m[2] * sy;
            var m12 = this.m[1] + this.m[3] * sy;
            var m21 = this.m[2] + this.m[0] * sx;
            var m22 = this.m[3] + this.m[1] * sx;
            this.m[0] = m11;
            this.m[1] = m12;
            this.m[2] = m21;
            this.m[3] = m22;
        },
        /**
         * Transform multiplication
         * @method
         * @memberof Kinetic.Transform.prototype
         * @param {Kinetic.Transform} matrix
         */
        multiply: function(matrix) {
            var m11 = this.m[0] * matrix.m[0] + this.m[2] * matrix.m[1];
            var m12 = this.m[1] * matrix.m[0] + this.m[3] * matrix.m[1];

            var m21 = this.m[0] * matrix.m[2] + this.m[2] * matrix.m[3];
            var m22 = this.m[1] * matrix.m[2] + this.m[3] * matrix.m[3];

            var dx = this.m[0] * matrix.m[4] + this.m[2] * matrix.m[5] + this.m[4];
            var dy = this.m[1] * matrix.m[4] + this.m[3] * matrix.m[5] + this.m[5];

            this.m[0] = m11;
            this.m[1] = m12;
            this.m[2] = m21;
            this.m[3] = m22;
            this.m[4] = dx;
            this.m[5] = dy;
        },
        /**
         * Invert the matrix
         * @method
         * @memberof Kinetic.Transform.prototype
         */
        invert: function() {
            var d = 1 / (this.m[0] * this.m[3] - this.m[1] * this.m[2]);
            var m0 = this.m[3] * d;
            var m1 = -this.m[1] * d;
            var m2 = -this.m[2] * d;
            var m3 = this.m[0] * d;
            var m4 = d * (this.m[2] * this.m[5] - this.m[3] * this.m[4]);
            var m5 = d * (this.m[1] * this.m[4] - this.m[0] * this.m[5]);
            this.m[0] = m0;
            this.m[1] = m1;
            this.m[2] = m2;
            this.m[3] = m3;
            this.m[4] = m4;
            this.m[5] = m5;
        },
        /**
         * return matrix
         * @method
         * @memberof Kinetic.Transform.prototype
         */
        getMatrix: function() {
            return this.m;
        },
        /**
         * set to absolute position via translation
         * @method
         * @memberof Kinetic.Transform.prototype
         * @author ericdrowell
         */
        setAbsolutePosition: function(x, y) {
            var m0 = this.m[0],
                m1 = this.m[1],
                m2 = this.m[2],
                m3 = this.m[3],
                m4 = this.m[4],
                m5 = this.m[5],
                yt = ((m0 * (y - m5)) - (m1 * (x - m4))) / ((m0 * m3) - (m1 * m2)),
                xt = (x - m4 - (m2 * yt)) / m0;

            this.translate(xt, yt);
        }
    };

    // CONSTANTS
    var CANVAS = 'canvas',
        CONTEXT_2D = '2d',
        OBJECT_ARRAY = '[object Array]',
        OBJECT_NUMBER = '[object Number]',
        OBJECT_STRING = '[object String]',
        PI_OVER_DEG180 = Math.PI / 180,
        DEG180_OVER_PI = 180 / Math.PI,
        HASH = '#',
        EMPTY_STRING = '',
        ZERO = '0',
        KINETIC_WARNING = 'Kinetic warning: ',
        KINETIC_ERROR = 'Kinetic error: ',
        RGB_PAREN = 'rgb(',
        COLORS = {
            aqua: [0,255,255],
            lime: [0,255,0],
            silver: [192,192,192],
            black: [0,0,0],
            maroon: [128,0,0],
            teal: [0,128,128],
            blue: [0,0,255],
            navy: [0,0,128],
            white: [255,255,255],
            fuchsia: [255,0,255],
            olive:[128,128,0],
            yellow: [255,255,0],
            orange: [255,165,0],
            gray: [128,128,128],
            purple: [128,0,128],
            green: [0,128,0],
            red: [255,0,0],
            pink: [255,192,203],
            cyan: [0,255,255],
            transparent: [255,255,255,0]
        },

        RGB_REGEX = /rgb\((\d{1,3}),(\d{1,3}),(\d{1,3})\)/;

    /**
     * @namespace Util
     * @memberof Kinetic
     */
    Kinetic.Util = {
        /*
         * cherry-picked utilities from underscore.js
         */
        _isElement: function(obj) {
            return !!(obj && obj.nodeType == 1);
        },
        _isFunction: function(obj) {
            return !!(obj && obj.constructor && obj.call && obj.apply);
        },
        _isObject: function(obj) {
            return (!!obj && obj.constructor == Object);
        },
        _isArray: function(obj) {
            return Object.prototype.toString.call(obj) == OBJECT_ARRAY;
        },
        _isNumber: function(obj) {
            return Object.prototype.toString.call(obj) == OBJECT_NUMBER;
        },
        _isString: function(obj) {
            return Object.prototype.toString.call(obj) == OBJECT_STRING;
        },
        /*
         * other utils
         */
        _hasMethods: function(obj) {
            var names = [],
                key;

            for(key in obj) {
                if(this._isFunction(obj[key])) {
                    names.push(key);
                }
            }
            return names.length > 0;
        },
        _isInDocument: function(el) {
            while(el = el.parentNode) {
                if(el == document) {
                    return true;
                }
            }
            return false;
        },
        _simplifyArray: function(arr) {
            var retArr = [],
                len = arr.length,
                util = Kinetic.Util,
                n, val;

            for (n=0; n<len; n++) {
                val = arr[n];
                if (util._isNumber(val)) {
                    val = Math.round(val * 1000) / 1000;
                }
                else if (!util._isString(val)) {
                    val = val.toString();
                }

                retArr.push(val);
            }

            return retArr;
        },
        /*
         * The argument can be:
         * - an integer (will be applied to both x and y)
         * - an array of one integer (will be applied to both x and y)
         * - an array of two integers (contains x and y)
         * - an array of four integers (contains x, y, width, and height)
         * - an object with x and y properties
         * - an array of one element which is an array of integers
         * - an array of one element of an object
         */
        _getXY: function(arg) {
            if(this._isNumber(arg)) {
                return {
                    x: arg,
                    y: arg
                };
            }
            else if(this._isArray(arg)) {
                // if arg is an array of one element
                if(arg.length === 1) {
                    var val = arg[0];
                    // if arg is an array of one element which is a number
                    if(this._isNumber(val)) {
                        return {
                            x: val,
                            y: val
                        };
                    }
                    // if arg is an array of one element which is an array
                    else if(this._isArray(val)) {
                        return {
                            x: val[0],
                            y: val[1]
                        };
                    }
                    // if arg is an array of one element which is an object
                    else if(this._isObject(val)) {
                        return val;
                    }
                }
                // if arg is an array of two or more elements
                else if(arg.length >= 2) {
                    return {
                        x: arg[0],
                        y: arg[1]
                    };
                }
            }
            // if arg is an object return the object
            else if(this._isObject(arg)) {
                return arg;
            }

            // default
            return null;
        },
        /*
         * The argument can be:
         * - an integer (will be applied to both width and height)
         * - an array of one integer (will be applied to both width and height)
         * - an array of two integers (contains width and height)
         * - an array of four integers (contains x, y, width, and height)
         * - an object with width and height properties
         * - an array of one element which is an array of integers
         * - an array of one element of an object
         */
        _getSize: function(arg) {
            if(this._isNumber(arg)) {
                return {
                    width: arg,
                    height: arg
                };
            }
            else if(this._isArray(arg)) {
                // if arg is an array of one element
                if(arg.length === 1) {
                    var val = arg[0];
                    // if arg is an array of one element which is a number
                    if(this._isNumber(val)) {
                        return {
                            width: val,
                            height: val
                        };
                    }
                    // if arg is an array of one element which is an array
                    else if(this._isArray(val)) {
                        /*
                         * if arg is an array of one element which is an
                         * array of four elements
                         */
                        if(val.length >= 4) {
                            return {
                                width: val[2],
                                height: val[3]
                            };
                        }
                        /*
                         * if arg is an array of one element which is an
                         * array of two elements
                         */
                        else if(val.length >= 2) {
                            return {
                                width: val[0],
                                height: val[1]
                            };
                        }
                    }
                    // if arg is an array of one element which is an object
                    else if(this._isObject(val)) {
                        return val;
                    }
                }
                // if arg is an array of four elements
                else if(arg.length >= 4) {
                    return {
                        width: arg[2],
                        height: arg[3]
                    };
                }
                // if arg is an array of two elements
                else if(arg.length >= 2) {
                    return {
                        width: arg[0],
                        height: arg[1]
                    };
                }
            }
            // if arg is an object return the object
            else if(this._isObject(arg)) {
                return arg;
            }

            // default
            return null;
        },
        /*
         * arg will be an array of numbers or
         *  an array of point arrays or
         *  an array of point objects
         */
        _getPoints: function(arg) {
            var arr = [],
                n, len;

            if(arg === undefined) {
                return [];
            }

            len = arg.length;

            // an array of arrays
            if(this._isArray(arg[0])) {
                /*
                 * convert array of arrays into an array
                 * of objects containing x, y
                 */
                for(n = 0; n < len; n++) {
                    arr.push({
                        x: arg[n][0],
                        y: arg[n][1]
                    });
                }

                return arr;
            }
            // an array of objects
            if(this._isObject(arg[0])) {
                return arg;
            }
            // an array of integers
            else {
                /*
                 * convert array of numbers into an array
                 * of objects containing x, y
                 */
                for(n = 0; n < len; n += 2) {
                    arr.push({
                        x: arg[n],
                        y: arg[n + 1]
                    });
                }

                return arr;
            }
        },
        /*
         * arg can be an image object or image data
         */
        _getImage: function(arg, callback) {
            var imageObj, canvas, context, dataUrl;

            // if arg is null or undefined
            if(!arg) {
                callback(null);
            }

            // if arg is already an image object
            else if(this._isElement(arg)) {
                callback(arg);
            }

            // if arg is a string, then it's a data url
            else if(this._isString(arg)) {
                imageObj = new Image();
                imageObj.onload = function() {
                    callback(imageObj);
                };
                imageObj.src = arg;
            }

            //if arg is an object that contains the data property, it's an image object
            else if(arg.data) {
                canvas = document.createElement(CANVAS);
                canvas.width = arg.width;
                canvas.height = arg.height;
                _context = canvas.getContext(CONTEXT_2D);
                _context.putImageData(arg, 0, 0);
                dataUrl = canvas.toDataURL();
                imageObj = new Image();
                imageObj.onload = function() {
                    callback(imageObj);
                };
                imageObj.src = dataUrl;
            }
            else {
                callback(null);
            }
        },
        _rgbToHex: function(r, g, b) {
            return ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        },
        _hexToRgb: function(hex) {
            hex = hex.replace(HASH, EMPTY_STRING);
            var bigint = parseInt(hex, 16);
            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255
            };
        },
        /**
         * return random hex color
         * @method
         * @memberof Kinetic.Util.prototype
         */
        getRandomColor: function() {
            var randColor = (Math.random() * 0xFFFFFF << 0).toString(16);
            while (randColor.length < 6) {
                randColor = ZERO + randColor;
            }
            return HASH + randColor;
        },
        /**
         * return value with default fallback
         * @method
         * @memberof Kinetic.Util.prototype
         */
        get: function(val, def) {
            if (val === undefined) {
                return def;
            }
            else {
                return val;
            }
        },
        /**
         * get RGB components of a color
         * @method
         * @memberof Kinetic.Util.prototype
         * @param {String} color
         * @example
         * // each of the following examples return {r:0, g:0, b:255}<br>
         * var rgb = Kinetic.Util.getRGB('blue');<br>
         * var rgb = Kinetic.Util.getRGB('#0000ff');<br>
         * var rgb = Kinetic.Util.getRGB('rgb(0,0,255)');
         */
        getRGB: function(color) {
            var rgb;
            // color string
            if (color in COLORS) {
                rgb = COLORS[color];
                return {
                    r: rgb[0],
                    g: rgb[1],
                    b: rgb[2]
                };
            }
            // hex
            else if (color[0] === HASH) {
                return this._hexToRgb(color.substring(1));
            }
            // rgb string
            else if (color.substr(0, 4) === RGB_PAREN) {
                rgb = RGB_REGEX.exec(color.replace(/ /g,''));
                return {
                    r: parseInt(rgb[1], 10),
                    g: parseInt(rgb[2], 10),
                    b: parseInt(rgb[3], 10)
                };
            }
            // default
            else {
                return {
                    r: 0,
                    g: 0,
                    b: 0
                };
            }
        },
        // o1 takes precedence over o2
        _merge: function(o1, o2) {
            var retObj = this._clone(o2);
            for(var key in o1) {
                if(this._isObject(o1[key])) {
                    retObj[key] = this._merge(o1[key], retObj[key]);
                }
                else {
                    retObj[key] = o1[key];
                }
            }
            return retObj;
        },
        // deep object clone
        _clone: function(obj) {
            var retObj = {};
            for(var key in obj) {
                if(this._isObject(obj[key])) {
                    retObj[key] = this._clone(obj[key]);
                }
                else {
                    retObj[key] = obj[key];
                }
            }
            return retObj;
        },
        _degToRad: function(deg) {
            return deg * PI_OVER_DEG180;
        },
        _radToDeg: function(rad) {
            return rad * DEG180_OVER_PI;
        },
        _capitalize: function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        error: function(str) {
            throw new Error(KINETIC_ERROR + str);
        },
        warn: function(str) {
            /*
             * IE9 on Windows7 64bit will throw a JS error
             * if we don't use window.console in the conditional
             */
            if(window.console && console.warn) {
                console.warn(KINETIC_WARNING + str);
            }
        },
        extend: function(c1, c2) {
            for(var key in c2.prototype) {
                if(!( key in c1.prototype)) {
                    c1.prototype[key] = c2.prototype[key];
                }
            }
        },
        /**
         * adds methods to a constructor prototype
         * @method
         * @memberof Kinetic.Util.prototype
         * @param {Function} constructor
         * @param {Object} methods
         */
        addMethods: function(constructor, methods) {
            var key;

            for (key in methods) {
                constructor.prototype[key] = methods[key];
            }
        },
        _getControlPoints: function(p0, p1, p2, t) {
            var x0 = p0.x;
            var y0 = p0.y;
            var x1 = p1.x;
            var y1 = p1.y;
            var x2 = p2.x;
            var y2 = p2.y;
            var d01 = Math.sqrt(Math.pow(x1 - x0, 2) + Math.pow(y1 - y0, 2));
            var d12 = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
            var fa = t * d01 / (d01 + d12);
            var fb = t * d12 / (d01 + d12);
            var p1x = x1 - fa * (x2 - x0);
            var p1y = y1 - fa * (y2 - y0);
            var p2x = x1 + fb * (x2 - x0);
            var p2y = y1 + fb * (y2 - y0);
            return [{
                x: p1x,
                y: p1y
            }, {
                x: p2x,
                y: p2y
            }];
        },
        _expandPoints: function(points, tension) {
            var length = points.length,
                allPoints = [],
                n, cp;

            for(n = 1; n < length - 1; n++) {
                cp = Kinetic.Util._getControlPoints(points[n - 1], points[n], points[n + 1], tension);
                allPoints.push(cp[0]);
                allPoints.push(points[n]);
                allPoints.push(cp[1]);
            }

            return allPoints;
        },
        _removeLastLetter: function(str) {
            return str.substring(0, str.length - 1);
        }
    };
})();
;(function() {
    // calculate pixel ratio
    var canvas = document.createElement('canvas'),
        context = canvas.getContext('2d'),
        devicePixelRatio = window.devicePixelRatio || 1,
        backingStoreRatio = context.webkitBackingStorePixelRatio
            || context.mozBackingStorePixelRatio
            || context.msBackingStorePixelRatio
            || context.oBackingStorePixelRatio
            || context.backingStorePixelRatio
            || 1,
        _pixelRatio = devicePixelRatio / backingStoreRatio;

    /**
     * Canvas Renderer constructor
     * @constructor
     * @abstract
     * @memberof Kinetic
     * @param {Number} width
     * @param {Number} height
     * @param {Number} pixelRatio KineticJS automatically handles pixel ratio adustments in order to render crisp drawings
     *  on all devices. Most desktops, low end tablets, and low end phones, have device pixel ratios
     *  of 1.  Some high end tablets and phones, like iPhones and iPads (not the mini) have a device pixel ratio
     *  of 2.  Some Macbook Pros, and iMacs also have a device pixel ratio of 2.  Some high end Android devices have pixel
     *  ratios of 2 or 3.  Some browsers like Firefox allow you to configure the pixel ratio of the viewport.  Unless otherwise
     *  specificed, the pixel ratio will be defaulted to the actual device pixel ratio.  You can override the device pixel
     *  ratio for special situations, or, if you don't want the pixel ratio to be taken into account, you can set it to 1.
     */
    Kinetic.Canvas = function(config) {
        this.init(config);
    };

    Kinetic.Canvas.prototype = {
        init: function(config) {
            config = config || {};

            var pixelRatio = config.pixelRatio || Kinetic.pixelRatio || _pixelRatio;

            this.pixelRatio = pixelRatio;
            this._canvas = document.createElement('canvas');

            // set inline styles
            this._canvas.style.padding = 0;
            this._canvas.style.margin = 0;
            this._canvas.style.border = 0;
            this._canvas.style.background = 'transparent';
            this._canvas.style.position = 'absolute';
            this._canvas.style.top = 0;
            this._canvas.style.left = 0;
        },
        /**
         * get canvas context
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @returns {CanvasContext} context
         */
        getContext: function() {
            return this.context;
        },
        /**
         * get pixel ratio
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @returns {Number} pixel ratio
         */
        getPixelRatio: function() {
            return this.pixelRatio;
        },
        /**
         * get pixel ratio
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @param {Number} pixelRatio KineticJS automatically handles pixel ratio adustments in order to render crisp drawings
         *  on all devices. Most desktops, low end tablets, and low end phones, have device pixel ratios
         *  of 1.  Some high end tablets and phones, like iPhones and iPads (not the mini) have a device pixel ratio
         *  of 2.  Some Macbook Pros, and iMacs also have a device pixel ratio of 2.  Some high end Android devices have pixel
         *  ratios of 2 or 3.  Some browsers like Firefox allow you to configure the pixel ratio of the viewport.  Unless otherwise
         *  specificed, the pixel ratio will be defaulted to the actual device pixel ratio.  You can override the device pixel
         *  ratio for special situations, or, if you don't want the pixel ratio to be taken into account, you can set it to 1.
         */
        setPixelRatio: function(pixelRatio) {
            this.pixelRatio = pixelRatio;
            this.setSize(this.getWidth(), this.getHeight());
        },
        /**
         * set width
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @param {Number} width
         */
        setWidth: function(width) {
            // take into account pixel ratio
            this.width = this._canvas.width = width * this.pixelRatio;
            this._canvas.style.width = width + 'px';
        },
        /**
         * set height
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @param {Number} height
         */
        setHeight: function(height) {
            // take into account pixel ratio
            this.height = this._canvas.height = height * this.pixelRatio;
            this._canvas.style.height = height + 'px';
        },
        /**
         * get width
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @returns {Number} width
         */
        getWidth: function() {
            return this.width;
        },
        /**
         * get height
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @returns {Number} height
         */
        getHeight: function() {
            return this.height;
        },
        /**
         * set size
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @param {Number} width
         * @param {Number} height
         */
        setSize: function(width, height) {
            this.setWidth(width);
            this.setHeight(height);
        },
        /**
         * to data url
         * @method
         * @memberof Kinetic.Canvas.prototype
         * @param {String} mimeType
         * @param {Number} quality between 0 and 1 for jpg mime types
         * @returns {String} data url string
         */
        toDataURL: function(mimeType, quality) {
            try {
                // If this call fails (due to browser bug, like in Firefox 3.6),
                // then revert to previous no-parameter image/png behavior
                return this._canvas.toDataURL(mimeType, quality);
            }
            catch(e) {
                try {
                    return this._canvas.toDataURL();
                }
                catch(err) {
                    Kinetic.Util.warn('Unable to get data URL. ' + err.message);
                    return '';
                }
            }
        }
    };

    Kinetic.SceneCanvas = function(config) {
        config = config || {};
        var width = config.width || 0,
            height = config.height || 0;

        Kinetic.Canvas.call(this, config);
        this.context = new Kinetic.SceneContext(this);
        this.setSize(width, height);
    };

    Kinetic.SceneCanvas.prototype = {
        setWidth: function(width) {
            var pixelRatio = this.pixelRatio,
                _context = this.getContext()._context;

            Kinetic.Canvas.prototype.setWidth.call(this, width);
            _context.scale(pixelRatio, pixelRatio);
        },
        setHeight: function(height) {
            var pixelRatio = this.pixelRatio,
                _context = this.getContext()._context;

            Kinetic.Canvas.prototype.setHeight.call(this, height);
            _context.scale(pixelRatio, pixelRatio);
        }
    };
    Kinetic.Util.extend(Kinetic.SceneCanvas, Kinetic.Canvas);

    Kinetic.HitCanvas = function(config) {
        config = config || {};
        var width = config.width || 0,
            height = config.height || 0;

        Kinetic.Canvas.call(this, config);
        this.context = new Kinetic.HitContext(this);
        this.setSize(width, height);
    };
    Kinetic.Util.extend(Kinetic.HitCanvas, Kinetic.Canvas);

})();
;(function() {
    var COMMA = ',',
        OPEN_PAREN = '(',
        CLOSE_PAREN = ')',
        OPEN_PAREN_BRACKET = '([',
        CLOSE_BRACKET_PAREN = '])',
        SEMICOLON = ';',
        DOUBLE_PAREN = '()',
        EMPTY_STRING = '',
        EQUALS = '=',
        SET = 'set',
        CONTEXT_METHODS = [
            'arc',
            'arcTo',
            'beginPath',
            'bezierCurveTo',
            'clearRect',
            'clip',
            'closePath',
            'createLinearGradient',
            'createPattern',
            'createRadialGradient',
            'drawImage',
            'fill',
            'fillText',
            'getImageData',
            'lineTo',
            'moveTo',
            'putImageData',
            'quadraticCurveTo',
            'rect',
            'restore',
            'rotate',
            'save',
            'scale',
            'setLineDash',
            'setTransform',
            'stroke',
            'strokeText',
            'transform',
            'translate'
        ];

    /**
     * Canvas Context constructor
     * @constructor
     * @abstract
     * @memberof Kinetic
     */
    Kinetic.Context = function(canvas) {
        this.init(canvas);
    };

    Kinetic.Context.prototype = {
        init: function(canvas) {
            this.canvas = canvas;
            this._context = canvas._canvas.getContext('2d');

            if (Kinetic.enableTrace) {
                this.traceArr = [];
                this._enableTrace();
            }
        },
        /**
         * fill shape
         * @method
         * @memberof Kinetic.Context.prototype
         * @param {Kinetic.Shape} shape
         */
        fillShape: function(shape) {
            if(shape.getFillEnabled()) {
                this._fill(shape);
            }
        },
        /**
         * stroke shape
         * @method
         * @memberof Kinetic.Context.prototype
         * @param {Kinetic.Shape} shape
         */
        strokeShape: function(shape) {
            if(shape.getStrokeEnabled()) {
                this._stroke(shape);
            }
        },
        /**
         * fill then stroke
         * @method
         * @memberof Kinetic.Context.prototype
         * @param {Kinetic.Shape} shape
         */
        fillStrokeShape: function(shape) {
            var fillEnabled = shape.getFillEnabled();
            if(fillEnabled) {
                this._fill(shape);
            }
            if(shape.getStrokeEnabled()) {
                this._stroke(shape);
            }
        },
        /**
         * get context trace if trace is enabled
         * @method
         * @memberof Kinetic.Context.prototype
         * @param {Boolean} relaxed if false, return strict context trace, which includes method names, method parameters
         *  properties, and property values.  If true, return relaxed context trace, which only returns method names and
         *  properites.
         * @returns {String}
         */
        getTrace: function(relaxed) {
            var traceArr = this.traceArr,
                len = traceArr.length,
                str = '',
                n, trace, method, args;

            for (n=0; n<len; n++) {
                trace = traceArr[n];
                method = trace.method;

                // methods
                if (method) {
                    args = trace.args;
                    str += method;
                    if (relaxed) {
                        str += DOUBLE_PAREN;
                    }
                    else {
                        if (Kinetic.Util._isArray(args[0])) {
                            str += OPEN_PAREN_BRACKET + args.join(COMMA) + CLOSE_BRACKET_PAREN;
                        }
                        else {
                            str += OPEN_PAREN + args.join(COMMA) + CLOSE_PAREN;
                        }
                    }
                }
                // properties
                else {
                    str += trace.property;
                    if (!relaxed) {
                        str += EQUALS + trace.val;
                    }
                }

                str += SEMICOLON;
            }

            return str;
        },
        /**
         * clear trace if trace is enabled
         * @method
         * @memberof Kinetic.Context.prototype
         */
        clearTrace: function() {
            this.traceArr = [];
        },
        _trace: function(str) {
            var traceArr = this.traceArr,
                len;

            traceArr.push(str);
            len = traceArr.length;

            if (len >= Kinetic.traceArrMax) {
                traceArr.shift();
            }
        },
        /**
         * reset canvas context transform
         * @method
         * @memberof Kinetic.Context.prototype
         */
        reset: function() {
            var pixelRatio = this.getCanvas().getPixelRatio();
            this.setTransform(1 * pixelRatio, 0, 0, 1 * pixelRatio, 0, 0);
        },
        /**
         * get canvas
         * @method
         * @memberof Kinetic.Context.prototype
         * @returns {Kinetic.Canvas}
         */
        getCanvas: function() {
            return this.canvas;
        },
        /**
         * clear canvas
         * @method
         * @memberof Kinetic.Context.prototype
         */
        clear: function() {
            var args = [].slice.call(arguments),
                canvas = this.getCanvas(),
                pos, size;

            if (args.length) {
                pos = Kinetic.Util._getXY(args);
                size = Kinetic.Util._getSize(args);
                this.clearRect(pos.x || 0, pos.y || 0, size.width, size.height);
            }
            else {
                this.clearRect(0, 0, canvas.getWidth(), canvas.getHeight());
            }
        },
        _applyLineCap: function(shape) {
            var lineCap = shape.getLineCap();
            if(lineCap) {
                this.setAttr('lineCap', lineCap);
            }
        },
        _applyOpacity: function(shape) {
            var absOpacity = shape.getAbsoluteOpacity();
            if(absOpacity !== 1) {
                this.setAttr('globalAlpha', absOpacity);
            }
        },
        _applyLineJoin: function(shape) {
            var lineJoin = shape.getLineJoin();
            if(lineJoin) {
                this.setAttr('lineJoin', lineJoin);
            }
        },
        _applyAncestorTransforms: function(shape) {
            var m = shape.getAbsoluteTransform().getMatrix();
            this.transform(m[0], m[1], m[2], m[3], m[4], m[5]);
        },
        _clip: function(container) {
            var clipX = container.getClipX() || 0,
                clipY = container.getClipY() || 0,
                clipWidth = container.getClipWidth(),
                clipHeight = container.getClipHeight();

            this.save();
            this._applyAncestorTransforms(container);
            this.beginPath();
            this.rect(clipX, clipY, clipWidth, clipHeight);
            this.clip();
            this.reset();
            container._drawChildren(this.getCanvas());
            this.restore();
        },

        setAttr: function(attr, val) {
            this._context[attr] = val;
        },

        // context pass through methods
        arc: function() {
            var a = arguments;
            this._context.arc(a[0], a[1], a[2], a[3], a[4], a[5]);
        },
        beginPath: function() {
            this._context.beginPath();
        },
        bezierCurveTo: function() {
            var a = arguments;
            this._context.bezierCurveTo(a[0], a[1], a[2], a[3], a[4], a[5]);
        },
        clearRect: function() {
            var a = arguments;
            this._context.clearRect(a[0], a[1], a[2], a[3]);
        },
        clip: function() {
            this._context.clip();
        },
        closePath: function() {
            this._context.closePath();
        },
        createLinearGradient: function() {
            var a = arguments;
            return this._context.createLinearGradient(a[0], a[1], a[2], a[3]);
        },
        createPattern: function() {
            var a = arguments;
            return this._context.createPattern(a[0], a[1]);
        },
        createRadialGradient: function() {
            var a = arguments;
            return this._context.createRadialGradient(a[0], a[1], a[2], a[3], a[4], a[5]);
        },
        drawImage: function() {
            var a = arguments,
                _context = this._context;

            if(a.length === 3) {
                _context.drawImage(a[0], a[1], a[2]);
            }
            else if(a.length === 5) {
                _context.drawImage(a[0], a[1], a[2], a[3], a[4]);
            }
            else if(a.length === 9) {
                _context.drawImage(a[0], a[1], a[2], a[3], a[4], a[5], a[6], a[7], a[8]);
            }
        },
        fill: function() {
            this._context.fill();
        },
        fillText: function() {
            var a = arguments;
            this._context.fillText(a[0], a[1], a[2]);
        },
        getImageData: function() {
            var a = arguments;
            return this._context.getImageData(a[0], a[1], a[2], a[3]);
        },
        lineTo: function() {
            var a = arguments;
            this._context.lineTo(a[0], a[1]);
        },
        moveTo: function() {
            var a = arguments;
            this._context.moveTo(a[0], a[1]);
        },
        rect: function() {
            var a = arguments;
            this._context.rect(a[0], a[1], a[2], a[3]);
        },
        putImageData: function() {
            var a = arguments;
            this._context.putImageData(a[0], a[1], a[2]);
        },
        quadraticCurveTo: function() {
            var a = arguments;
            this._context.quadraticCurveTo(a[0], a[1], a[2], a[3]);
        },
        restore: function() {
            this._context.restore();
        },
        rotate: function() {
            var a = arguments;
            this._context.rotate(a[0]);
        },
        save: function() {
            this._context.save();
        },
        scale: function() {
            var a = arguments;
            this._context.scale(a[0], a[1]);
        },
        setLineDash: function() {
            var a = arguments,
                _context = this._context;

            // works for Chrome and IE11
            if(this._context.setLineDash) {
                _context.setLineDash(a[0]);
            }
            // verified that this works in firefox
            else if('mozDash' in _context) {
                _context.mozDash = a[0];
            }
            // does not currently work for Safari
            else if('webkitLineDash' in _context) {
                _context.webkitLineDash = a[0];
            }

            // no support for IE9 and IE10
        },
        setTransform: function() {
            var a = arguments;
            this._context.setTransform(a[0], a[1], a[2], a[3], a[4], a[5]);
        },
        stroke: function() {
            this._context.stroke();
        },
        strokeText: function() {
            var a = arguments;
            this._context.strokeText(a[0], a[1], a[2]);
        },
        transform: function() {
            var a = arguments;
            this._context.transform(a[0], a[1], a[2], a[3], a[4], a[5]);
        },
        translate: function() {
            var a = arguments;
            this._context.translate(a[0], a[1]);
        },
        _enableTrace: function() {
            var that = this,
                len = CONTEXT_METHODS.length,
                _simplifyArray = Kinetic.Util._simplifyArray,
                origSetter = this.setAttr,
                n, args;

            // methods
            for (n=0; n<len; n++) {
                (function(methodName) {
                    var origMethod = that[methodName],
                        ret;

                    that[methodName] = function() {
                        args = _simplifyArray(Array.prototype.slice.call(arguments, 0));
                        ret = origMethod.apply(that, arguments);

                        that._trace({
                            method: methodName,
                            args: args
                        });

                        return ret;
                    };
                })(CONTEXT_METHODS[n]);
            }

            // attrs
            that.setAttr = function() {
                origSetter.apply(that, arguments);
                that._trace({
                    property: arguments[0],
                    val: arguments[1]
                });
            };
        }
    };

    Kinetic.SceneContext = function(canvas) {
        Kinetic.Context.call(this, canvas);
    };

    Kinetic.SceneContext.prototype = {
        _fillColor: function(shape) {
            var fill = shape.getFill();

            this.setAttr('fillStyle', fill);
            shape._fillFunc(this);
        },
        _fillPattern: function(shape) {
            var fillPatternImage = shape.getFillPatternImage(),
                fillPatternX = shape.getFillPatternX(),
                fillPatternY = shape.getFillPatternY(),
                fillPatternScale = shape.getFillPatternScale(),
                fillPatternRotation = shape.getFillPatternRotation(),
                fillPatternOffset = shape.getFillPatternOffset(),
                fillPatternRepeat = shape.getFillPatternRepeat();

            if(fillPatternX || fillPatternY) {
                this.translate(fillPatternX || 0, fillPatternY || 0);
            }
            if(fillPatternRotation) {
                this.rotate(fillPatternRotation);
            }
            if(fillPatternScale) {
                this.scale(fillPatternScale.x, fillPatternScale.y);
            }
            if(fillPatternOffset) {
                this.translate(-1 * fillPatternOffset.x, -1 * fillPatternOffset.y);
            }

            this.setAttr('fillStyle', this.createPattern(fillPatternImage, fillPatternRepeat || 'repeat'));
            this.fill();
        },
        _fillLinearGradient: function(shape) {
            var start = shape.getFillLinearGradientStartPoint(),
                end = shape.getFillLinearGradientEndPoint(),
                colorStops = shape.getFillLinearGradientColorStops(),
                grd = this.createLinearGradient(start.x, start.y, end.x, end.y);

            if (colorStops) {
                // build color stops
                for(var n = 0; n < colorStops.length; n += 2) {
                    grd.addColorStop(colorStops[n], colorStops[n + 1]);
                }
                this.setAttr('fillStyle', grd);
                this.fill();
            }
        },
        _fillRadialGradient: function(shape) {
            var start = shape.getFillRadialGradientStartPoint(),
                end = shape.getFillRadialGradientEndPoint(),
                startRadius = shape.getFillRadialGradientStartRadius(),
                endRadius = shape.getFillRadialGradientEndRadius(),
                colorStops = shape.getFillRadialGradientColorStops(),
                grd = this.createRadialGradient(start.x, start.y, startRadius, end.x, end.y, endRadius);

            // build color stops
            for(var n = 0; n < colorStops.length; n += 2) {
                grd.addColorStop(colorStops[n], colorStops[n + 1]);
            }
            this.setAttr('fillStyle', grd);
            this.fill();
        },
        _fill: function(shape) {
            var hasColor = shape.getFill(),
                hasPattern = shape.getFillPatternImage(),
                hasLinearGradient = shape.getFillLinearGradientColorStops(),
                hasRadialGradient = shape.getFillRadialGradientColorStops(),
                fillPriority = shape.getFillPriority();

            // priority fills
            if(hasColor && fillPriority === 'color') {
                this._fillColor(shape);
            }
            else if(hasPattern && fillPriority === 'pattern') {
                this._fillPattern(shape);
            }
            else if(hasLinearGradient && fillPriority === 'linear-gradient') {
                this._fillLinearGradient(shape);
            }
            else if(hasRadialGradient && fillPriority === 'radial-gradient') {
                this._fillRadialGradient(shape);
            }
            // now just try and fill with whatever is available
            else if(hasColor) {
                this._fillColor(shape);
            }
            else if(hasPattern) {
                this._fillPattern(shape);
            }
            else if(hasLinearGradient) {
                this._fillLinearGradient(shape);
            }
            else if(hasRadialGradient) {
                this._fillRadialGradient(shape);
            }
        },
        _stroke: function(shape) {
            var stroke = shape.getStroke(),
                strokeWidth = shape.getStrokeWidth(),
                dashArray = shape.getDashArray(),
                strokeScaleEnabled = shape.getStrokeScaleEnabled();

            if(shape.hasStroke()) {
                if (!strokeScaleEnabled) {
                    this.save();
                    this.setTransform(1, 0, 0, 1, 0, 0);
                }

                /////////////////////
                this._applyLineCap(shape);
                if(dashArray && shape.getDashArrayEnabled()) {
                    this.setLineDash(dashArray);
                }

                this.setAttr('lineWidth', strokeWidth || 2);
                this.setAttr('strokeStyle', stroke || 'black');
                shape._strokeFunc(this);

                if (!strokeScaleEnabled) {
                    this.restore();
                }
            }
        },
        _applyShadow: function(shape) {
            var util = Kinetic.Util,
                absOpacity = shape.getAbsoluteOpacity(),
                color = util.get(shape.getShadowColor(), 'black'),
                blur = util.get(shape.getShadowBlur(), 5),
                shadowOpacity = util.get(shape.getShadowOpacity(), 0),
                offset = util.get(shape.getShadowOffset(), {
                    x: 0,
                    y: 0
                });

            if(shadowOpacity) {
                this.setAttr('globalAlpha', shadowOpacity * absOpacity);
            }

            this.setAttr('shadowColor', color);
            this.setAttr('shadowBlur', blur);
            this.setAttr('shadowOffsetX', offset.x);
            this.setAttr('shadowOffsetY', offset.y);

        }
    };
    Kinetic.Util.extend(Kinetic.SceneContext, Kinetic.Context);

    Kinetic.HitContext = function(canvas) {
        Kinetic.Context.call(this, canvas);
    };

    Kinetic.HitContext.prototype = {
        _fill: function(shape) {
            this.save();
            this.setAttr('fillStyle', shape.colorKey);
            shape._fillFuncHit(this);
            this.restore();
        },
        _stroke: function(shape) {
            var stroke = shape.getStroke(),
                strokeWidth = shape.getStrokeWidth();

            if(stroke || strokeWidth) {
                this._applyLineCap(shape);
                this.setAttr('lineWidth', strokeWidth || 2);
                this.setAttr('strokeStyle', shape.colorKey);
                shape._strokeFuncHit(this);
            }
        }
    };
    Kinetic.Util.extend(Kinetic.HitContext, Kinetic.Context);
})();
; (function() {
    // CONSTANTS
    var ABSOLUTE_OPACITY = 'absoluteOpacity',
        ABSOLUTE_TRANSFORM = 'absoluteTransform',
        ADD = 'add',
        B = 'b',
        BEFORE = 'before',
        BLACK = 'black',
        CHANGE = 'Change',
        CHILDREN = 'children',
        DEG = 'Deg',
        DOT = '.',
        EMPTY_STRING = '',
        G = 'g',
        GET = 'get',
        HASH = '#',
        ID = 'id',
        KINETIC = 'kinetic',
        LISTENING = 'listening',
        MOUSEENTER = 'mouseenter',
        MOUSELEAVE = 'mouseleave',
        NAME = 'name',
        OFF = 'off',
        ON = 'on',
        PRIVATE_GET = '_get',
        R = 'r',
        RGB = 'RGB',
        SET = 'set',
        SHAPE = 'Shape',
        SPACE = ' ',
        STAGE = 'Stage',
        TRANSFORM = 'transform',
        UPPER_B = 'B',
        UPPER_G = 'G',
        UPPER_HEIGHT = 'Height',
        UPPER_R = 'R',
        UPPER_WIDTH = 'Width',
        UPPER_X = 'X',
        UPPER_Y = 'Y',
        VISIBLE = 'visible',
        X = 'x',
        Y = 'y';

    Kinetic.Factory = {
        // getter setter adders
        addGetterSetter: function(constructor, attr, def) {
            this.addGetter(constructor, attr, def);
            this.addSetter(constructor, attr);
        },
        addPointGetterSetter: function(constructor, attr, def) {
            this.addPointGetter(constructor, attr, def);
            this.addPointSetter(constructor, attr);

            // add invdividual component getters and setters
            this.addGetter(constructor, attr + UPPER_X, def);
            this.addGetter(constructor, attr + UPPER_Y, def);
            this.addSetter(constructor, attr + UPPER_X);
            this.addSetter(constructor, attr + UPPER_Y);
        },
        addBoxGetterSetter: function(constructor, attr, def) {
            this.addBoxGetter(constructor, attr, def);
            this.addBoxSetter(constructor, attr);

            // add invdividual component getters and setters
            this.addGetter(constructor, attr + UPPER_X, def);
            this.addGetter(constructor, attr + UPPER_Y, def);
            this.addGetter(constructor, attr + UPPER_WIDTH, def);
            this.addGetter(constructor, attr + UPPER_HEIGHT, def);

            this.addSetter(constructor, attr + UPPER_X);
            this.addSetter(constructor, attr + UPPER_Y);
            this.addSetter(constructor, attr + UPPER_WIDTH);
            this.addSetter(constructor, attr + UPPER_HEIGHT);
        },
        addPointsGetterSetter: function(constructor, attr) {
            this.addPointsGetter(constructor, attr);
            this.addPointsSetter(constructor, attr);
            this.addPointAdder(constructor, attr);
        },
        addRotationGetterSetter: function(constructor, attr, def) {
            this.addRotationGetter(constructor, attr, def);
            this.addRotationSetter(constructor, attr);
        },
        addColorGetterSetter: function(constructor, attr) {
            this.addGetter(constructor, attr);
            this.addSetter(constructor, attr);

            // component getters
            this.addColorRGBGetter(constructor, attr);
            this.addColorComponentGetter(constructor, attr, R);
            this.addColorComponentGetter(constructor, attr, G);
            this.addColorComponentGetter(constructor, attr, B);

            // component setters
            this.addColorRGBSetter(constructor, attr);
            this.addColorComponentSetter(constructor, attr, R);
            this.addColorComponentSetter(constructor, attr, G);
            this.addColorComponentSetter(constructor, attr, B);
        },

        // getter adders
        addColorRGBGetter: function(constructor, attr) {
            var method = GET + Kinetic.Util._capitalize(attr) + RGB;
            constructor.prototype[method] = function() {
                return Kinetic.Util.getRGB(this.attrs[attr]);
            };
        },

        addColorComponentGetter: function(constructor, attr, c) {
            var prefix = GET + Kinetic.Util._capitalize(attr),
                method = prefix + Kinetic.Util._capitalize(c);
            constructor.prototype[method] = function() {
                return this[prefix + RGB]()[c];
            };
        },
        addPointsGetter: function(constructor, attr) {
            var that = this,
                method = GET + Kinetic.Util._capitalize(attr);

            constructor.prototype[method] = function() {
                var val = this.attrs[attr];
                return val === undefined ? [] : val;
            };
        },
        addGetter: function(constructor, attr, def) {
            var that = this,
                method = GET + Kinetic.Util._capitalize(attr);

            constructor.prototype[method] = function() {
                var val = this.attrs[attr];
                return val === undefined ? def : val;
            };
        },
        addPointGetter: function(constructor, attr) {
            var that = this,
                baseMethod = GET + Kinetic.Util._capitalize(attr);

            constructor.prototype[baseMethod] = function() {
                var that = this;
                return {
                    x: that[baseMethod + UPPER_X](),
                    y: that[baseMethod + UPPER_Y]()
                };
            };
        },
        addBoxGetter: function(constructor, attr) {
            var that = this,
                baseMethod = GET + Kinetic.Util._capitalize(attr);

            constructor.prototype[baseMethod] = function() {
                var that = this;
                return {
                    x: that[baseMethod + UPPER_X](),
                    y: that[baseMethod + UPPER_Y](),
                    width: that[baseMethod + UPPER_WIDTH](),
                    height: that[baseMethod + UPPER_HEIGHT]()
                };
            };
        },
        addRotationGetter: function(constructor, attr, def) {
            var that = this,
                method = GET + Kinetic.Util._capitalize(attr);

            // radians
            constructor.prototype[method] = function() {
                var val = this.attrs[attr];
                if (val === undefined) {
                    val = def;
                }
                return val;
            };
            // degrees
            constructor.prototype[method + DEG] = function() {
                var val = this.attrs[attr];
                if (val === undefined) {
                    val = def;
                }
                return Kinetic.Util._radToDeg(val);
            };
        },

        // setter adders
        addColorRGBSetter: function(constructor, attr) {
            var method = SET + Kinetic.Util._capitalize(attr) + RGB;

            constructor.prototype[method] = function(obj) {
                var r = obj && obj.r !== undefined ? obj.r | 0 : this.getAttr(attr + UPPER_R),
                    g = obj && obj.g !== undefined ? obj.g | 0 : this.getAttr(attr + UPPER_G),
                    b = obj && obj.b !== undefined ? obj.b | 0 : this.getAttr(attr + UPPER_B);

                this._setAttr(attr, HASH + Kinetic.Util._rgbToHex(r, g, b));
            };
        },

        addColorComponentSetter: function(constructor, attr, c) {
            var prefix = SET + Kinetic.Util._capitalize(attr),
                method = prefix + Kinetic.Util._capitalize(c);
            constructor.prototype[method] = function(val) {
                var obj = {};
                obj[c] = val;
                this[prefix + RGB](obj);
            };
        },
        addPointsSetter: function(constructor, attr) {
            var method = SET + Kinetic.Util._capitalize(attr);
            constructor.prototype[method] = function(val) {
                var points = Kinetic.Util._getPoints(val);
                this._setAttr('points', points);
            };
        },
        addSetter: function(constructor, attr) {
            var method = SET + Kinetic.Util._capitalize(attr);

            constructor.prototype[method] = function(val) {
                this._setAttr(attr, val);
            };
        },
        addPointSetter: function(constructor, attr) {
            var that = this,
                baseMethod = SET + Kinetic.Util._capitalize(attr);

            constructor.prototype[baseMethod] = function() {
                var pos = Kinetic.Util._getXY([].slice.call(arguments)),
                    oldVal = this.attrs[attr],
                    x = 0,
                    y = 0;

                if (pos) {
                    x = pos.x;
                    y = pos.y;

                    this._fireBeforeChangeEvent(attr, oldVal, pos);
                    if (x !== undefined) {
                        this[baseMethod + UPPER_X](x);
                    }
                    if (y !== undefined) {
                        this[baseMethod + UPPER_Y](y);
                    }
                    this._fireChangeEvent(attr, oldVal, pos);
                }
            };
        },
        addBoxSetter: function(constructor, attr) {
            var that = this,
                baseMethod = SET + Kinetic.Util._capitalize(attr);

            constructor.prototype[baseMethod] = function() {
                var config = [].slice.call(arguments),
                    pos = Kinetic.Util._getXY(config),
                    size = Kinetic.Util._getSize(config),
                    both = Kinetic.Util._merge(pos, size),
                    oldVal = this.attrs[attr],
                    x, y, width, height;

                if (both) {
                    x = both.x;
                    y = both.y;
                    width = both.width;
                    height = both.height;

                    this._fireBeforeChangeEvent(attr, oldVal, both);
                    if (x !== undefined) {
                        this[baseMethod + UPPER_X](x);
                    }
                    if (y !== undefined) {
                        this[baseMethod + UPPER_Y](y);
                    }
                    if (width !== undefined) {
                        this[baseMethod + UPPER_WIDTH](width);
                    }
                    if (height !== undefined) {
                        this[baseMethod + UPPER_HEIGHT](height);
                    }
                    this._fireChangeEvent(attr, oldVal, both);
                }
            };
        },
        addRotationSetter: function(constructor, attr) {
            var that = this,
                method = SET + Kinetic.Util._capitalize(attr);

            // radians
            constructor.prototype[method] = function(val) {
                this._setAttr(attr, val);
            };
            // degrees
            constructor.prototype[method + DEG] = function(deg) {
                this._setAttr(attr, Kinetic.Util._degToRad(deg));
            };
        },

        // add adders
        addPointAdder: function(constructor, attr) {
            var that = this,
                baseMethod = ADD + Kinetic.Util._removeLastLetter(Kinetic.Util._capitalize(attr));

            constructor.prototype[baseMethod] = function() {
                var pos = Kinetic.Util._getXY([].slice.call(arguments)),
                    oldVal = this.attrs[attr];

                if (pos) {
                    this._fireBeforeChangeEvent(attr, oldVal, pos);
                    this.attrs[attr].push(pos);
                    this._fireChangeEvent(attr, oldVal, pos);
                }
            };
        }
    };
})();;(function() {
    // CONSTANTS
    var ABSOLUTE_OPACITY = 'absoluteOpacity',
        ABSOLUTE_TRANSFORM = 'absoluteTransform',
        ADD = 'add',
        B = 'b',
        BEFORE = 'before',
        BLACK = 'black',
        CHANGE = 'Change',
        CHILDREN = 'children',
        DEG = 'Deg',
        DOT = '.',
        EMPTY_STRING = '',
        G = 'g',
        GET = 'get',
        HASH = '#',
        ID = 'id',
        KINETIC = 'kinetic',
        LISTENING = 'listening',
        MOUSEENTER = 'mouseenter',
        MOUSELEAVE = 'mouseleave',
        NAME = 'name',
        OFF = 'off',
        ON = 'on',
        PRIVATE_GET = '_get',
        R = 'r',
        RGB = 'RGB',
        SET = 'set',
        SHAPE = 'Shape',
        SPACE = ' ',
        STAGE = 'stage',
        TRANSFORM = 'transform',
        UPPER_B = 'B',
        UPPER_G = 'G',
        UPPER_HEIGHT = 'Height',
        UPPER_R = 'R',
        UPPER_STAGE = 'Stage',
        UPPER_WIDTH = 'Width',
        UPPER_X = 'X',
        UPPER_Y = 'Y',
        VISIBLE = 'visible',
        X = 'x',
        Y = 'y',

        transformChangeStr = [
            'xChange.kinetic',
            'yChange.kinetic',
            'scaleXChange.kinetic',
            'scaleYChange.kinetic',
            'skewXChange.kinetic',
            'skewYChange.kinetic',
            'rotationChange.kinetic',
            'offsetXChange.kinetic',
            'offsetYChange.kinetic'
        ].join(SPACE);

    Kinetic.Util.addMethods(Kinetic.Node, {
        _init: function(config) {
            var that = this;
            this._id = Kinetic.idCounter++;
            this.eventListeners = {};
            this.attrs = {};
            this.cache = {};
            this.setAttrs(config);

            // event bindings for cache handling
            this.on(transformChangeStr, function() {
                this._clearCache(TRANSFORM);
                that._clearSelfAndChildrenCache(ABSOLUTE_TRANSFORM);
            });
            this.on('visibleChange.kinetic', function() {
                that._clearSelfAndChildrenCache(VISIBLE);
            });
            this.on('listeningChange.kinetic', function() {
                that._clearSelfAndChildrenCache(LISTENING);
            });
            this.on('opacityChange.kinetic', function() {
                that._clearSelfAndChildrenCache(ABSOLUTE_OPACITY);
            });
        },
        /**
         * clear cached variables
         * @method
         * @memberof Kinetic.Node.prototype
         * @example
         * node.clearCache();
         */
        clearCache: function() {
            this.cache = {};
        },
        _clearCache: function(attr){
            delete this.cache[attr];
        },
        _getCache: function(attr, privateGetter){
            var cache = this.cache[attr];



            // if not cached, we need to set it using the private getter method.
            if (cache === undefined) {
                this.cache[attr] = privateGetter.call(this);
            }

            return this.cache[attr];
        },
        _clearSelfAndChildrenCache: function(attr) {
            var that = this;
            this._clearCache(attr);

            if (this.children) {
                this.getChildren().each(function(node) {
                    node._clearSelfAndChildrenCache(attr);
                });
            }
        },
        /**
         * bind events to the node. KineticJS supports mouseover, mousemove,
         *  mouseout, mouseenter, mouseleave, mousedown, mouseup, click, dblclick, touchstart, touchmove,
         *  touchend, tap, dbltap, dragstart, dragmove, and dragend events. Pass in a string
         *  of events delimmited by a space to bind multiple events at once
         *  such as 'mousedown mouseup mousemove'. Include a namespace to bind an
         *  event by name such as 'click.foobar'.
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} evtStr e.g. 'click', 'mousedown touchstart', 'mousedown.foo touchstart.foo'
         * @param {Function} handler The handler function is passed an event object
         * @example
         * // add click listener<br>
         * node.on('click', function() {<br>
         *   console.log('you clicked me!');<br>
         * });<br><br>
         *
         * // get the target node<br>
         * node.on('click', function(evt) {<br>
         *   console.log(evt.targetNode);<br>
         * });<br><br>
         *
         * // stop event propagation<br>
         * node.on('click', function(evt) {<br>
         *   evt.cancelBubble = true;<br>
         * });<br><br>
         *
         * // bind multiple listeners<br>
         * node.on('click touchstart', function() {<br>
         *   console.log('you clicked/touched me!');<br>
         * });<br><br>
         *
         * // namespace listener<br>
         * node.on('click.foo', function() {<br>
         *   console.log('you clicked/touched me!');<br>
         * });
         */
        on: function(evtStr, handler) {
            var events = evtStr.split(SPACE),
                len = events.length,
                n, event, parts, baseEvent, name;

            /*
             * loop through types and attach event listeners to
             * each one.  eg. 'click mouseover.namespace mouseout'
             * will create three event bindings
             */
            for(n = 0; n < len; n++) {
                event = events[n];
                parts = event.split(DOT);
                baseEvent = parts[0];
                name = parts[1] || EMPTY_STRING;

                // create events array if it doesn't exist
                if(!this.eventListeners[baseEvent]) {
                    this.eventListeners[baseEvent] = [];
                }

                this.eventListeners[baseEvent].push({
                    name: name,
                    handler: handler
                });
            }
            return this;
        },
        /**
         * remove event bindings from the node. Pass in a string of
         *  event types delimmited by a space to remove multiple event
         *  bindings at once such as 'mousedown mouseup mousemove'.
         *  include a namespace to remove an event binding by name
         *  such as 'click.foobar'. If you only give a name like '.foobar',
         *  all events in that namespace will be removed.
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} evtStr e.g. 'click', 'mousedown touchstart', '.foobar'
         * @example
         * // remove listener<br>
         * node.off('click');<br><br>
         *
         * // remove multiple listeners<br>
         * node.off('click touchstart');<br><br>
         *
         * // remove listener by name<br>
         * node.off('click.foo');
         */
        off: function(evtStr) {
            var events = evtStr.split(SPACE),
                len = events.length,
                n, i, t, event, parts, baseEvent, name;

            for(n = 0; n < len; n++) {
                event = events[n];
                parts = event.split(DOT);
                baseEvent = parts[0];
                name = parts[1];

                if(baseEvent) {
                    if(this.eventListeners[baseEvent]) {
                        this._off(baseEvent, name);
                    }
                }
                else {
                    for(t in this.eventListeners) {
                        this._off(t, name);
                    }
                }
            }
            return this;
        },
        /**
         * remove self from parent, but don't destroy
         * @method
         * @memberof Kinetic.Node.prototype
         * @example
         * node.remove();
         */
        remove: function() {
            var parent = this.getParent();

            if(parent && parent.children) {
                parent.children.splice(this.index, 1);
                parent._setChildrenIndices();
                delete this.parent;
            }

            // every cached attr that is calculated via node tree
            // treversal must be cleared when removing a node
            this._clearSelfAndChildrenCache(STAGE);
            this._clearSelfAndChildrenCache(ABSOLUTE_TRANSFORM);
            this._clearSelfAndChildrenCache(VISIBLE);
            this._clearSelfAndChildrenCache(LISTENING);
            this._clearSelfAndChildrenCache(ABSOLUTE_OPACITY);

            return this;
        },
        /**
         * remove and destroy self
         * @method
         * @memberof Kinetic.Node.prototype
         * @example
         * node.destroy();
         */
        destroy: function() {
            // remove from ids and names hashes
            Kinetic._removeId(this.getId());
            Kinetic._removeName(this.getName(), this._id);

            this.remove();
        },
        /**
         * get attr
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} attr
         * @example
         * var x = node.getAttr('x');
         */
        getAttr: function(attr) {
            var method = GET + Kinetic.Util._capitalize(attr);
            if(Kinetic.Util._isFunction(this[method])) {
                return this[method]();
            }
            // otherwise get directly
            else {
                return this.attrs[attr];
            }
        },
        /**
         * get ancestors
         * @method
         * @memberof Kinetic.Node.prototype
         * @example
         * shape.getAncestors().each(function(node) {
        *   console.log(node.getId());
        * })
         */
        getAncestors: function() {
            var parent = this.getParent(),
                ancestors = new Kinetic.Collection();

            while (parent) {
                ancestors.push(parent);
                parent = parent.getParent();
            }

            return ancestors;
        },
        /**
         * set attr
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} attr
         * #param {*} val
         * @example
         * node.setAttr('x', 5);
         */
        setAttr: function() {
            var args = Array.prototype.slice.call(arguments),
                attr = args[0],
                method = SET + Kinetic.Util._capitalize(attr),
                func = this[method];

            args.shift();
            if(Kinetic.Util._isFunction(func)) {
                func.apply(this, args);
            }
            // otherwise set directly
            else {
                this.attrs[attr] = args[0];
            }
            return this;
        },
        /**
         * get attrs object literal
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getAttrs: function() {
            return this.attrs || {};
        },
        /**
         * set multiple attrs at once using an object literal
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Object} config object containing key value pairs
         * @example
         * node.setAttrs({<br>
         *   x: 5,<br>
         *   fill: 'red'<br>
         * });<br>
         */
        setAttrs: function(config) {
            var key, method;

            if(config) {
                for(key in config) {
                    if (key === CHILDREN) {

                    }
                    else {
                        method = SET + Kinetic.Util._capitalize(key);
                        // use setter if available
                        if(Kinetic.Util._isFunction(this[method])) {
                            this[method](config[key]);
                        }
                        // otherwise set directly
                        else {
                            this._setAttr(key, config[key]);
                        }
                    }
                }
            }
            return this;
        },
        /**
         * determine if node is listening for events.  The node is listening only
         *  if it's listening and all of its ancestors are listening.  If an ancestor
         *  is listening, this means that the node is also listening
         * @method
         * @memberof Kinetic.Node.prototype
         */
        isListening: function() {
            return this._getCache(LISTENING, this._isListening);
        },
        _isListening: function() {
            var listening = this.getListening(),
                parent = this.getParent();

            if(listening && parent && !parent.isListening()) {
                return false;
            }
            return listening;
        },
        /**
         * determine if node is visible or not.  Node is visible only
         *  if it's visible and all of its ancestors are visible.  If an ancestor
         *  is invisible, this means that the node is also invisible
         * @method
         * @memberof Kinetic.Node.prototype
         */
        isVisible: function() {
            return this._getCache(VISIBLE, this._isVisible);
        },
        _isVisible: function() {
            var visible = this.getVisible(),
                parent = this.getParent();

            if(visible && parent && !parent.isVisible()) {
                return false;
            }
            return visible;
        },
        /**
         * show node
         * @method
         * @memberof Kinetic.Node.prototype
         */
        show: function() {
            this.setVisible(true);
            return this;
        },
        /**
         * hide node.  Hidden nodes are no longer detectable
         * @method
         * @memberof Kinetic.Node.prototype
         */
        hide: function() {
            this.setVisible(false);
            return this;
        },
        /**
         * get zIndex relative to the node's siblings who share the same parent
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getZIndex: function() {
            return this.index || 0;
        },
        /**
         * get absolute z-index which takes into account sibling
         *  and ancestor indices
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getAbsoluteZIndex: function() {
            var level = this.getLevel(),
                that = this,
                index = 0,
                nodes, len, n, child;

            function addChildren(children) {
                nodes = [];
                len = children.length;
                for(n = 0; n < len; n++) {
                    child = children[n];
                    index++;

                    if(child.nodeType !== SHAPE) {
                        nodes = nodes.concat(child.getChildren().toArray());
                    }

                    if(child._id === that._id) {
                        n = len;
                    }
                }

                if(nodes.length > 0 && nodes[0].getLevel() <= level) {
                    addChildren(nodes);
                }
            }
            if(that.nodeType !== UPPER_STAGE) {
                addChildren(that.getStage().getChildren());
            }

            return index;
        },
        /**
         * get node level in node tree.  Returns an integer.<br><br>
         *  e.g. Stage level will always be 0.  Layers will always be 1.  Groups and Shapes will always
         *  be >= 2
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getLevel: function() {
            var level = 0,
                parent = this.parent;

            while(parent) {
                level++;
                parent = parent.parent;
            }
            return level;
        },
        /**
         * set node position relative to parent
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} x
         * @param {Number} y
         * @example
         * // set x and y<br>
         * node.setPosition(5, 10);<br><br>
         *
         * // set x only<br>
         * node.setPosition({<br>
         *   x: 5<br>
         * });<br><br>
         *
         * // set x and y using an array<br>
         * node.setPosition([5, 10]);<br><br>
         *
         * // set both x and y to 5<br>
         * node.setPosition(5);
         */
        setPosition: function() {
            var pos = Kinetic.Util._getXY([].slice.call(arguments));
            this.setX(pos.x);
            this.setY(pos.y);
            return this;
        },
        /**
         * get node position relative to parent
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getPosition: function() {
            return {
                x: this.getX(),
                y: this.getY()
            };
        },
        /**
         * get absolute position relative to the top left corner of the stage container div
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getAbsolutePosition: function() {
            var absoluteMatrix = this.getAbsoluteTransform().getMatrix(),
                absoluteTransform = new Kinetic.Transform(),
                o = this.getOffset();

            // clone the matrix array
            absoluteTransform.m = absoluteMatrix.slice();

            absoluteTransform.translate(o.x, o.y);

            return absoluteTransform.getTranslation();
        },
        /**
         * set absolute position
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} x
         * @param {Number} y
         */
        setAbsolutePosition: function() {
            var pos = Kinetic.Util._getXY([].slice.call(arguments)),
                trans = this._clearTransform(),
                it;

            // don't clear translation
            this.attrs.x = trans.x;
            this.attrs.y = trans.y;
            delete trans.x;
            delete trans.y;

            // unravel transform
            it = this.getAbsoluteTransform();

            it.invert();
            it.translate(pos.x, pos.y);
            pos = {
                x: this.attrs.x + it.getTranslation().x,
                y: this.attrs.y + it.getTranslation().y
            };

            this.setPosition(pos.x, pos.y);
            this._setTransform(trans);
            return this;
        },
        _setTransform: function(trans) {
            var key;

            for(key in trans) {
                this.attrs[key] = trans[key];
            }

            this._clearCache(TRANSFORM);
            this._clearSelfAndChildrenCache(ABSOLUTE_TRANSFORM);
        },
        _clearTransform: function() {
            var trans = {
                x: this.getX(),
                y: this.getY(),
                rotation: this.getRotation(),
                scaleX: this.getScaleX(),
                scaleY: this.getScaleY(),
                offsetX: this.getOffsetX(),
                offsetY: this.getOffsetY(),
                skewX: this.getSkewX(),
                skewY: this.getSkewY()
            };

            this.attrs.x = 0;
            this.attrs.y = 0;
            this.attrs.rotation = 0;
            this.attrs.scaleX = 1;
            this.attrs.scaleY = 1;
            this.attrs.offsetX = 0;
            this.attrs.offsetY = 0;
            this.attrs.skewX = 0;
            this.attrs.skewY = 0;

            this._clearCache(TRANSFORM);
            this._clearSelfAndChildrenCache(ABSOLUTE_TRANSFORM);

            return trans;
        },
        /**
         * move node by an amount relative to its current position
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} x
         * @param {Number} y
         * @example
         * // move node in x direction by 1px and y direction by 2px<br>
         * node.move(1, 2);<br><br>
         *
         * // move node in x direction by 1px<br>
         * node.move({<br>
         *   x: 1<br>
         * });
         */
        move: function() {
            var pos = Kinetic.Util._getXY([].slice.call(arguments)),
                x = this.getX(),
                y = this.getY();

            if(pos.x !== undefined) {
                x += pos.x;
            }

            if(pos.y !== undefined) {
                y += pos.y;
            }

            this.setPosition(x, y);
            return this;
        },
        _eachAncestorReverse: function(func, includeSelf) {
            var family = [],
                parent = this.getParent(),
                len, n;

            // build family by traversing ancestors
            if(includeSelf) {
                family.unshift(this);
            }
            while(parent) {
                family.unshift(parent);
                parent = parent.parent;
            }

            len = family.length;
            for(n = 0; n < len; n++) {
                func(family[n]);
            }
        },
        /**
         * rotate node by an amount in radians relative to its current rotation
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} theta
         */
        rotate: function(theta) {
            this.setRotation(this.getRotation() + theta);
            return this;
        },
        /**
         * rotate node by an amount in degrees relative to its current rotation
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} deg
         */
        rotateDeg: function(deg) {
            this.setRotation(this.getRotation() + Kinetic.Util._degToRad(deg));
            return this;
        },
        /**
         * move node to the top of its siblings
         * @method
         * @memberof Kinetic.Node.prototype
         */
        moveToTop: function() {
            var index = this.index;
            this.parent.children.splice(index, 1);
            this.parent.children.push(this);
            this.parent._setChildrenIndices();
            return true;
        },
        /**
         * move node up
         * @method
         * @memberof Kinetic.Node.prototype
         */
        moveUp: function() {
            var index = this.index,
                len = this.parent.getChildren().length;
            if(index < len - 1) {
                this.parent.children.splice(index, 1);
                this.parent.children.splice(index + 1, 0, this);
                this.parent._setChildrenIndices();
                return true;
            }
            return false;
        },
        /**
         * move node down
         * @method
         * @memberof Kinetic.Node.prototype
         */
        moveDown: function() {
            var index = this.index;
            if(index > 0) {
                this.parent.children.splice(index, 1);
                this.parent.children.splice(index - 1, 0, this);
                this.parent._setChildrenIndices();
                return true;
            }
            return false;
        },
        /**
         * move node to the bottom of its siblings
         * @method
         * @memberof Kinetic.Node.prototype
         */
        moveToBottom: function() {
            var index = this.index;
            if(index > 0) {
                this.parent.children.splice(index, 1);
                this.parent.children.unshift(this);
                this.parent._setChildrenIndices();
                return true;
            }
            return false;
        },
        /**
         * set zIndex relative to siblings
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Integer} zIndex
         */
        setZIndex: function(zIndex) {
            var index = this.index;
            this.parent.children.splice(index, 1);
            this.parent.children.splice(zIndex, 0, this);
            this.parent._setChildrenIndices();
            return this;
        },
        /**
         * get absolute opacity
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getAbsoluteOpacity: function() {
            return this._getCache(ABSOLUTE_OPACITY, this._getAbsoluteOpacity);
        },
        _getAbsoluteOpacity: function() {
            var absOpacity = this.getOpacity();
            if(this.getParent()) {
                absOpacity *= this.getParent().getAbsoluteOpacity();
            }
            return absOpacity;
        },
        /**
         * move node to another container
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Container} newContainer
         * @example
         * // move node from current layer into layer2<br>
         * node.moveTo(layer2);
         */
        moveTo: function(newContainer) {
            Kinetic.Node.prototype.remove.call(this);
            newContainer.add(this);
            return this;
        },
        /**
         * convert Node into an object for serialization.  Returns an object.
         * @method
         * @memberof Kinetic.Node.prototype
         */
        toObject: function() {
            var type = Kinetic.Util,
                obj = {},
                attrs = this.getAttrs(),
                key, val;

            obj.attrs = {};

            // serialize only attributes that are not function, image, DOM, or objects with methods
            for(key in attrs) {
                val = attrs[key];
                if(!type._isFunction(val) && !type._isElement(val) && !(type._isObject(val) && type._hasMethods(val))) {
                    obj.attrs[key] = val;
                }
            }

            obj.className = this.getClassName();
            return obj;
        },
        /**
         * convert Node into a JSON string.  Returns a JSON string.
         * @method
         * @memberof Kinetic.Node.prototype
         */
        toJSON: function() {
            return JSON.stringify(this.toObject());
        },
        /**
         * get parent container
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getParent: function() {
            return this.parent;
        },
        /**
         * get layer ancestor
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getLayer: function() {
            return this.getParent().getLayer();
        },
        /**
         * get stage ancestor
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getStage: function() {
            return this._getCache(STAGE, this._getStage);
        },
        _getStage: function() {
            var parent = this.getParent();
            if(parent) {
                return parent.getStage();
            }
            else {
                return undefined;
            }
        },
        /**
         * fire event
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} eventType event type.  can be a regular event, like click, mouseover, or mouseout, or it can be a custom event, like myCustomEvent
         * @param {EventObject} evt event object
         * @param {Boolean} bubble setting the value to false, or leaving it undefined, will result in the event
         *  not bubbling.  Setting the value to true will result in the event bubbling.
         * @example
         * // manually fire click event<br>
         * node.fire('click');<br><br>
         *
         * // fire custom event<br>
         * node.fire('foo');<br><br>
         *
         * // fire custom event with custom event object<br>
         * node.fire('foo', {<br>
         *   bar: 10<br>
         * });<br><br>
         *
         * // fire click event that bubbles<br>
         * node.fire('click', null, true);
         */
        fire: function(eventType, evt, bubble) {
            // bubble
            if (bubble) {
                this._fireAndBubble(eventType, evt || {});
            }
            // no bubble
            else {
                this._fire(eventType, evt || {});
            }
            return this;
        },
        /**
         * get absolute transform of the node which takes into
         *  account its ancestor transforms
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getAbsoluteTransform: function() {
            return this._getCache(ABSOLUTE_TRANSFORM, this._getAbsoluteTransform);
        },
        _getAbsoluteTransform: function() {
            // absolute transform
            var am = new Kinetic.Transform(),
                m;

            this._eachAncestorReverse(function(node) {
                m = node.getTransform();
                am.multiply(m);
            }, true);
            return am;
        },
        _getTransform: function() {
            var m = new Kinetic.Transform(),
                x = this.getX(),
                y = this.getY(),
                rotation = this.getRotation(),
                scaleX = this.getScaleX(),
                scaleY = this.getScaleY(),
                skewX = this.getSkewX(),
                skewY = this.getSkewY(),
                offsetX = this.getOffsetX(),
                offsetY = this.getOffsetY();

            if(x !== 0 || y !== 0) {
                m.translate(x, y);
            }
            if(rotation !== 0) {
                m.rotate(rotation);
            }
            if(skewX !== 0 || skewY !== 0) {
                m.skew(skewX, skewY);
            }
            if(scaleX !== 1 || scaleY !== 1) {
                m.scale(scaleX, scaleY);
            }
            if(offsetX !== 0 || offsetY !== 0) {
                m.translate(-1 * offsetX, -1 * offsetY);
            }

            return m;
        },
        /**
         * clone node.  Returns a new Node instance with identical attributes.  You can also override
         *  the node properties with an object literal, enabling you to use an existing node as a template
         *  for another node
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Object} attrs override attrs
         * @example
         * // simple clone<br>
         * var clone = node.clone();<br><br>
         *
         * // clone a node and override the x position<br>
         * var clone = rect.clone({<br>
         *   x: 5<br>
         * });
         */
        clone: function(obj) {
            // instantiate new node
            var className = this.getClassName(),
                node = new Kinetic[className](this.attrs),
                key, allListeners, len, n, listener;

            // copy over listeners
            for(key in this.eventListeners) {
                allListeners = this.eventListeners[key];
                len = allListeners.length;
                for(n = 0; n < len; n++) {
                    listener = allListeners[n];
                    /*
                     * don't include kinetic namespaced listeners because
                     *  these are generated by the constructors
                     */
                    if(listener.name.indexOf(KINETIC) < 0) {
                        // if listeners array doesn't exist, then create it
                        if(!node.eventListeners[key]) {
                            node.eventListeners[key] = [];
                        }
                        node.eventListeners[key].push(listener);
                    }
                }
            }

            // apply attr overrides
            node.setAttrs(obj);
            return node;
        },
        /**
         * Creates a composite data URL. If MIME type is not
         * specified, then "image/png" will result. For "image/jpeg", specify a quality
         * level as quality (range 0.0 - 1.0)
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Object} config
         * @param {Function} config.callback function executed when the composite has completed
         * @param {String} [config.mimeType] can be "image/png" or "image/jpeg".
         *  "image/png" is the default
         * @param {Number} [config.x] x position of canvas section
         * @param {Number} [config.y] y position of canvas section
         * @param {Number} [config.width] width of canvas section
         * @param {Number} [config.height] height of canvas section
         * @param {Number} [config.quality] jpeg quality.  If using an "image/jpeg" mimeType,
         *  you can specify the quality from 0 to 1, where 0 is very poor quality and 1
         *  is very high quality
         */
        toDataURL: function(config) {
            config = config || {};

            var mimeType = config.mimeType || null,
                quality = config.quality || null,
                stage = this.getStage(),
                x = config.x || 0,
                y = config.y || 0,
                canvas = new Kinetic.SceneCanvas({
                    width: config.width || stage.getWidth(),
                    height: config.height || stage.getHeight(),
                    pixelRatio: 1
                }),
                context = canvas.getContext();

            context.save();

            if(x || y) {
                context.translate(-1 * x, -1 * y);
            }

            this.drawScene(canvas);
            context.restore();

            return canvas.toDataURL(mimeType, quality);
        },
        /**
         * converts node into an image.  Since the toImage
         *  method is asynchronous, a callback is required.  toImage is most commonly used
         *  to cache complex drawings as an image so that they don't have to constantly be redrawn
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Object} config
         * @param {Function} config.callback function executed when the composite has completed
         * @param {String} [config.mimeType] can be "image/png" or "image/jpeg".
         *  "image/png" is the default
         * @param {Number} [config.x] x position of canvas section
         * @param {Number} [config.y] y position of canvas section
         * @param {Number} [config.width] width of canvas section
         * @param {Number} [config.height] height of canvas section
         * @param {Number} [config.quality] jpeg quality.  If using an "image/jpeg" mimeType,
         *  you can specify the quality from 0 to 1, where 0 is very poor quality and 1
         *  is very high quality
         * @example
         * var image = node.toImage({<br>
         *   callback: function(img) {<br>
         *     // do stuff with img<br>
         *   }<br>
         * });
         */
        toImage: function(config) {
            Kinetic.Util._getImage(this.toDataURL(config), function(img) {
                config.callback(img);
            });
        },
        /**
         * set size
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Number} width
         * @param {Number} height
         */
        setSize: function() {
            // set stage dimensions
            var size = Kinetic.Util._getSize(Array.prototype.slice.call(arguments));
            this.setWidth(size.width);
            this.setHeight(size.height);
            return this;
        },
        /**
         * get size
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getSize: function() {
            return {
                width: this.getWidth(),
                height: this.getHeight()
            };
        },
        /**
         * get width
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getWidth: function() {
            return this.attrs.width || 0;
        },
        /**
         * get height
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getHeight: function() {
            return this.attrs.height || 0;
        },
        /**
         * get class name, which may return Stage, Layer, Group, or shape class names like Rect, Circle, Text, etc.
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getClassName: function() {
            return this.className || this.nodeType;
        },
        /**
         * get the node type, which may return Stage, Layer, Group, or Node
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getType: function() {
            return this.nodeType;
        },
        _get: function(selector) {
            return this.nodeType === selector ? [this] : [];
        },
        _off: function(type, name) {
            var evtListeners = this.eventListeners[type],
                i, evtName;

            for(i = 0; i < evtListeners.length; i++) {
                evtName = evtListeners[i].name;
                // the following two conditions must be true in order to remove a handler:
                // 1) the current event name cannot be kinetic unless the event name is kinetic
                //    this enables developers to force remove a kinetic specific listener for whatever reason
                // 2) an event name is not specified, or if one is specified, it matches the current event name
                if((evtName !== 'kinetic' || name === 'kinetic') && (!name || evtName === name)) {
                    evtListeners.splice(i, 1);
                    if(evtListeners.length === 0) {
                        delete this.eventListeners[type];
                        break;
                    }
                    i--;
                }
            }
        },
        _fireBeforeChangeEvent: function(attr, oldVal, newVal) {
            this._fire(BEFORE + Kinetic.Util._capitalize(attr) + CHANGE, {
                oldVal: oldVal,
                newVal: newVal
            });
        },
        _fireChangeEvent: function(attr, oldVal, newVal) {
            this._fire(attr + CHANGE, {
                oldVal: oldVal,
                newVal: newVal
            });
        },
        /**
         * set id
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} id
         */
        setId: function(id) {
            var oldId = this.getId();

            Kinetic._removeId(oldId);
            Kinetic._addId(this, id);
            this._setAttr(ID, id);
            return this;
        },
        /**
         * set name
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {String} name
         */
        setName: function(name) {
            var oldName = this.getName();

            Kinetic._removeName(oldName, this._id);
            Kinetic._addName(this, name);
            this._setAttr(NAME, name);
            return this;
        },
        _setAttr: function(key, val) {
            var oldVal;
            if(val !== undefined) {
                oldVal = this.attrs[key];
                this._fireBeforeChangeEvent(key, oldVal, val);
                this.attrs[key] = val;
                this._fireChangeEvent(key, oldVal, val);
            }
        },
        _fireAndBubble: function(eventType, evt, compareShape) {
            var okayToRun = true;

            if(evt && this.nodeType === SHAPE) {
                evt.targetNode = this;
            }

            if(eventType === MOUSEENTER && compareShape && this._id === compareShape._id) {
                okayToRun = false;
            }
            else if(eventType === MOUSELEAVE && compareShape && this._id === compareShape._id) {
                okayToRun = false;
            }

            if(okayToRun) {
                this._fire(eventType, evt);

                // simulate event bubbling
                if(evt && !evt.cancelBubble && this.parent) {
                    if(compareShape && compareShape.parent) {
                        this._fireAndBubble.call(this.parent, eventType, evt, compareShape.parent);
                    }
                    else {
                        this._fireAndBubble.call(this.parent, eventType, evt);
                    }
                }
            }
        },
        _fire: function(eventType, evt) {
            var events = this.eventListeners[eventType],
                len, i;

            if (events) {
                len = events.length;
                for(i = 0; i < len; i++) {
                    events[i].handler.call(this, evt);
                }
            }
        },
        /*
         * draw both scene and hit graphs.  If the node being drawn is the stage, all of the layers will be cleared and redra
         * @method
         * @memberof Kinetic.Node.prototype
         *  the scene renderer
         */
        draw: function() {
            this.drawScene();
            this.drawHit();
            return this;
        },
        shouldDrawHit: function() {
            return this.isListening() && this.isVisible() && !Kinetic.isDragging();
        },
        isDraggable: function() {
            return false;
        },
        /**
         * get transform of the node
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getTransform: function() {
            //return this._getTransform();
            return this._getCache(TRANSFORM, this._getTransform);
        }
    });

    /**
     * create node with JSON string.  De-serializtion does not generate custom
     *  shape drawing functions, images, or event handlers (this would make the
     *  serialized object huge).  If your app uses custom shapes, images, and
     *  event handlers (it probably does), then you need to select the appropriate
     *  shapes after loading the stage and set these properties via on(), setDrawFunc(),
     *  and setImage() methods
     * @method
     * @memberof Kinetic.Node
     * @param {String} JSON string
     * @param {DomElement} [container] optional container dom element used only if you're
     *  creating a stage node
     */
    Kinetic.Node.create = function(json, container) {
        return this._createNode(JSON.parse(json), container);
    };
    Kinetic.Node._createNode = function(obj, container) {
        var className = Kinetic.Node.prototype.getClassName.call(obj),
            children = obj.children,
            no, len, n;

        // if container was passed in, add it to attrs
        if(container) {
            obj.attrs.container = container;
        }

        no = new Kinetic[className](obj.attrs);
        if(children) {
            len = children.length;
            for(n = 0; n < len; n++) {
                no.add(this._createNode(children[n]));
            }
        }

        return no;
    };
    // add getters setters

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'x', 0);

    /**
     * set x position
     * @name setX
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} x
     */

    /**
     * get x position
     * @name getX
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'y', 0);

    /**
     * set y position
     * @name setY
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} y
     */

    /**
     * get y position
     * @name getY
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'opacity', 1);

    /**
     * set opacity.  Opacity values range from 0 to 1.
     *  A node with an opacity of 0 is fully transparent, and a node
     *  with an opacity of 1 is fully opaque
     * @name setOpacity
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Object} opacity
     */

    /**
     * get opacity.
     * @name getOpacity
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetter(Kinetic.Node, 'name');

    /**
     * get name
     * @name getName
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetter(Kinetic.Node, 'id');

    /**
     * get id
     * @name getId
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addRotationGetterSetter(Kinetic.Node, 'rotation', 0);

    /**
     * set rotation in radians
     * @name setRotation
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} theta
     */

    /**
     * set rotation in degrees
     * @name setRotationDeg
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} deg
     */

    /**
     * get rotation in degrees
     * @name getRotationDeg
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get rotation in radians
     * @name getRotation
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Node, 'scale', 1);

    /**
     * set scale
     * @name setScale
     * @param {Number} scale
     * @method
     * @memberof Kinetic.Node.prototype
     * @example
     * // set x and y to the same value<br>
     * shape.setScale(5);<br><br>
     *
     * // set x and y<br>
     * shape.setScale(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setScale({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setScale([20, 40]);
     */

    /**
     * set scale x
     * @name setScaleX
     * @param {Number} x
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * set scale y
     * @name setScaleY
     * @param {Number} y
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get scale
     * @name getScale
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get scale x
     * @name getScaleX
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get scale y
     * @name getScaleY
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Node, 'skew', 0);

    /**
     * set skew
     * @name setSkew
     * @param {Number} x
     * @param {Number} y
     * @method
     * @memberof Kinetic.Node.prototype
     * @example
     * // set x and y<br>
     * shape.setSkew(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setSkew({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setSkew([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setSkew(5);
     */

    /**
     * set skew x
     * @name setSkewX
     * @param {Number} x
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * set skew y
     * @name setSkewY
     * @param {Number} y
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get skew
     * @name getSkew
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get skew x
     * @name getSkewX
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get skew y
     * @name getSkewY
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Node, 'offset', 0);

    /**
     * set offset.  A node's offset defines the position and rotation point
     * @name setOffset
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} x
     * @param {Number} y
     * @example
     * // set x and y<br>
     * shape.setOffset(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setOffset({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setOffset([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setOffset(5);
     */

    /**
     * set offset x
     * @name setOffsetX
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} x
     */

    /**
     * set offset y
     * @name setOffsetY
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} y
     */

    /**
     * get offset
     * @name getOffset
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get offset x
     * @name getOffsetX
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * get offset y
     * @name getOffsetY
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addSetter(Kinetic.Node, 'width');

    /**
     * set width
     * @name setWidth
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} width
     */

    Kinetic.Factory.addSetter(Kinetic.Node, 'height');

    /**
     * set height
     * @name setHeight
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Number} height
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'listening', true);

    /**
     * listen or don't listen to events
     * @name setListening
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Boolean} listening
     */

    /**
     * determine if node is listening or not.  Node can be listening even if its ancestors
     *  are not listening
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'visible', true);

    /**
     * set visible
     * @name setVisible
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Boolean} visible
     */

    /**
     * get visible property for the node.  If you need to determine if the node is actually visible,
     * use the isVisible() method because it takes ancestors into account
     * @name getVisible
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Collection.mapMethods([
        'on',
        'off',
        'remove',
        'destroy',
        'show',
        'hide',
        'move',
        'rotate',
        'moveToTop',
        'moveUp',
        'moveDown',
        'moveToBottom',
        'moveTo',
        'fire',
        'draw'
    ]);
})();
;(function() {
    var BATCH_DRAW_STOP_TIME_DIFF = 500;

    /**
     * Animation constructor.  A stage is used to contain multiple layers and handle
     * @constructor
     * @memberof Kinetic
     * @param {Function} func function executed on each animation frame.  The function is passed a frame object, which contains
     *  timeDiff, lastTime, time, and frameRate properties.  The timeDiff property is the number of milliseconds that have passed
     *  since the last animation frame.  The lastTime property is time in milliseconds that elapsed from the moment the animation started
     *  to the last animation frame.  The time property is the time in milliseconds that ellapsed from the moment the animation started
     *  to the current animation frame.  The frameRate property is the current frame rate in frames / second
     * @param {Kinetic.Layer|Array} [layers] layer(s) to be redrawn on each animation frame. Can be a layer, an array of layers, or null.
     *  Not specifying a node will result in no redraw.
     * @example
     * // move a node to the right at 50 pixels / second<br>
     * var velocity = 50;<br><br>
     *
     * var anim = new Kinetic.Animation(function(frame) {<br>
     *   var dist = velocity * (frame.timeDiff / 1000);<br>
     *   node.move(dist, 0);<br>
     * }, layer);<br><br>
     *
     * anim.start();
     */
    Kinetic.Animation = function(func, layers) {
        this.func = func;
        this.setLayers(layers);
        this.id = Kinetic.Animation.animIdCounter++;
        this.frame = {
            time: 0,
            timeDiff: 0,
            lastTime: new Date().getTime()
        };
    };
    /*
     * Animation methods
     */
    Kinetic.Animation.prototype = {
        /**
         * set layers to be redrawn on each animation frame
         * @method
         * @memberof Kinetic.Animation.prototype
         * @param {Kinetic.Layer|Array} [layers] layer(s) to be redrawn.&nbsp; Can be a layer, an array of layers, or null.  Not specifying a node will result in no redraw.
         */
        setLayers: function(layers) {
            var lays = [];
            // if passing in no layers
            if (!layers) {
                lays = [];
            }
            // if passing in an array of Layers
            // NOTE: layers could be an array or Kinetic.Collection.  for simplicity, I'm just inspecting
            // the length property to check for both cases
            else if (layers.length > 0) {
                lays = layers;
            }
            // if passing in a Layer
            else {
                lays = [layers];
            }

            this.layers = lays;
        },
        /**
         * get layers
         * @method
         * @memberof Kinetic.Animation.prototype
         */
        getLayers: function() {
            return this.layers;
        },
        /**
         * add layer.  Returns true if the layer was added, and false if it was not
         * @method
         * @memberof Kinetic.Animation.prototype
         * @param {Kinetic.Layer} layer
         */
        addLayer: function(layer) {
            var layers = this.layers,
                len, n;

            if (layers) {
                len = layers.length;

                // don't add the layer if it already exists
                for (n = 0; n < len; n++) {
                    if (layers[n]._id === layer._id) {
                        return false;
                    }
                }
            }
            else {
                this.layers = [];
            }

            this.layers.push(layer);
            return true;
        },
        /**
         * determine if animation is running or not.  returns true or false
         * @method
         * @memberof Kinetic.Animation.prototype
         */
        isRunning: function() {
            var a = Kinetic.Animation, animations = a.animations;
            for(var n = 0; n < animations.length; n++) {
                if(animations[n].id === this.id) {
                    return true;
                }
            }
            return false;
        },
        /**
         * start animation
         * @method
         * @memberof Kinetic.Animation.prototype
         */
        start: function() {
            this.stop();
            this.frame.timeDiff = 0;
            this.frame.lastTime = new Date().getTime();
            Kinetic.Animation._addAnimation(this);
        },
        /**
         * stop animation
         * @method
         * @memberof Kinetic.Animation.prototype
         */
        stop: function() {
            Kinetic.Animation._removeAnimation(this);
        },
        _updateFrameObject: function(time) {
            this.frame.timeDiff = time - this.frame.lastTime;
            this.frame.lastTime = time;
            this.frame.time += this.frame.timeDiff;
            this.frame.frameRate = 1000 / this.frame.timeDiff;
        }
    };
    Kinetic.Animation.animations = [];
    Kinetic.Animation.animIdCounter = 0;
    Kinetic.Animation.animRunning = false;

    Kinetic.Animation._addAnimation = function(anim) {
        this.animations.push(anim);
        this._handleAnimation();
    };
    Kinetic.Animation._removeAnimation = function(anim) {
        var id = anim.id, animations = this.animations, len = animations.length;
        for(var n = 0; n < len; n++) {
            if(animations[n].id === id) {
                this.animations.splice(n, 1);
                break;
            }
        }
    };

    Kinetic.Animation._runFrames = function() {
        var layerHash = {},
            animations = this.animations,
            anim, layers, func, n, i, layersLen, layer, key;
        /*
         * loop through all animations and execute animation
         *  function.  if the animation object has specified node,
         *  we can add the node to the nodes hash to eliminate
         *  drawing the same node multiple times.  The node property
         *  can be the stage itself or a layer
         */
        /*
         * WARNING: don't cache animations.length because it could change while
         * the for loop is running, causing a JS error
         */
        for(n = 0; n < animations.length; n++) {
            anim = animations[n];
            layers = anim.layers;
            func = anim.func;

            anim._updateFrameObject(new Date().getTime());
            layersLen = layers.length;

            for (i=0; i<layersLen; i++) {
                layer = layers[i];
                if(layer._id !== undefined) {
                    layerHash[layer._id] = layer;
                }
            }

            // if animation object has a function, execute it
            if(func) {
                func.call(anim, anim.frame);
            }
        }

        for(key in layerHash) {
            layerHash[key].draw();
        }
    };
    Kinetic.Animation._animationLoop = function() {
        var that = this;
        if(this.animations.length > 0) {
            this._runFrames();
            Kinetic.Animation.requestAnimFrame(function() {
                that._animationLoop();
            });
        }
        else {
            this.animRunning = false;
        }
    };
    Kinetic.Animation._handleAnimation = function() {
        var that = this;
        if(!this.animRunning) {
            this.animRunning = true;
            that._animationLoop();
        }
    };
    RAF = (function() {
        return window.requestAnimationFrame
            || window.webkitRequestAnimationFrame
            || window.mozRequestAnimationFrame
            || window.oRequestAnimationFrame
            || window.msRequestAnimationFrame
            || FRAF;
    })();

    function FRAF(callback) {
        window.setTimeout(callback, 1000 / 60);
    }

    Kinetic.Animation.requestAnimFrame = function(callback) {
        var raf = Kinetic.DD && Kinetic.DD.isDragging ? FRAF : RAF;
        raf(callback);
    };

    var moveTo = Kinetic.Node.prototype.moveTo;
    Kinetic.Node.prototype.moveTo = function(container) {
        moveTo.call(this, container);
    };

    /**
     * batch draw
     * @method
     * @memberof Kinetic.Layer.prototype
     */
    Kinetic.Layer.prototype.batchDraw = function() {
        var that = this;

        if (!this.batchAnim) {
            this.batchAnim = new Kinetic.Animation(function() {
                if (that.lastBatchDrawTime && new Date().getTime() - that.lastBatchDrawTime > BATCH_DRAW_STOP_TIME_DIFF) {
                    that.batchAnim.stop();
                }
            }, this);
        }

        this.lastBatchDrawTime = new Date().getTime();

        if (!this.batchAnim.isRunning()) {
            this.draw();
            this.batchAnim.start();
        }
    };

    /**
     * batch draw
     * @method
     * @memberof Kinetic.Stage.prototype
     */
    Kinetic.Stage.prototype.batchDraw = function() {
        this.getChildren().each(function(layer) {
            layer.batchDraw();
        });
    };
})();;(function() {
    var blacklist = {
            node: 1,
            duration: 1,
            easing: 1,
            onFinish: 1,
            yoyo: 1
        },

        PAUSED = 1,
        PLAYING = 2,
        REVERSING = 3,

        idCounter = 0;

    /**
     * Tween constructor.  Tweens enable you to animate a node between the current state and a new state.
     *  You can play, pause, reverse, seek, reset, and finish tweens.  By default, tweens are animated using
     *  a linear easing.  For more tweening options, check out {@link Kinetic.Easings}
     * @constructor
     * @memberof Kinetic
     * @example
     * // instantiate new tween which fully rotates a node in 1 second
     * var tween = new Kinetic.Tween({<br>
     *   node: node,<br>
     *   rotationDeg: 360,<br>
     *   duration: 1,<br>
     *   easing: Kinetic.Easings.EaseInOut<br>
     * });<br><br>
     *
     * // play tween<br>
     * tween.play();<br><br>
     *
     * // pause tween<br>
     * tween.pause();
     */
    Kinetic.Tween = function(config) {
        var that = this,
            node = config.node,
            nodeId = node._id,
            duration = config.duration || 1,
            easing = config.easing || Kinetic.Easings.Linear,
            yoyo = !!config.yoyo,
            key, tween, start, tweenId;

        this.node = node;
        this._id = idCounter++;

        this.anim = new Kinetic.Animation(function() {
            that.tween.onEnterFrame();
        }, node.getLayer() || node.getLayers());

        this.tween = new Tween(key, function(i) {
            that._tweenFunc(i);
        }, easing, 0, 1, duration * 1000, yoyo);

        this._addListeners();

        // init attrs map
        if (!Kinetic.Tween.attrs[nodeId]) {
            Kinetic.Tween.attrs[nodeId] = {};
        }
        if (!Kinetic.Tween.attrs[nodeId][this._id]) {
            Kinetic.Tween.attrs[nodeId][this._id] = {};
        }
        // init tweens map
        if (!Kinetic.Tween.tweens[nodeId]) {
            Kinetic.Tween.tweens[nodeId] = {};
        }

        for (key in config) {
            if (blacklist[key] === undefined) {
                this._addAttr(key, config[key]);
            }
        }

        this.reset();

        // callbacks
        this.onFinish = config.onFinish;
        this.onReset = config.onReset;
    };

    // start/diff object = attrs.nodeId.tweenId.attr
    Kinetic.Tween.attrs = {};
    // tweenId = tweens.nodeId.attr
    Kinetic.Tween.tweens = {};

    Kinetic.Tween.prototype = {
        _addAttr: function(key, end) {
            var node = this.node,
                nodeId = node._id,
                start, diff, tweenId, n, len, startVal, endVal;

            // remove conflict from tween map if it exists
            tweenId = Kinetic.Tween.tweens[nodeId][key];

            if (tweenId) {
                delete Kinetic.Tween.attrs[nodeId][tweenId][key];
            }

            // add to tween map
            start = node.getAttr(key);

            if (Kinetic.Util._isArray(end)) {
                end = Kinetic.Util._getPoints(end);
                diff = [];
                len = end.length;
                for (n=0; n<len; n++) {
                    startVal = start[n];
                    endVal = end[n];
                    diff.push({
                        x: endVal.x - startVal.x,
                        y: endVal.y - startVal.y
                    });
                }

            }
            else {
                diff = end - start;
            }

            Kinetic.Tween.attrs[nodeId][this._id][key] = {
                start: start,
                diff: diff
            };
            Kinetic.Tween.tweens[nodeId][key] = this._id;
        },
        _tweenFunc: function(i) {
            var node = this.node,
                attrs = Kinetic.Tween.attrs[node._id][this._id],
                key, attr, start, diff, newVal, n, len, startVal, diffVal;

            for (key in attrs) {
                attr = attrs[key];
                start = attr.start;
                diff = attr.diff;

                if (Kinetic.Util._isArray(start)) {
                    newVal = [];
                    len = start.length;
                    for (n=0; n<len; n++) {
                        startVal = start[n];
                        diffVal = diff[n];
                        newVal.push({
                            x: startVal.x + (diffVal.x * i),
                            y: startVal.y + (diffVal.y * i)
                        });
                    }
                }
                else {
                    newVal = start + (diff * i);
                }

                node.setAttr(key, newVal);
            }
        },
        _addListeners: function() {
            var that = this;

            // start listeners
            this.tween.onPlay = function() {
                that.anim.start();
            };
            this.tween.onReverse = function() {
                that.anim.start();
            };

            // stop listeners
            this.tween.onPause = function() {
                that.anim.stop();
            };
            this.tween.onFinish = function() {
                if (that.onFinish) {
                    that.onFinish();
                }
            };
            this.tween.onReset = function() {
                if (that.onReset) {
                    that.onReset();
                }
            };
        },
        /**
         * play
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        play: function() {
            this.tween.play();
            return this;
        },
        /**
         * reverse
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        reverse: function() {
            this.tween.reverse();
            return this;
        },
        /**
         * reset
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        reset: function() {
            var node = this.node;
            this.tween.reset();
            (node.getLayer() || node.getLayers()).draw();
            return this;
        },
        /**
         * seek
         * @method
         * @memberof Kinetic.Tween.prototype
         * @param {Integer} t time in seconds between 0 and the duration
         */
        seek: function(t) {
            var node = this.node;
            this.tween.seek(t * 1000);
            (node.getLayer() || node.getLayers()).draw();
            return this;
        },
        /**
         * pause
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        pause: function() {
            this.tween.pause();
            return this;
        },
        /**
         * finish
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        finish: function() {
            var node = this.node;
            this.tween.finish();
            (node.getLayer() || node.getLayers()).draw();
            return this;
        },
        /**
         * destroy
         * @method
         * @memberof Kinetic.Tween.prototype
         */
        destroy: function() {
            var nodeId = this.node._id,
                thisId = this._id,
                attrs = Kinetic.Tween.tweens[nodeId],
                key;

            this.pause();

            for (key in attrs) {
                delete Kinetic.Tween.tweens[nodeId][key];
            }

            delete Kinetic.Tween.attrs[nodeId][thisId];
        }
    };

    var Tween = function(prop, propFunc, func, begin, finish, duration, yoyo) {
        this.prop = prop;
        this.propFunc = propFunc;
        this.begin = begin;
        this._pos = begin;
        this.duration = duration;
        this._change = 0;
        this.prevPos = 0;
        this.yoyo = yoyo;
        this._time = 0;
        this._position = 0;
        this._startTime = 0;
        this._finish = 0;
        this.func = func;
        this._change = finish - this.begin;
        this.pause();
    };
    /*
     * Tween methods
     */
    Tween.prototype = {
        fire: function(str) {
            var handler = this[str];
            if (handler) {
                handler();
            }
        },
        setTime: function(t) {
            if(t > this.duration) {
                if(this.yoyo) {
                    this._time = this.duration;
                    this.reverse();
                }
                else {
                    this.finish();
                }
            }
            else if(t < 0) {
                if(this.yoyo) {
                    this._time = 0;
                    this.play();
                }
                else {
                    this.reset();
                }
            }
            else {
                this._time = t;
                this.update();
            }
        },
        getTime: function() {
            return this._time;
        },
        setPosition: function(p) {
            this.prevPos = this._pos;
            this.propFunc(p);
            this._pos = p;
        },
        getPosition: function(t) {
            if(t === undefined) {
                t = this._time;
            }
            return this.func(t, this.begin, this._change, this.duration);
        },
        play: function() {
            this.state = PLAYING;
            this._startTime = this.getTimer() - this._time;
            this.onEnterFrame();
            this.fire('onPlay');
        },
        reverse: function() {
            this.state = REVERSING;
            this._time = this.duration - this._time;
            this._startTime = this.getTimer() - this._time;
            this.onEnterFrame();
            this.fire('onReverse');
        },
        seek: function(t) {
            this.pause();
            this._time = t;
            this.update();
            this.fire('onSeek');
        },
        reset: function() {
            this.pause();
            this._time = 0;
            this.update();
            this.fire('onReset');
        },
        finish: function() {
            this.pause();
            this._time = this.duration;
            this.update();
            this.fire('onFinish');
        },
        update: function() {
            this.setPosition(this.getPosition(this._time));
        },
        onEnterFrame: function() {
            var t = this.getTimer() - this._startTime;
            if(this.state === PLAYING) {
                this.setTime(t);
            }
            else if (this.state === REVERSING) {
                this.setTime(this.duration - t);
            }
        },
        pause: function() {
            this.state = PAUSED;
            this.fire('onPause');
        },
        getTimer: function() {
            return new Date().getTime();
        }
    };

    /*
     * These eases were ported from an Adobe Flash tweening library to JavaScript
     * by Xaric
     */

    /**
     * @namespace Easings
     * @memberof Kinetic
     */
    Kinetic.Easings = {
        /**
         * back ease in
         * @function
         * @memberof Kinetic.Easings
         */
        'BackEaseIn': function(t, b, c, d, a, p) {
            var s = 1.70158;
            return c * (t /= d) * t * ((s + 1) * t - s) + b;
        },
        /**
         * back ease out
         * @function
         * @memberof Kinetic.Easings
         */
        'BackEaseOut': function(t, b, c, d, a, p) {
            var s = 1.70158;
            return c * (( t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b;
        },
        /**
         * back ease in out
         * @function
         * @memberof Kinetic.Easings
         */
        'BackEaseInOut': function(t, b, c, d, a, p) {
            var s = 1.70158;
            if((t /= d / 2) < 1) {
                return c / 2 * (t * t * (((s *= (1.525)) + 1) * t - s)) + b;
            }
            return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b;
        },
        /**
         * elastic ease in
         * @function
         * @memberof Kinetic.Easings
         */
        'ElasticEaseIn': function(t, b, c, d, a, p) {
            // added s = 0
            var s = 0;
            if(t === 0) {
                return b;
            }
            if((t /= d) == 1) {
                return b + c;
            }
            if(!p) {
                p = d * 0.3;
            }
            if(!a || a < Math.abs(c)) {
                a = c;
                s = p / 4;
            }
            else {
                s = p / (2 * Math.PI) * Math.asin(c / a);
            }
            return -(a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b;
        },
        /**
         * elastic ease out
         * @function
         * @memberof Kinetic.Easings
         */
        'ElasticEaseOut': function(t, b, c, d, a, p) {
            // added s = 0
            var s = 0;
            if(t === 0) {
                return b;
            }
            if((t /= d) == 1) {
                return b + c;
            }
            if(!p) {
                p = d * 0.3;
            }
            if(!a || a < Math.abs(c)) {
                a = c;
                s = p / 4;
            }
            else {
                s = p / (2 * Math.PI) * Math.asin(c / a);
            }
            return (a * Math.pow(2, -10 * t) * Math.sin((t * d - s) * (2 * Math.PI) / p) + c + b);
        },
        /**
         * elastic ease in out
         * @function
         * @memberof Kinetic.Easings
         */
        'ElasticEaseInOut': function(t, b, c, d, a, p) {
            // added s = 0
            var s = 0;
            if(t === 0) {
                return b;
            }
            if((t /= d / 2) == 2) {
                return b + c;
            }
            if(!p) {
                p = d * (0.3 * 1.5);
            }
            if(!a || a < Math.abs(c)) {
                a = c;
                s = p / 4;
            }
            else {
                s = p / (2 * Math.PI) * Math.asin(c / a);
            }
            if(t < 1) {
                return -0.5 * (a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b;
            }
            return a * Math.pow(2, -10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p) * 0.5 + c + b;
        },
        /**
         * bounce ease out
         * @function
         * @memberof Kinetic.Easings
         */
        'BounceEaseOut': function(t, b, c, d) {
            if((t /= d) < (1 / 2.75)) {
                return c * (7.5625 * t * t) + b;
            }
            else if(t < (2 / 2.75)) {
                return c * (7.5625 * (t -= (1.5 / 2.75)) * t + 0.75) + b;
            }
            else if(t < (2.5 / 2.75)) {
                return c * (7.5625 * (t -= (2.25 / 2.75)) * t + 0.9375) + b;
            }
            else {
                return c * (7.5625 * (t -= (2.625 / 2.75)) * t + 0.984375) + b;
            }
        },
        /**
         * bounce ease in
         * @function
         * @memberof Kinetic.Easings
         */
        'BounceEaseIn': function(t, b, c, d) {
            return c - Kinetic.Easings.BounceEaseOut(d - t, 0, c, d) + b;
        },
        /**
         * bounce ease in out
         * @function
         * @memberof Kinetic.Easings
         */
        'BounceEaseInOut': function(t, b, c, d) {
            if(t < d / 2) {
                return Kinetic.Easings.BounceEaseIn(t * 2, 0, c, d) * 0.5 + b;
            }
            else {
                return Kinetic.Easings.BounceEaseOut(t * 2 - d, 0, c, d) * 0.5 + c * 0.5 + b;
            }
        },
        /**
         * ease in
         * @function
         * @memberof Kinetic.Easings
         */
        'EaseIn': function(t, b, c, d) {
            return c * (t /= d) * t + b;
        },
        /**
         * ease out
         * @function
         * @memberof Kinetic.Easings
         */
        'EaseOut': function(t, b, c, d) {
            return -c * (t /= d) * (t - 2) + b;
        },
        /**
         * ease in out
         * @function
         * @memberof Kinetic.Easings
         */
        'EaseInOut': function(t, b, c, d) {
            if((t /= d / 2) < 1) {
                return c / 2 * t * t + b;
            }
            return -c / 2 * ((--t) * (t - 2) - 1) + b;
        },
        /**
         * strong ease in
         * @function
         * @memberof Kinetic.Easings
         */
        'StrongEaseIn': function(t, b, c, d) {
            return c * (t /= d) * t * t * t * t + b;
        },
        /**
         * strong ease out
         * @function
         * @memberof Kinetic.Easings
         */
        'StrongEaseOut': function(t, b, c, d) {
            return c * (( t = t / d - 1) * t * t * t * t + 1) + b;
        },
        /**
         * strong ease in out
         * @function
         * @memberof Kinetic.Easings
         */
        'StrongEaseInOut': function(t, b, c, d) {
            if((t /= d / 2) < 1) {
                return c / 2 * t * t * t * t * t + b;
            }
            return c / 2 * ((t -= 2) * t * t * t * t + 2) + b;
        },
        /**
         * linear
         * @function
         * @memberof Kinetic.Easings
         */
        'Linear': function(t, b, c, d) {
            return c * t / d + b;
        }
    };
})();
;(function() {
    Kinetic.DD = {
        // properties
        anim: new Kinetic.Animation(),
        isDragging: false,
        offset: {
            x: 0,
            y: 0
        },
        node: null,

        // methods
        _drag: function(evt) {
            var dd = Kinetic.DD,
                node = dd.node;

            if(node) {
                node._setDragPosition(evt);

                if(!dd.isDragging) {
                    dd.isDragging = true;
                    node.fire('dragstart', evt, true);
                }

                // execute ondragmove if defined
                node.fire('dragmove', evt, true);
            }
        },
        _endDragBefore: function(evt) {
            var dd = Kinetic.DD,
                node = dd.node,
                nodeType, layer;

            if(node) {
                nodeType = node.nodeType,
                    layer = node.getLayer();
                dd.anim.stop();

                // only fire dragend event if the drag and drop
                // operation actually started.
                if(dd.isDragging) {
                    dd.isDragging = false;
                    Kinetic.listenClickTap = false;

                    if (evt) {
                        evt.dragEndNode = node;
                    }
                }

                delete dd.node;

                (layer || node).draw();
            }
        },
        _endDragAfter: function(evt) {
            evt = evt || {};

            var dragEndNode = evt.dragEndNode;

            if (evt && dragEndNode) {
                dragEndNode.fire('dragend', evt, true);
            }
        }
    };

    // Node extenders

    /**
     * initiate drag and drop
     * @method
     * @memberof Kinetic.Node.prototype
     */
    Kinetic.Node.prototype.startDrag = function() {
        var dd = Kinetic.DD,
            stage = this.getStage(),
            layer = this.getLayer(),
            pos = stage.getPointerPosition(),
            ap = this.getAbsolutePosition();

        if(pos) {
            if (dd.node) {
                dd.node.stopDrag();
            }

            dd.node = this;
            dd.offset.x = pos.x - ap.x;
            dd.offset.y = pos.y - ap.y;
            dd.anim.setLayers(layer || this.getLayers());
            dd.anim.start();

            this._setDragPosition();
        }
    };

    Kinetic.Node.prototype._setDragPosition = function(evt) {
        var dd = Kinetic.DD,
            pos = this.getStage().getPointerPosition(),
            dbf = this.getDragBoundFunc(),
            newNodePos = {
                x: pos.x - dd.offset.x,
                y: pos.y - dd.offset.y
            };

        if(dbf !== undefined) {
            newNodePos = dbf.call(this, newNodePos, evt);
        }

        this.setAbsolutePosition(newNodePos);
    };

    /**
     * stop drag and drop
     * @method
     * @memberof Kinetic.Node.prototype
     */
    Kinetic.Node.prototype.stopDrag = function() {
        var dd = Kinetic.DD,
            evt = {};
        dd._endDragBefore(evt);
        dd._endDragAfter(evt);
    };

    /**
     * set draggable
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {String} draggable
     */
    Kinetic.Node.prototype.setDraggable = function(draggable) {
        this._setAttr('draggable', draggable);
        this._dragChange();
    };

    var origDestroy = Kinetic.Node.prototype.destroy;

    Kinetic.Node.prototype.destroy = function() {
        var dd = Kinetic.DD;

        // stop DD
        if(dd.node && dd.node._id === this._id) {

            this.stopDrag();
        }

        origDestroy.call(this);
    };

    /**
     * determine if node is currently in drag and drop mode
     * @method
     * @memberof Kinetic.Node.prototype
     */
    Kinetic.Node.prototype.isDragging = function() {
        var dd = Kinetic.DD;
        return dd.node && dd.node._id === this._id && dd.isDragging;
    };

    Kinetic.Node.prototype._listenDrag = function() {
        var that = this;

        this._dragCleanup();

        if (this.getClassName() === 'Stage') {
            this.on('contentMousedown.kinetic contentTouchstart.kinetic', function(evt) {
                if(!Kinetic.DD.node) {
                    that.startDrag(evt);
                }
            });
        }
        else {
            this.on('mousedown.kinetic touchstart.kinetic', function(evt) {
                if(!Kinetic.DD.node) {
                    that.startDrag(evt);
                }
            });
        }
    };

    Kinetic.Node.prototype._dragChange = function() {
        if(this.attrs.draggable) {
            this._listenDrag();
        }
        else {
            // remove event listeners
            this._dragCleanup();

            /*
             * force drag and drop to end
             * if this node is currently in
             * drag and drop mode
             */
            var stage = this.getStage();
            var dd = Kinetic.DD;
            if(stage && dd.node && dd.node._id === this._id) {
                dd.node.stopDrag();
            }
        }
    };

    Kinetic.Node.prototype._dragCleanup = function() {
        this.off('mousedown.kinetic');
        this.off('touchstart.kinetic');
    };

    Kinetic.Factory.addGetterSetter(Kinetic.Node, 'dragBoundFunc');

    /**
     * set drag bound function.  This is used to override the default
     *  drag and drop position
     * @name setDragBoundFunc
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Function} dragBoundFunc
     */

    /**
     * get dragBoundFunc
     * @name getDragBoundFunc
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Factory.addGetter(Kinetic.Node, 'draggable', false);

    /**
     * get draggable
     * @name getDraggable
     * @method
     * @memberof Kinetic.Node.prototype
     */

    /**
     * alias of getDraggable()
     * @name isDraggable
     * @method
     * @memberof Kinetic.Node.prototype
     */

    Kinetic.Node.prototype.isDraggable = Kinetic.Node.prototype.getDraggable;

    var html = document.getElementsByTagName('html')[0];
    html.addEventListener('mouseup', Kinetic.DD._endDragBefore, true);
    html.addEventListener('touchend', Kinetic.DD._endDragBefore, true);

    html.addEventListener('mouseup', Kinetic.DD._endDragAfter, false);
    html.addEventListener('touchend', Kinetic.DD._endDragAfter, false);

})();
;(function() {
    Kinetic.Util.addMethods(Kinetic.Container, {
        __init: function(config) {
            this.children = new Kinetic.Collection();
            Kinetic.Node.call(this, config);
        },
        /**
         * returns a {@link Kinetic.Collection} of direct descendant nodes
         * @method
         * @memberof Kinetic.Container.prototype
         */
        getChildren: function() {
            return this.children;
        },
        /**
         * determine if node has children
         * @method
         * @memberof Kinetic.Container.prototype
         */
        hasChildren: function() {
            return this.getChildren().length > 0;
        },
        /**
         * remove all children
         * @method
         * @memberof Kinetic.Container.prototype
         */
        removeChildren: function() {
            var children = this.children,
                child;

            while(children.length > 0) {
                child = children[0];
                if (child.hasChildren()) {
                    child.removeChildren();
                }
                child.remove();
            }

            return this;
        },
        /**
         * destroy all children
         * @method
         * @memberof Kinetic.Container.prototype
         */
        destroyChildren: function() {
            var children = this.children;
            while(children.length > 0) {
                children[0].destroy();
            }
            return this;
        },
        /**
         * add node to container
         * @method
         * @memberof Kinetic.Container.prototype
         * @param {Node} child
         */
        add: function(child) {
            var children = this.children;

            this._validateAdd(child);
            child.index = children.length;
            child.parent = this;
            children.push(child);
            this._fire('add', {
                child: child
            });

            // chainable
            return this;
        },
        destroy: function() {
            // destroy children
            if (this.hasChildren()) {
                this.destroyChildren();
            }
            // then destroy self
            Kinetic.Node.prototype.destroy.call(this);
        },
        /**
         * return a {@link Kinetic.Collection} of nodes that match the selector.  Use '#' for id selections
         * and '.' for name selections.  You can also select by type or class name. Pass multiple selectors
         * separated by a space.
         * @method
         * @memberof Kinetic.Container.prototype
         * @param {String} selector
         * @example
         * // select node with id foo<br>
         * var node = stage.find('#foo');<br><br>
         *
         * // select nodes with name bar inside layer<br>
         * var nodes = layer.find('.bar');<br><br>
         *
         * // select all groups inside layer<br>
         * var nodes = layer.find('Group');<br><br>
         *
         * // select all rectangles inside layer<br>
         * var nodes = layer.find('Rect');<br><br>
         *
         * // select node with an id of foo or a name of bar inside layer<br>
         * var nodes = layer.find('#foo, .bar');
         */
        find: function(selector) {
            var retArr = [],
                selectorArr = selector.replace(/ /g, '').split(','),
                len = selectorArr.length,
                n, i, sel, arr, node, children, clen;

            for (n = 0; n < len; n++) {
                sel = selectorArr[n];

                // id selector
                if(sel.charAt(0) === '#') {
                    node = this._getNodeById(sel.slice(1));
                    if(node) {
                        retArr.push(node);
                    }
                }
                // name selector
                else if(sel.charAt(0) === '.') {
                    arr = this._getNodesByName(sel.slice(1));
                    retArr = retArr.concat(arr);
                }
                // unrecognized selector, pass to children
                else {
                    children = this.getChildren();
                    clen = children.length;
                    for(i = 0; i < clen; i++) {
                        retArr = retArr.concat(children[i]._get(sel));
                    }
                }
            }

            return Kinetic.Collection.toCollection(retArr);
        },
        _getNodeById: function(key) {
            var node = Kinetic.ids[key];

            if(node !== undefined && this.isAncestorOf(node)) {
                return node;
            }
            return null;
        },
        _getNodesByName: function(key) {
            var arr = Kinetic.names[key] || [];
            return this._getDescendants(arr);
        },
        _get: function(selector) {
            var retArr = Kinetic.Node.prototype._get.call(this, selector);
            var children = this.getChildren();
            var len = children.length;
            for(var n = 0; n < len; n++) {
                retArr = retArr.concat(children[n]._get(selector));
            }
            return retArr;
        },
        // extenders
        toObject: function() {
            var obj = Kinetic.Node.prototype.toObject.call(this);

            obj.children = [];

            var children = this.getChildren();
            var len = children.length;
            for(var n = 0; n < len; n++) {
                var child = children[n];
                obj.children.push(child.toObject());
            }

            return obj;
        },
        _getDescendants: function(arr) {
            var retArr = [];
            var len = arr.length;
            for(var n = 0; n < len; n++) {
                var node = arr[n];
                if(this.isAncestorOf(node)) {
                    retArr.push(node);
                }
            }

            return retArr;
        },
        /**
         * determine if node is an ancestor
         * of descendant
         * @method
         * @memberof Kinetic.Container.prototype
         * @param {Kinetic.Node} node
         */
        isAncestorOf: function(node) {
            var parent = node.getParent();
            while(parent) {
                if(parent._id === this._id) {
                    return true;
                }
                parent = parent.getParent();
            }

            return false;
        },
        clone: function(obj) {
            // call super method
            var node = Kinetic.Node.prototype.clone.call(this, obj);

            this.getChildren().each(function(no) {
                node.add(no.clone());
            });
            return node;
        },
        /**
         * get all shapes that intersect a point.  Note: because this method must clear a temporary
         * canvas and redraw every shape inside the container, it should only be used for special sitations
         * because it performs very poorly.  Please use the {@link Kinetic.Stage#getIntersection} method if at all possible
         * because it performs much better
         * @method
         * @memberof Kinetic.Container.prototype
         * @param {Object} pos
         */
        getAllIntersections: function() {
            var pos = Kinetic.Util._getXY(Array.prototype.slice.call(arguments));
            var arr = [];
            var shapes = this.find('Shape');

            var len = shapes.length;
            for(var n = 0; n < len; n++) {
                var shape = shapes[n];
                if(shape.isVisible() && shape.intersects(pos)) {
                    arr.push(shape);
                }
            }

            return arr;
        },
        _setChildrenIndices: function() {
            this.children.each(function(child, n) {
                child.index = n;
            });
        },
        drawScene: function(canvas) {
            var layer = this.getLayer(),
                clip = this.getClipWidth() && this.getClipHeight(),
                children, n, len;

            if (!canvas && layer) {
                canvas = layer.getCanvas();
            }

            if(this.isVisible()) {
                if (clip) {
                    canvas.getContext()._clip(this);
                }
                else {
                    this._drawChildren(canvas);
                }
            }

            return this;
        },
        _drawChildren: function(canvas) {
            this.children.each(function(child) {
                child.drawScene(canvas);
            });
        },
        drawHit: function() {
            var hasClip = this.getClipWidth() && this.getClipHeight() && this.nodeType !== 'Stage',
                n = 0,
                len = 0,
                children = [],
                hitCanvas;

            if(this.shouldDrawHit()) {
                if (hasClip) {
                    hitCanvas = this.getLayer().hitCanvas;
                    hitCanvas.getContext()._clip(this);
                }

                children = this.children;
                len = children.length;

                for(n = 0; n < len; n++) {
                    children[n].drawHit();
                }
                if (hasClip) {
                    hitCanvas.getContext()._context.restore();
                }
            }

            return this;
        }
    });

    Kinetic.Util.extend(Kinetic.Container, Kinetic.Node);
    // deprecated methods
    Kinetic.Container.prototype.get = Kinetic.Container.prototype.find;

    // add getters setters
    Kinetic.Factory.addBoxGetterSetter(Kinetic.Container, 'clip');

    /**
     * set clip
     * @method
     * @name setClip
     * @memberof Kinetic.Container.prototype
     * @param {Object|Array}
     * @example
     * // set clip x, y, width and height with an array<br>
     * image.setClip([20, 20, 100, 100]);<br><br>
     *
     * // set clip x, y, width and height with an object<br>
     * image.setClip({<br>
     *   x: 20,<br>
     *   y: 20,<br>
     *   width: 20,<br>
     *   height: 20<br>
     * });
     */

    /**
     * set clipX
     * @method
     * @name setClipX
     * @memberof Kinetic.Container.prototype
     * @param {Number} x
     */

    /**
     * set clipY
     * @name setClipY
     * @method
     * @memberof Kinetic.Container.prototype
     * @param {Number} y
     */

    /**
     * set clipWidth
     * @name setClipWidth
     * @method
     * @memberof Kinetic.Container.prototype
     * @param {Number} width
     */

    /**
     * set clipHeight
     * @name setClipHeight
     * @method
     * @memberof Kinetic.Container.prototype
     * @param {Number} height
     */

    /**
     * get clip
     * @name getClip
     * @method
     * @memberof Kinetic.Container.prototype
     * @return {Object}
     */

    /**
     * get clip x
     * @name getClipX
     * @method
     * @memberof Kinetic.Container.prototype
     */

    /**
     * get clip y
     * @name getClipY
     * @method
     * @memberof Kinetic.Container.prototype
     */

    /**
     * get clip width
     * @name getClipWidth
     * @method
     * @memberof Kinetic.Container.prototype
     */

    /**
     * get clip height
     * @name getClipHeight
     * @method
     * @memberof Kinetic.Container.prototype
     */

})();
;(function() {
    var HAS_SHADOW = 'hasShadow';

    function _fillFunc(context) {
        context.fill();
    }
    function _strokeFunc(context) {
        context.stroke();
    }
    function _fillFuncHit(context) {
        context.fill();
    }
    function _strokeFuncHit(context) {
        context.stroke();
    }

    function _clearHasShadowCache() {
        this._clearCache(HAS_SHADOW);
    }

    Kinetic.Util.addMethods(Kinetic.Shape, {
        __init: function(config) {
            this.nodeType = 'Shape';
            this._fillFunc = _fillFunc;
            this._strokeFunc = _strokeFunc;
            this._fillFuncHit = _fillFuncHit;
            this._strokeFuncHit = _strokeFuncHit;

            // set colorKey
            var shapes = Kinetic.shapes;
            var key;

            while(true) {
                key = Kinetic.Util.getRandomColor();
                if(key && !( key in shapes)) {
                    break;
                }
            }

            this.colorKey = key;
            shapes[key] = this;

            // call super constructor
            Kinetic.Node.call(this, config);

            this._setDrawFuncs();

            this.on('shadowColorChange.kinetic shadowBlurChange.kinetic shadowOffsetChange.kinetic shadowOpacityChange.kinetic', _clearHasShadowCache);
        },
        hasChildren: function() {
            return false;
        },
        getChildren: function() {
            return [];
        },
        /**
         * get canvas context tied to the layer
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        getContext: function() {
            return this.getLayer().getContext();
        },
        /**
         * get canvas renderer tied to the layer.  Note that this returns a canvas renderer, not a canvas element
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        getCanvas: function() {
            return this.getLayer().getCanvas();
        },
        /**
         * returns whether or not a shadow will be rendered
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        hasShadow: function() {
            return this._getCache(HAS_SHADOW, this._hasShadow);
        },
        _hasShadow: function() {
            return this.getShadowEnabled() && (this.getShadowOpacity() !== 0 && !!(this.getShadowColor() || this.getShadowBlur() || this.getShadowOffsetX() || this.getShadowOffsetY()));
        },
        /**
         * returns whether or not the shape will be filled
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        hasFill: function() {
            return !!(this.getFill() || this.getFillPatternImage() || this.getFillLinearGradientColorStops() || this.getFillRadialGradientColorStops());
        },
        /**
         * returns whether or not the shape will be stroked
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        hasStroke: function() {
            return !!(this.getStroke() || this.getStrokeWidth());
        },
        _get: function(selector) {
            return this.className === selector || this.nodeType === selector ? [this] : [];
        },
        /**
         * determines if point is in the shape, regardless if other shapes are on top of it.  Note: because
         *  this method clears a temporary canvas and then redraws the shape, it performs very poorly if executed many times
         *  consecutively.  Please use the {@link Kinetic.Stage#getIntersection} method if at all possible
         *  because it performs much better
         * @method
         * @memberof Kinetic.Shape.prototype
         * @param {Object} point point can be an object containing
         *  an x and y property, or it can be an array with two elements
         *  in which the first element is the x component and the second
         *  element is the y component
         */
        intersects: function() {
            var pos = Kinetic.Util._getXY(Array.prototype.slice.call(arguments)),
                stage = this.getStage(),
                bufferHitCanvas = stage.bufferHitCanvas,
                p;

            bufferHitCanvas.getContext().clear();
            this.drawScene(bufferHitCanvas);
            p = bufferHitCanvas.context.getImageData(pos.x | 0, pos.y | 0, 1, 1).data;
            return p[3] > 0;
        },
        /**
         * enable fill
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        enableFill: function() {
            this._setAttr('fillEnabled', true);
            return this;
        },
        /**
         * disable fill
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        disableFill: function() {
            this._setAttr('fillEnabled', false);
            return this;
        },
        /**
         * enable stroke
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        enableStroke: function() {
            this._setAttr('strokeEnabled', true);
            return this;
        },
        /**
         * disable stroke
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        disableStroke: function() {
            this._setAttr('strokeEnabled', false);
            return this;
        },
        /**
         * enable stroke scale
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        enableStrokeScale: function() {
            this._setAttr('strokeScaleEnabled', true);
            return this;
        },
        /**
         * disable stroke scale
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        disableStrokeScale: function() {
            this._setAttr('strokeScaleEnabled', false);
            return this;
        },
        /**
         * enable shadow
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        enableShadow: function() {
            this._setAttr('shadowEnabled', true);
            return this;
        },
        /**
         * disable shadow
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        disableShadow: function() {
            this._setAttr('shadowEnabled', false);
            return this;
        },
        /**
         * enable dash array
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        enableDashArray: function() {
            this._setAttr('dashArrayEnabled', true);
            return this;
        },
        /**
         * disable dash array
         * @method
         * @memberof Kinetic.Shape.prototype
         */
        disableDashArray: function() {
            this._setAttr('dashArrayEnabled', false);
            return this;
        },
        // extends Node.prototype.destroy
        destroy: function() {
            Kinetic.Node.prototype.destroy.call(this);
            delete Kinetic.shapes[this.colorKey];
            return this;
        },
        _useBufferCanvas: function() {
            return (this.hasShadow() || this.getAbsoluteOpacity() !== 1) && this.hasFill() && this.hasStroke();
        },
        drawScene: function(can) {
            var canvas = can || this.getLayer().getCanvas(),
                context = canvas.getContext(),
                drawFunc = this.getDrawFunc(),
                hasShadow = this.hasShadow(),
                stage, bufferCanvas, bufferContext;

            if(drawFunc && this.isVisible()) {
                if (this._useBufferCanvas()) {
                    stage = this.getStage();
                    bufferCanvas = stage.bufferCanvas;
                    bufferContext = bufferCanvas.getContext();
                    bufferContext.clear();
                    bufferContext.save();
                    bufferContext._applyLineJoin(this);
                    bufferContext._applyAncestorTransforms(this);
                    drawFunc.call(this, bufferContext);
                    bufferContext.restore();

                    context.save();
                    if (hasShadow) {
                        context.save();
                        context._applyShadow(this);
                        context.drawImage(bufferCanvas._canvas, 0, 0);
                        context.restore();
                    }

                    context._applyOpacity(this);
                    context.drawImage(bufferCanvas._canvas, 0, 0);
                    context.restore();
                }
                // if buffer canvas is not needed
                else {
                    context.save();
                    context._applyLineJoin(this);
                    context._applyAncestorTransforms(this);

                    if (hasShadow) {
                        context.save();
                        context._applyShadow(this);
                        drawFunc.call(this, context);
                        context.restore();
                    }

                    context._applyOpacity(this);
                    drawFunc.call(this, context);
                    context.restore();
                }
            }

            return this;
        },
        drawHit: function() {
            var attrs = this.getAttrs(),
                drawFunc = attrs.drawHitFunc || attrs.drawFunc,
                canvas = this.getLayer().hitCanvas,
                context = canvas.getContext();

            if(drawFunc && this.shouldDrawHit()) {
                context.save();
                context._applyLineJoin(this);
                context._applyAncestorTransforms(this);
                drawFunc.call(this, context);
                context.restore();
            }
            return this;
        },
        _setDrawFuncs: function() {
            if(!this.attrs.drawFunc && this.drawFunc) {
                this.setDrawFunc(this.drawFunc);
            }
            if(!this.attrs.drawHitFunc && this.drawHitFunc) {
                this.setDrawHitFunc(this.drawHitFunc);
            }
        }
    });
    Kinetic.Util.extend(Kinetic.Shape, Kinetic.Node);

    // add getters and setters
    Kinetic.Factory.addColorGetterSetter(Kinetic.Shape, 'stroke');

    /**
     * set stroke color
     * @name setStroke
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {String} color
     */

    /**
     * set stroke color with an object literal
     * @name setStrokeRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Obect} color requires an object literal containing an r, g, and b component
     * @example
     * shape.setStrokeRGB({<br>
     *   r: 200,<br>
     *   g: 50,<br>
     *   b: 100<br>
     * });
     */

    /**
     * set stroke color red component
     * @name setStrokeR
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} red
     */

    /**
     * set stroke color green component
     * @name setStrokeG
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} green
     */

    /**
     * set stroke color blue component
     * @name setStrokeB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} blue
     */

    /**
     * get stroke color
     * @name getStroke
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get stroke color as an object literal
     * @name getStrokeRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get stroke color red component
     * @name getStrokeR
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get stroke color green component
     * @name getStrokeG
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get stroke color blue component
     * @name getStrokeB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'lineJoin');

    /**
     * set line join
     * @name setLineJoin
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {String} lineJoin.  Can be miter, round, or bevel.  The
     *  default is miter
     */

    /**
     * get line join
     * @name getLineJoin
     * @method
     * @memberof Kinetic.Shape.prototype
     */


    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'lineCap');

    /**
     * set line cap.  Can be butt, round, or square
     * @name setLineCap
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {String} lineCap
     */

    /**
     * get line cap
     * @name getLineCap
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'strokeWidth');

    /**
     * set stroke width
     * @name setStrokeWidth
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} strokeWidth
     */

    /**
     * get stroke width
     * @name getStrokeWidth
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'drawFunc');

    /**
     * set draw function
     * @name setDrawFunc
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Function} drawFunc drawing function
     */

    /**
     * get draw function
     * @name getDrawFunc
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'drawHitFunc');

    /**
     * set draw hit function used for hit detection
     * @name setDrawHitFunc
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Function} drawHitFunc drawing function used for hit detection
     */

    /**
     * get draw hit function
     * @name getDrawHitFunc
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'dashArray');

    /**
     * set dash array.
     * @name setDashArray
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Array} dashArray
     *  examples:<br>
     *  [10, 5] dashes are 10px long and 5 pixels apart
     *  [10, 20, 0.001, 20] if using a round lineCap, the line will
     *  be made up of alternating dashed lines that are 10px long
     *  and 20px apart, and dots that have a radius of 5px and are 20px
     *  apart
     */

    /**
     * get dash array
     * @name getDashArray
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addColorGetterSetter(Kinetic.Shape, 'shadowColor');

    /**
     * set shadow color
     * @name setShadowColor
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {String} color
     */

    /**
     * set shadow color with an object literal
     * @name setShadowColorRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Obect} color requires an object literal containing an r, g, and b component
     * @example
     * shape.setShadowRGB({<br>
     *   r: 200,<br>
     *   g: 50,<br>
     *   b: 100<br>
     * });
     */

    /**
     * set shadow color red component
     * @name setShadowColorR
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} red
     */

    /**
     * set shadow color green component
     * @name setShadowColorG
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} green
     */

    /**
     * set shadow color blue component
     * @name setShadowColorB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} blue
     */

    /**
     * get shadow color
     * @name getShadowColor
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow color as an object literal
     * @name getShadowColorRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow color red component
     * @name getShadowColorR
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow color green component
     * @name getShadowColorG
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow color blue component
     * @name getShadowColorB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'shadowBlur');

    /**
     * set shadow blur
     * @name setShadowBlur
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} blur
     */

    /**
     * get shadow blur
     * @name getShadowBlur
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'shadowOpacity');

    /**
     * set shadow opacity
     * @name setShadowOpacity
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} opacity must be a value between 0 and 1
     */

    /**
     * get shadow opacity
     * @name getShadowOpacity
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillPatternImage');

    /**
     * set fill pattern image
     * @name setFillPatternImage
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Image} image object
     */

    /**
     * get fill pattern image
     * @name getFillPatternImage
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addColorGetterSetter(Kinetic.Shape, 'fill');

    /**
     * set fill color
     * @name setFill
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {String} color
     */

    /**
     * set fill color with an object literal
     * @name setFillRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Obect} color requires an object literal containing an r, g, and b component
     * @example
     * shape.setFillRGB({<br>
     *   r: 200,<br>
     *   g: 50,<br>
     *   b: 100<br>
     * });
     */

    /**
     * set fill color red component
     * @name setFillR
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} red
     */

    /**
     * set fill color green component
     * @name setFillG
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} green
     */

    /**
     * set fill color blue component
     * @name setFillB
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Integer} blue
     */

    /**
     * get fill color
     * @name getFill
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill color as an object literal
     * @name getFillRGB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill color red component
     * @name getFillR
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill color green component
     * @name getFillG
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill color blue component
     * @name getFillB
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillPatternX');

    /**
     * set fill pattern x
     * @name setFillPatternX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * get fill pattern x
     * @name getFillPatternX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillPatternY');

    /**
     * set fill pattern y
     * @name setFillPatternY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill pattern y
     * @name getFillPatternY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillLinearGradientColorStops');

    /**
     * set fill linear gradient color stops
     * @name setFillLinearGradientColorStops
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Array} colorStops
     */

    /**
     * get fill linear gradient color stops
     * @name getFillLinearGradientColorStops
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Array} colorStops
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillRadialGradientStartRadius');

    /**
     * set fill radial gradient start radius
     * @name setFillRadialGradientStartRadius
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} radius
     */

    /**
     * get fill radial gradient start radius
     * @name getFillRadialGradientStartRadius
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillRadialGradientEndRadius');

    /**
     * set fill radial gradient end radius
     * @name setFillRadialGradientEndRadius
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} radius
     */

    /**
     * get fill radial gradient end radius
     * @name getFillRadialGradientEndRadius
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillRadialGradientColorStops');

    /**
     * set fill radial gradient color stops
     * @name setFillRadialGradientColorStops
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} colorStops
     */

    /**
     * get fill radial gradient color stops
     * @name getFillRadialGradientColorStops
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillPatternRepeat');

    /**
     * set fill pattern repeat
     * @name setFillPatternRepeat
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} repeat can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     */

    /**
     * get fill pattern repeat
     * @name getFillPatternRepeat
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillEnabled', true);

    /**
     * set fill enabled
     * @name setFillEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Boolean} enabled
     */

    /**
     * get fill enabled
     * @name getFillEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'strokeEnabled', true);

    /**
     * set stroke enabled
     * @name setStrokeEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Boolean} enabled
     */

    /**
     * get stroke enabled
     * @name getStrokeEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'shadowEnabled', true);

    /**
     * set shadow enabled
     * @name setShadowEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Boolean} enabled
     */

    /**
     * get shadow enabled
     * @name getShadowEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'dashArrayEnabled', true);

    /**
     * set dash array enabled
     * @name setDashArrayEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Boolean} enabled
     */

    /**
     * get dash array enabled
     * @name getDashArrayEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'fillPriority', 'color');

    /**
     * set fill priority
     * @name setFillPriority
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} priority can be color, pattern, linear-gradient, or radial-gradient
     *  The default is color.
     */

    /**
     * get fill priority
     * @name getFillPriority
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Shape, 'strokeScaleEnabled', true);

    /**
     * set stroke scale enabled
     * @name setStrokeScaleEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Boolean} enabled
     */

    /**
     * get stroke scale enabled
     * @name getStrokeScaleEnabled
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillPatternOffset', 0);

    /**
     * set fill pattern offset
     * @name setFillPatternOffset
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} offset
     * @example
     * // set x and y<br>
     * shape.setFillPatternOffset(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillPatternOffset({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillPatternOffset([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setFillPatternOffset(5);
     */

    /**
     * set fill pattern offset x
     * @name setFillPatternOffsetX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill pattern offset y
     * @name setFillPatternOffsetY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill pattern offset
     * @name getFillPatternOffset
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill pattern offset x
     * @name getFillPatternOffsetX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill pattern offset y
     * @name getFillPatternOffsetY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillPatternScale', 1);

    /**
     * set fill pattern scale
     * @name setFillPatternScale
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} scale
     * @example
     * // set x and y to the same value<br>
     * shape.setFillPatternScale(5);<br><br>
     *
     * // set x and y<br>
     * shape.setFillPatternScale(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillPatternScale({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillPatternScale([20, 40]);
     */

    /**
     * set fill pattern scale x
     * @name setFillPatternScaleX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill pattern scale y
     * @name setFillPatternScaleY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill pattern scale
     * @name getFillPatternScale
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill pattern scale x
     * @name getFillPatternScaleX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill pattern scale y
     * @name getFillPatternScaleY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillLinearGradientStartPoint', 0);

    /**
     * set fill linear gradient start point
     * @name setFillLinearGradientStartPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} startPoint
     * @example
     * // set x and y<br>
     * shape.setFillLinearGradientStartPoint(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillLinearGradientStartPoint({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillLinearGradientStartPoint([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setFillLinearGradientStartPoint(5);
     */

    /**
     * set fill linear gradient start point x
     * @name setFillLinearGradientStartPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill linear gradient start point y
     * @name setFillLinearGradientStartPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill linear gradient start point
     * @name getFillLinearGradientStartPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill linear gradient start point x
     * @name getFillLinearGradientStartPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill linear gradient start point y
     * @name getFillLinearGradientStartPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillLinearGradientEndPoint', 0);

    /**
     * set fill linear gradient end point
     * @name setFillLinearGradientEndPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} endPoint
     * @example
     * // set x and y<br>
     * shape.setFillLinearGradientEndPoint(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillLinearGradientEndPoint({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillLinearGradientEndPoint([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setFillLinearGradientEndPoint(5);
     */

    /**
     * set fill linear gradient end point x
     * @name setFillLinearGradientEndPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill linear gradient end point y
     * @name setFillLinearGradientEndPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill linear gradient end point
     * @name getFillLinearGradientEndPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill linear gradient end point x
     * @name getFillLinearGradientEndPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill linear gradient end point y
     * @name getFillLinearGradientEndPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillRadialGradientStartPoint', 0);

    /**
     * set fill radial gradient start point
     * @name setFillRadialGradientStartPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} startPoint
     * @example
     * // set x and y<br>
     * shape.setFillRadialGradientStartPoint(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillRadialGradientStartPoint({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillRadialGradientStartPoint([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setFillRadialGradientStartPoint(5);
     */

    /**
     * set fill radial gradient start point x
     * @name setFillRadialGradientStartPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill radial gradient start point y
     * @name setFillRadialGradientStartPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill radial gradient start point
     * @name getFillRadialGradientStartPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill radial gradient start point x
     * @name getFillRadialGradientStartPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill radial gradient start point y
     * @name getFillRadialGradientStartPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'fillRadialGradientEndPoint', 0);

    /**
     * set fill radial gradient end point
     * @name setFillRadialGradientEndPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} endPoint
     * @example
     * // set x and y<br>
     * shape.setFillRadialGradientEndPoint(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setFillRadialGradientEndPoint({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setFillRadialGradientEndPoint([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setFillRadialGradientEndPoint(5);
     */

    /**
     * set fill radial gradient end point x
     * @name setFillRadialGradientEndPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set fill radial gradient end point y
     * @name setFillRadialGradientEndPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get fill radial gradient end point
     * @name getFillRadialGradientEndPoint
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill radial gradient end point x
     * @name getFillRadialGradientEndPointX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill radial gradient end point y
     * @name getFillRadialGradientEndPointY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addPointGetterSetter(Kinetic.Shape, 'shadowOffset', 0);

    /**
     * set shadow offset
     * @name setShadowOffset
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number|Array|Object} offset
     * @example
     * // set x and y<br>
     * shape.setShadowOffset(20, 40);<br><br>
     *
     * // set x only <br>
     * shape.setShadowOffset({<br>
     *   x: 20<br>
     * });<br><br>
     *
     * // set x and y using an array<br>
     * shape.setShadowOffset([20, 40]);<br><br>
     *
     * // set x and y to the same value<br>
     * shape.setShadowOffset(5);
     */

    /**
     * set shadow offset x
     * @name setShadowOffsetX
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} x
     */

    /**
     * set shadow offset y
     * @name setShadowOffsetY
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} y
     */

    /**
     * get shadow offset
     * @name getShadowOffset
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow offset x
     * @name getShadowOffsetX
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get shadow offset y
     * @name getShadowOffsetY
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    Kinetic.Factory.addRotationGetterSetter(Kinetic.Shape, 'fillPatternRotation', 0);

    /**
     * set fill pattern rotation in radians
     * @name setFillPatternRotation
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} rotation
     */

    /**
     * set fill pattern rotation in degrees
     * @name setFillPatternRotationDeg
     * @method
     * @memberof Kinetic.Shape.prototype
     * @param {Number} rotationDeg
     */

    /**
     * get fill pattern rotation in radians
     * @name getFillPatternRotation
     * @method
     * @memberof Kinetic.Shape.prototype
     */

    /**
     * get fill pattern rotation in degrees
     * @name getFillPatternRotationDeg
     * @method
     * @memberof Kinetic.Shape.prototype
     */

})();
;(function() {
    // CONSTANTS
    var STAGE = 'Stage',
        STRING = 'string',
        PX = 'px',

        MOUSEOUT = 'mouseout',
        MOUSELEAVE = 'mouseleave',
        MOUSEOVER = 'mouseover',
        MOUSEENTER = 'mouseenter',
        MOUSEMOVE = 'mousemove',
        MOUSEDOWN = 'mousedown',
        MOUSEUP = 'mouseup',
        CLICK = 'click',
        DBL_CLICK = 'dblclick',
        TOUCHSTART = 'touchstart',
        TOUCHEND = 'touchend',
        TAP = 'tap',
        DBL_TAP = 'dbltap',
        TOUCHMOVE = 'touchmove',

        CONTENT_MOUSEOUT = 'contentMouseout',
        CONTENT_MOUSELEAVE = 'contentMouseleave',
        CONTENT_MOUSEOVER = 'contentMouseover',
        CONTENT_MOUSEENTER = 'contentMouseenter',
        CONTENT_MOUSEMOVE = 'contentMousemove',
        CONTENT_MOUSEDOWN = 'contentMousedown',
        CONTENT_MOUSEUP = 'contentMouseup',
        CONTENT_CLICK = 'contentClick',
        CONTENT_DBL_CLICK = 'contentDblclick',
        CONTENT_TOUCHSTART = 'contentTouchstart',
        CONTENT_TOUCHEND = 'contentTouchend',
        CONTENT_TAP = 'contentTap',
        CONTENT_DBL_TAP = 'contentDbltap',
        CONTENT_TOUCHMOVE = 'contentTouchmove',

        DIV = 'div',
        RELATIVE = 'relative',
        INLINE_BLOCK = 'inline-block',
        KINETICJS_CONTENT = 'kineticjs-content',
        SPACE = ' ',
        UNDERSCORE = '_',
        CONTAINER = 'container',
        EMPTY_STRING = '',
        EVENTS = [MOUSEDOWN, MOUSEMOVE, MOUSEUP, MOUSEOUT, TOUCHSTART, TOUCHMOVE, TOUCHEND, MOUSEOVER],

    // cached variables
        eventsLength = EVENTS.length;

    function addEvent(ctx, eventName) {
        ctx.content.addEventListener(eventName, function(evt) {
            ctx[UNDERSCORE + eventName](evt);
        }, false);
    }

    Kinetic.Util.addMethods(Kinetic.Stage, {
        ___init: function(config) {
            this.nodeType = STAGE;
            // call super constructor
            Kinetic.Container.call(this, config);
            this._id = Kinetic.idCounter++;
            this._buildDOM();
            this._bindContentEvents();
            Kinetic.stages.push(this);
        },
        _validateAdd: function(child) {
            if (child.getType() !== 'Layer') {
                Kinetic.Util.error('You may only add layers to the stage.');
            }
        },
        /**
         * set container dom element which contains the stage wrapper div element
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {DomElement} container can pass in a dom element or id string
         */
        setContainer: function(container) {
            if( typeof container === STRING) {
                container = document.getElementById(container);
            }
            this._setAttr(CONTAINER, container);
            return this;
        },
        draw: function() {
            Kinetic.Node.prototype.draw.call(this);
            return this;
        },
        /**
         * draw layer scene graphs
         * @name draw
         * @method
         * @memberof Kinetic.Stage.prototype
         */

        /**
         * draw layer hit graphs
         * @name drawHit
         * @method
         * @memberof Kinetic.Stage.prototype
         */

        /**
         * set height
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Number} height
         */
        setHeight: function(height) {
            Kinetic.Node.prototype.setHeight.call(this, height);
            this._resizeDOM();
            return this;
        },
        /**
         * set width
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Number} width
         */
        setWidth: function(width) {
            Kinetic.Node.prototype.setWidth.call(this, width);
            this._resizeDOM();
            return this;
        },
        /**
         * clear all layers
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        clear: function() {
            var layers = this.children,
                len = layers.length,
                n;

            for(n = 0; n < len; n++) {
                layers[n].clear();
            }
            return this;
        },
        /**
         * remove stage
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        destroy: function() {
            var content = this.content;
            Kinetic.Container.prototype.destroy.call(this);

            if(content && Kinetic.Util._isInDocument(content)) {
                this.getContainer().removeChild(content);
            }
        },
        /**
         * get mouse position for desktop apps
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        getMousePosition: function() {
            return this.mousePos;
        },
        /**
         * get touch position for mobile apps
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        getTouchPosition: function() {
            return this.touchPos;
        },
        /**
         * get pointer position which can be a touc position or mouse position
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        getPointerPosition: function() {
            return this.getTouchPosition() || this.getMousePosition();
        },
        getStage: function() {
            return this;
        },
        /**
         * get stage content div element which has the
         *  the class name "kineticjs-content"
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        getContent: function() {
            return this.content;
        },
        /**
         * Creates a composite data URL and requires a callback because the composite is generated asynchronously.
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Object} config
         * @param {Function} config.callback function executed when the composite has completed
         * @param {String} [config.mimeType] can be "image/png" or "image/jpeg".
         *  "image/png" is the default
         * @param {Number} [config.x] x position of canvas section
         * @param {Number} [config.y] y position of canvas section
         * @param {Number} [config.width] width of canvas section
         * @param {Number} [config.height] height of canvas section
         * @param {Number} [config.quality] jpeg quality.  If using an "image/jpeg" mimeType,
         *  you can specify the quality from 0 to 1, where 0 is very poor quality and 1
         *  is very high quality
         */
        toDataURL: function(config) {
            config = config || {};

            var mimeType = config.mimeType || null,
                quality = config.quality || null,
                x = config.x || 0,
                y = config.y || 0,
                canvas = new Kinetic.SceneCanvas({
                    width: config.width || this.getWidth(),
                    height: config.height || this.getHeight(),
                    pixelRatio: 1
                }),
                _context = canvas.getContext()._context,
                layers = this.children;

            if(x || y) {
                _context.translate(-1 * x, -1 * y);
            }

            function drawLayer(n) {
                var layer = layers[n],
                    layerUrl = layer.toDataURL(),
                    imageObj = new Image();

                imageObj.onload = function() {
                    _context.drawImage(imageObj, 0, 0);

                    if(n < layers.length - 1) {
                        drawLayer(n + 1);
                    }
                    else {
                        config.callback(canvas.toDataURL(mimeType, quality));
                    }
                };
                imageObj.src = layerUrl;
            }
            drawLayer(0);
        },
        /**
         * converts stage into an image.
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Object} config
         * @param {Function} config.callback function executed when the composite has completed
         * @param {String} [config.mimeType] can be "image/png" or "image/jpeg".
         *  "image/png" is the default
         * @param {Number} [config.x] x position of canvas section
         * @param {Number} [config.y] y position of canvas section
         * @param {Number} [config.width] width of canvas section
         * @param {Number} [config.height] height of canvas section
         * @param {Number} [config.quality] jpeg quality.  If using an "image/jpeg" mimeType,
         *  you can specify the quality from 0 to 1, where 0 is very poor quality and 1
         *  is very high quality
         */
        toImage: function(config) {
            var cb = config.callback;

            config.callback = function(dataUrl) {
                Kinetic.Util._getImage(dataUrl, function(img) {
                    cb(img);
                });
            };
            this.toDataURL(config);
        },
        /**
         * get visible intersection object that contains shape and pixel data. This is the preferred
         *  method for determining if a point intersects a shape or not
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Object} pos point object
         */
        getIntersection: function() {
            var pos = Kinetic.Util._getXY(Array.prototype.slice.call(arguments)),
                layers = this.getChildren(),
                len = layers.length,
                end = len - 1,
                n, obj;

            for(n = end; n >= 0; n--) {
                obj = layers[n].getIntersection(pos);
                if (obj) {
                    return obj;
                }
            }

            return null;
        },
        _resizeDOM: function() {
            if(this.content) {
                var width = this.getWidth(),
                    height = this.getHeight(),
                    layers = this.getChildren(),
                    len = layers.length,
                    n, layer;

                // set content dimensions
                this.content.style.width = width + PX;
                this.content.style.height = height + PX;

                this.bufferCanvas.setSize(width, height);
                this.bufferHitCanvas.setSize(width, height);

                // set layer dimensions
                for(n = 0; n < len; n++) {
                    layer = layers[n];
                    layer.getCanvas().setSize(width, height);
                    layer.hitCanvas.setSize(width, height);
                    layer.draw();
                }
            }
        },
        /**
         * add layer to stage
         * @method
         * @memberof Kinetic.Stage.prototype
         * @param {Kinetic.Layer} layer
         */
        add: function(layer) {
            Kinetic.Container.prototype.add.call(this, layer);
            layer.canvas.setSize(this.attrs.width, this.attrs.height);
            layer.hitCanvas.setSize(this.attrs.width, this.attrs.height);

            // draw layer and append canvas to container
            layer.draw();
            this.content.appendChild(layer.canvas._canvas);

            // chainable
            return this;
        },
        getParent: function() {
            return null;
        },
        getLayer: function() {
            return null;
        },
        /**
         * returns a {@link Kinetic.Collection} of layers
         * @method
         * @memberof Kinetic.Stage.prototype
         */
        getLayers: function() {
            return this.getChildren();
        },
        _setPointerPosition: function(evt) {
            if(!evt) {
                evt = window.event;
            }
            this._setMousePosition(evt);
            this._setTouchPosition(evt);
        },
        _bindContentEvents: function() {
            var that = this,
                n;

            for (n = 0; n < eventsLength; n++) {
                addEvent(this, EVENTS[n]);
            }
        },
        _mouseover: function(evt) {
            this._fire(CONTENT_MOUSEOVER, evt);
        },
        _mouseout: function(evt) {
            this._setPointerPosition(evt);
            var targetShape = this.targetShape;

            if(targetShape && !Kinetic.isDragging()) {
                targetShape._fireAndBubble(MOUSEOUT, evt);
                targetShape._fireAndBubble(MOUSELEAVE, evt);
                this.targetShape = null;
            }
            this.mousePos = undefined;

            this._fire(CONTENT_MOUSEOUT, evt);
        },
        _mousemove: function(evt) {
            this._setPointerPosition(evt);
            var dd = Kinetic.DD,
                obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined;

            if(shape) {
                if(!Kinetic.isDragging() && obj.pixel[3] === 255 && (!this.targetShape || this.targetShape._id !== shape._id)) {
                    if(this.targetShape) {
                        this.targetShape._fireAndBubble(MOUSEOUT, evt, shape);
                        this.targetShape._fireAndBubble(MOUSELEAVE, evt, shape);
                    }
                    shape._fireAndBubble(MOUSEOVER, evt, this.targetShape);
                    shape._fireAndBubble(MOUSEENTER, evt, this.targetShape);
                    this.targetShape = shape;
                }
                else {
                    shape._fireAndBubble(MOUSEMOVE, evt);
                }
            }
            /*
             * if no shape was detected, clear target shape and try
             * to run mouseout from previous target shape
             */
            else {
                if(this.targetShape && !Kinetic.isDragging()) {
                    this.targetShape._fireAndBubble(MOUSEOUT, evt);
                    this.targetShape._fireAndBubble(MOUSELEAVE, evt);
                    this.targetShape = null;
                }

            }

            // content event
            this._fire(CONTENT_MOUSEMOVE, evt);

            if(dd) {
                dd._drag(evt);
            }

            // always call preventDefault for desktop events because some browsers
            // try to drag and drop the canvas element
            if (evt.preventDefault) {
                evt.preventDefault();
            }
        },
        _mousedown: function(evt) {
            this._setPointerPosition(evt);
            var obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined;

            Kinetic.listenClickTap = true;

            if (shape) {
                this.clickStartShape = shape;
                shape._fireAndBubble(MOUSEDOWN, evt);
            }

            // content event
            this._fire(CONTENT_MOUSEDOWN);

            // always call preventDefault for desktop events because some browsers
            // try to drag and drop the canvas element
            if (evt.preventDefault) {
                evt.preventDefault();
            }
        },
        _mouseup: function(evt) {
            this._setPointerPosition(evt);
            var that = this,
                obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined,
                fireDblClick = false;

            if(Kinetic.inDblClickWindow) {
                fireDblClick = true;
                Kinetic.inDblClickWindow = false;
            }
            else {
                Kinetic.inDblClickWindow = true;
            }

            setTimeout(function() {
                Kinetic.inDblClickWindow = false;
            }, Kinetic.dblClickWindow);

            if (shape) {
                shape._fireAndBubble(MOUSEUP, evt);

                // detect if click or double click occurred
                if(Kinetic.listenClickTap && shape._id === this.clickStartShape._id) {
                    shape._fireAndBubble(CLICK, evt);

                    if(fireDblClick) {
                        shape._fireAndBubble(DBL_CLICK, evt);
                    }
                }
            }
            // content events
            this._fire(CONTENT_MOUSEUP);
            if (Kinetic.listenClickTap) {
                this._fire(CONTENT_CLICK, evt);
                if(fireDblClick) {
                    this._fire(CONTENT_DBL_CLICK, evt);
                }
            }

            Kinetic.listenClickTap = false;

            // always call preventDefault for desktop events because some browsers
            // try to drag and drop the canvas element
            if (evt.preventDefault) {
                evt.preventDefault();
            }
        },
        _touchstart: function(evt) {
            this._setPointerPosition(evt);
            var obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined;

            Kinetic.listenClickTap = true;

            if (shape) {
                this.tapStartShape = shape;
                shape._fireAndBubble(TOUCHSTART, evt);

                // only call preventDefault if the shape is listening for events
                if (shape.isListening() && evt.preventDefault) {
                    evt.preventDefault();
                }
            }
            // content event
            this._fire(CONTENT_TOUCHSTART, evt);
        },
        _touchend: function(evt) {
            this._setPointerPosition(evt);
            var that = this,
                obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined,
                fireDblClick = false;

            if(Kinetic.inDblClickWindow) {
                fireDblClick = true;
                Kinetic.inDblClickWindow = false;
            }
            else {
                Kinetic.inDblClickWindow = true;
            }

            setTimeout(function() {
                Kinetic.inDblClickWindow = false;
            }, Kinetic.dblClickWindow);

            if (shape) {
                shape._fireAndBubble(TOUCHEND, evt);

                // detect if tap or double tap occurred
                if(Kinetic.listenClickTap && shape._id === this.tapStartShape._id) {
                    shape._fireAndBubble(TAP, evt);

                    if(fireDblClick) {
                        shape._fireAndBubble(DBL_TAP, evt);
                    }
                }
                // only call preventDefault if the shape is listening for events
                if (shape.isListening() && evt.preventDefault) {
                    evt.preventDefault();
                }
            }
            // content events
            if (Kinetic.listenClickTap) {
                this._fire(CONTENT_TOUCHEND, evt);
                if(fireDblClick) {
                    this._fire(CONTENT_DBL_TAP, evt);
                }
            }

            Kinetic.listenClickTap = false;
        },
        _touchmove: function(evt) {
            this._setPointerPosition(evt);
            var dd = Kinetic.DD,
                obj = this.getIntersection(this.getPointerPosition()),
                shape = obj && obj.shape ? obj.shape : undefined;

            if (shape) {
                shape._fireAndBubble(TOUCHMOVE, evt);

                // only call preventDefault if the shape is listening for events
                if (shape.isListening() && evt.preventDefault) {
                    evt.preventDefault();
                }
            }
            this._fire(CONTENT_TOUCHMOVE, evt);

            // start drag and drop
            if(dd) {
                dd._drag(evt);
            }
        },
        _setMousePosition: function(evt) {
            var contentPosition = this._getContentPosition(),
                offsetX = evt.offsetX,
                clientX = evt.clientX,
                mouseX = 0,
                mouseY = 0;

            // if offsetX is defined, assume that offsetY is defined as well
            if (offsetX !== undefined) {
                mouseX = offsetX;
                mouseY = evt.offsetY;
            }
            // we unforunately have to use UA detection here because accessing
            // the layerX or layerY properties in newer veresions of Chrome
            // throws a JS warning.  layerX and layerY are required for FF
            // when the container is transformed via CSS.
            else if (Kinetic.UA.browser === 'mozilla') {
                mouseX = evt.layerX;
                mouseY = evt.layerY;
            }
            // if clientX is defined, assume that clientY is defined as well
            else if (clientX !== undefined && contentPosition) {
                mouseX = clientX - contentPosition.left;
                mouseY = evt.clientY - contentPosition.top;
            }


            this.mousePos = {
                x: mouseX,
                y: mouseY
            };
        },
        _setTouchPosition: function(evt) {
            var contentPosition = this._getContentPosition(),
                touch, touchX, touchY;

            if(evt.touches !== undefined && evt.touches.length === 1) {
                // one finger
                touch = evt.touches[0];

                // get the information for finger #1
                touchX = touch.clientX - contentPosition.left;
                touchY = touch.clientY - contentPosition.top;

                this.touchPos = {
                    x: touchX,
                    y: touchY
                };
            }
        },
        _getContentPosition: function() {
            var rect = this.content.getBoundingClientRect();
            return {
                top: rect.top,
                left: rect.left
            };
        },
        _buildDOM: function() {
            var container = this.getContainer();

            // clear content inside container
            container.innerHTML = EMPTY_STRING;

            // content
            this.content = document.createElement(DIV);
            this.content.style.position = RELATIVE;
            this.content.style.display = INLINE_BLOCK;
            this.content.className = KINETICJS_CONTENT;
            container.appendChild(this.content);

            this.bufferCanvas = new Kinetic.SceneCanvas();
            this.bufferHitCanvas = new Kinetic.HitCanvas();

            this._resizeDOM();
        },
        _onContent: function(typesStr, handler) {
            var types = typesStr.split(SPACE),
                len = types.length,
                n, baseEvent;

            for(n = 0; n < len; n++) {
                baseEvent = types[n];
                this.content.addEventListener(baseEvent, handler, false);
            }
        }
    });
    Kinetic.Util.extend(Kinetic.Stage, Kinetic.Container);

    // add getters and setters
    Kinetic.Factory.addGetter(Kinetic.Stage, 'container');

    /**
     * get container DOM element
     * @name getContainer
     * @method
     * @memberof Kinetic.Stage.prototype
     */
})();
;(function() {
    // constants
    var HASH = '#',
        BEFORE_DRAW ='beforeDraw',
        DRAW = 'draw';

    Kinetic.Util.addMethods(Kinetic.Layer, {
        ___init: function(config) {
            this.nodeType = 'Layer';
            this.canvas = new Kinetic.SceneCanvas();
            this.hitCanvas = new Kinetic.HitCanvas();
            // call super constructor
            Kinetic.Container.call(this, config);
        },
        _validateAdd: function(child) {
            var type = child.getType();
            if (type !== 'Group' && type !== 'Shape') {
                Kinetic.Util.error('You may only add groups and shapes to a layer.');
            }
        },
        /**
         * get visible intersection object that contains shape and pixel data. This is the preferred
         * method for determining if a point intersects a shape or not
         * @method
         * @memberof Kinetic.Layer.prototype
         * @param {Object} pos point object
         */
        getIntersection: function() {
            var pos = Kinetic.Util._getXY(Array.prototype.slice.call(arguments)),
                p, colorKey, shape;

            if(this.isVisible() && this.isListening()) {
                p = this.hitCanvas.context._context.getImageData(pos.x | 0, pos.y | 0, 1, 1).data;
                // this indicates that a hit pixel may have been found
                if(p[3] === 255) {
                    colorKey = Kinetic.Util._rgbToHex(p[0], p[1], p[2]);
                    shape = Kinetic.shapes[HASH + colorKey];
                    return {
                        shape: shape,
                        pixel: p
                    };
                }
                // if no shape mapped to that pixel, return pixel array
                else if(p[0] > 0 || p[1] > 0 || p[2] > 0 || p[3] > 0) {
                    return {
                        pixel: p
                    };
                }
            }

            return null;
        },
        drawScene: function(canvas) {
            canvas = canvas || this.getCanvas();

            this._fire(BEFORE_DRAW, {
                node: this
            });

            if(this.getClearBeforeDraw()) {
                canvas.getContext().clear();
            }

            Kinetic.Container.prototype.drawScene.call(this, canvas);

            this._fire(DRAW, {
                node: this
            });

            return this;
        },
        drawHit: function() {
            var layer = this.getLayer();

            if(layer && layer.getClearBeforeDraw()) {
                layer.getHitCanvas().getContext().clear();
            }

            Kinetic.Container.prototype.drawHit.call(this);
            return this;
        },
        /**
         * get layer canvas
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getCanvas: function() {
            return this.canvas;
        },
        /**
         * get layer hit canvas
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getHitCanvas: function() {
            return this.hitCanvas;
        },
        /**
         * get layer canvas context
         * @method
         * @memberof Kinetic.Node.prototype
         */
        getContext: function() {
            return this.getCanvas().getContext();
        },
        /**
         * clear scene and hit canvas contexts tied to the layer
         * @method
         * @memberof Kinetic.Node.prototype
         * @param {Array|Object} [bounds]
         * @example
         * layer.clear();<br>
         * layer.clear(0, 0, 100, 100);
         */
        clear: function() {
            var context = this.getContext(),
                hitContext = this.getHitCanvas().getContext();

            context.clear.apply(context, arguments);
            hitContext.clear.apply(hitContext, arguments);
            return this;
        },
        // extend Node.prototype.setVisible
        setVisible: function(visible) {
            Kinetic.Node.prototype.setVisible.call(this, visible);
            if(visible) {
                this.getCanvas()._canvas.style.display = 'block';
                this.hitCanvas._canvas.style.display = 'block';
            }
            else {
                this.getCanvas()._canvas.style.display = 'none';
                this.hitCanvas._canvas.style.display = 'none';
            }
            return this;
        },
        // extend Node.prototype.setZIndex
        setZIndex: function(index) {
            Kinetic.Node.prototype.setZIndex.call(this, index);
            var stage = this.getStage();
            if(stage) {
                stage.content.removeChild(this.getCanvas()._canvas);

                if(index < stage.getChildren().length - 1) {
                    stage.content.insertBefore(this.getCanvas()._canvas, stage.getChildren()[index + 1].getCanvas()._canvas);
                }
                else {
                    stage.content.appendChild(this.getCanvas()._canvas);
                }
            }
            return this;
        },
        // extend Node.prototype.moveToTop
        moveToTop: function() {
            Kinetic.Node.prototype.moveToTop.call(this);
            var stage = this.getStage();
            if(stage) {
                stage.content.removeChild(this.getCanvas()._canvas);
                stage.content.appendChild(this.getCanvas()._canvas);
            }
        },
        // extend Node.prototype.moveUp
        moveUp: function() {
            if(Kinetic.Node.prototype.moveUp.call(this)) {
                var stage = this.getStage();
                if(stage) {
                    stage.content.removeChild(this.getCanvas()._canvas);

                    if(this.index < stage.getChildren().length - 1) {
                        stage.content.insertBefore(this.getCanvas()._canvas, stage.getChildren()[this.index + 1].getCanvas()._canvas);
                    }
                    else {
                        stage.content.appendChild(this.getCanvas()._canvas);
                    }
                }
            }
        },
        // extend Node.prototype.moveDown
        moveDown: function() {
            if(Kinetic.Node.prototype.moveDown.call(this)) {
                var stage = this.getStage();
                if(stage) {
                    var children = stage.getChildren();
                    stage.content.removeChild(this.getCanvas()._canvas);
                    stage.content.insertBefore(this.getCanvas()._canvas, children[this.index + 1].getCanvas()._canvas);
                }
            }
        },
        // extend Node.prototype.moveToBottom
        moveToBottom: function() {
            if(Kinetic.Node.prototype.moveToBottom.call(this)) {
                var stage = this.getStage();
                if(stage) {
                    var children = stage.getChildren();
                    stage.content.removeChild(this.getCanvas()._canvas);
                    stage.content.insertBefore(this.getCanvas()._canvas, children[1].getCanvas()._canvas);
                }
            }
        },
        getLayer: function() {
            return this;
        },
        remove: function() {
            var stage = this.getStage(),
                canvas = this.getCanvas(),
                _canvas = canvas._canvas;

            Kinetic.Node.prototype.remove.call(this);

            if(stage && _canvas && Kinetic.Util._isInDocument(_canvas)) {
                stage.content.removeChild(_canvas);
            }
            return this;
        },
        getStage: function() {
            return this.parent;
        }
    });
    Kinetic.Util.extend(Kinetic.Layer, Kinetic.Container);

    // add getters and setters
    Kinetic.Factory.addGetterSetter(Kinetic.Layer, 'clearBeforeDraw', true);

    /**
     * set flag which determines if the layer is cleared or not
     *  before drawing
     * @name setClearBeforeDraw
     * @method
     * @memberof Kinetic.Node.prototype
     * @param {Boolean} clearBeforeDraw
     */

    /**
     * get flag which determines if the layer is cleared or not
     *  before drawing
     * @name getClearBeforeDraw
     * @method
     * @memberof Kinetic.Node.prototype
     */
})();
;(function() {
    Kinetic.Util.addMethods(Kinetic.Group, {
        ___init: function(config) {
            this.nodeType = 'Group';
            // call super constructor
            Kinetic.Container.call(this, config);
        },
        _validateAdd: function(child) {
            var type = child.getType();
            if (type !== 'Group' && type !== 'Shape') {
                Kinetic.Util.error('You may only add groups and shapes to groups.');
            }
        },
    });
    Kinetic.Util.extend(Kinetic.Group, Kinetic.Container);
})();
;(function() {
    /**
     * Rect constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Number} [config.cornerRadius]
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var rect = new Kinetic.Rect({<br>
     *   width: 100,<br>
     *   height: 50,<br>
     *   fill: 'red',<br>
     *   stroke: 'black'<br>
     *   strokeWidth: 5<br>
     * });
     */
    Kinetic.Rect = function(config) {
        this.___init(config);
    };

    Kinetic.Rect.prototype = {
        ___init: function(config) {
            Kinetic.Shape.call(this, config);
            this.className = 'Rect';
        },
        drawFunc: function(context) {
            var cornerRadius = this.getCornerRadius(),
                width = this.getWidth(),
                height = this.getHeight();

            context.beginPath();

            if(!cornerRadius) {
                // simple rect - don't bother doing all that complicated maths stuff.
                context.rect(0, 0, width, height);
            }
            else {
                // arcTo would be nicer, but browser support is patchy (Opera)
                context.moveTo(cornerRadius, 0);
                context.lineTo(width - cornerRadius, 0);
                context.arc(width - cornerRadius, cornerRadius, cornerRadius, Math.PI * 3 / 2, 0, false);
                context.lineTo(width, height - cornerRadius);
                context.arc(width - cornerRadius, height - cornerRadius, cornerRadius, 0, Math.PI / 2, false);
                context.lineTo(cornerRadius, height);
                context.arc(cornerRadius, height - cornerRadius, cornerRadius, Math.PI / 2, Math.PI, false);
                context.lineTo(0, cornerRadius);
                context.arc(cornerRadius, cornerRadius, cornerRadius, Math.PI, Math.PI * 3 / 2, false);
            }
            context.closePath();
            context.fillStrokeShape(this);
        }
    };

    Kinetic.Util.extend(Kinetic.Rect, Kinetic.Shape);

    Kinetic.Factory.addGetterSetter(Kinetic.Rect, 'cornerRadius', 0);

    /**
     * set corner radius
     * @name setCornerRadius
     * @method
     * @memberof Kinetic.Rect.prototype
     * @param {Number} corner radius
     */

    /**
     * get corner radius
     * @name getCornerRadius
     * @method
     * @memberof Kinetic.Rect.prototype
     * @returns {Number}
     */

})();
;(function() {
    // the 0.0001 offset fixes a bug in Chrome 27
    var PIx2 = (Math.PI * 2) - 0.0001,
        CIRCLE = 'Circle';

    /**
     * Circle constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Number} config.radius
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * // create simple circle
     * var circle = new Kinetic.Circle({<br>
     *   radius: 40,<br>
     *   fill: 'red',<br>
     *   stroke: 'black'<br>
     *   strokeWidth: 5<br>
     * });<br><br>
     *
     * // create ellipse<br>
     * var circle = new Kinetic.Circle({<br>
     *   radius: 5,<br>
     *   fill: 'red',<br>
     *   stroke: 'black'<br>
     *   strokeWidth: 5,<br>
     *   scaleX: 2,<br>
     *   strokeScaleEnabled: false<br>
     * });
     */
    Kinetic.Circle = function(config) {
        this.___init(config);
    };

    Kinetic.Circle.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = CIRCLE;
        },
        drawFunc: function(context) {
            context.beginPath();
            context.arc(0, 0, this.getRadius(), 0, PIx2, false);
            context.closePath();
            context.fillStrokeShape(this);
        },
        // implements Shape.prototype.getWidth()
        getWidth: function() {
            return this.getRadius() * 2;
        },
        // implements Shape.prototype.getHeight()
        getHeight: function() {
            return this.getRadius() * 2;
        },
        // implements Shape.prototype.setWidth()
        setWidth: function(width) {
            Kinetic.Node.prototype.setWidth.call(this, width);
            this.setRadius(width / 2);
        },
        // implements Shape.prototype.setHeight()
        setHeight: function(height) {
            Kinetic.Node.prototype.setHeight.call(this, height);
            this.setRadius(height / 2);
        }
    };
    Kinetic.Util.extend(Kinetic.Circle, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Circle, 'radius', 0);

    /**
     * set radius
     * @name setRadius
     * @method
     * @memberof Kinetic.Circle.prototype
     * @param {Number} radius
     */

    /**
     * get radius
     * @name getRadius
     * @method
     * @memberof Kinetic.Circle.prototype
     * @returns {Number}
     */
})();
;(function() {
    // the 0.0001 offset fixes a bug in Chrome 27
    var PIx2 = (Math.PI * 2) - 0.0001,
        ELLIPSE = 'Ellipse';

    /**
     * Ellipse constructor
     * @constructor
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Number|Array|Object} config.radius defines x and y radius
     * @@ShapeParams
     * @@NodeParams
     */
    Kinetic.Ellipse = function(config) {
        this.___init(config);
    };

    Kinetic.Ellipse.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = ELLIPSE;
        },
        drawFunc: function(context) {
            var r = this.getRadius();

            context.beginPath();
            context.save();
            if(r.x !== r.y) {
                context.scale(1, r.y / r.x);
            }
            context.arc(0, 0, r.x, 0, PIx2, false);
            context.restore();
            context.closePath();
            context.fillStrokeShape(this);
        },
        // implements Shape.prototype.getWidth()
        getWidth: function() {
            return this.getRadius().x * 2;
        },
        // implements Shape.prototype.getHeight()
        getHeight: function() {
            return this.getRadius().y * 2;
        },
        // implements Shape.prototype.setWidth()
        setWidth: function(width) {
            Kinetic.Node.prototype.setWidth.call(this, width);
            this.setRadius({
                x: width / 2
            });
        },
        // implements Shape.prototype.setHeight()
        setHeight: function(height) {
            Kinetic.Node.prototype.setHeight.call(this, height);
            this.setRadius({
                y: height / 2
            });
        }
    };
    Kinetic.Util.extend(Kinetic.Ellipse, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addPointGetterSetter(Kinetic.Ellipse, 'radius', 0);

    /**
     * set radius
     * @name setRadius
     * @method
     * @memberof Kinetic.Ellipse.prototype
     * @param {Object|Array} radius
     *  radius can be a number, in which the ellipse becomes a circle,
     *  it can be an object with an x and y component, or it
     *  can be an array in which the first element is the x component
     *  and the second element is the y component.  The x component
     *  defines the horizontal radius and the y component
     *  defines the vertical radius
     */

    /**
     * get radius
     * @name getRadius
     * @method
     * @memberof Kinetic.Ellipse.prototype
     * @returns {Object}
     */
})();;(function() {
    /**
     * Wedge constructor
     * @constructor
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Number} config.angle
     * @param {Number} config.angleDeg angle in degrees
     * @param {Number} config.radius
     * @param {Boolean} [config.clockwise]
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * // draw a wedge that's pointing downwards<br>
     * var wedge = new Kinetic.Wedge({<br>
     *   radius: 40,<br>
     *   fill: 'red',<br>
     *   stroke: 'black'<br>
     *   strokeWidth: 5,<br>
     *   angleDeg: 60,<br>
     *   rotationDeg: -120<br>
     * });
     */
    Kinetic.Wedge = function(config) {
        this.___init(config);
    };

    Kinetic.Wedge.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Wedge';
        },
        drawFunc: function(context) {
            context.beginPath();
            context.arc(0, 0, this.getRadius(), 0, this.getAngle(), this.getClockwise());
            context.lineTo(0, 0);
            context.closePath();
            context.fillStrokeShape(this);
        }
    };
    Kinetic.Util.extend(Kinetic.Wedge, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Wedge, 'radius', 0);

    /**
     * set radius
     * @name setRadius
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @param {Number} radius
     */

    /**
     * get radius
     * @name getRadius
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addRotationGetterSetter(Kinetic.Wedge, 'angle', 0);

    /**
     * set angle
     * @name setAngle
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @param {Number} angle
     */

    /**
     * set angle in degrees
     * @name setAngleDeg
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @param {Number} angleDeg
     */

    /**
     * get angle
     * @name getAngle
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @returns {Number}
     */

    /**
     * get angle in degrees
     * @name getAngleDeg
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Wedge, 'clockwise', false);

    /**
     * set clockwise draw direction.  If set to true, the wedge will be drawn clockwise
     *  If set to false, the wedge will be drawn anti-clockwise.  The default is false.
     * @name setClockwise
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @param {Boolean} clockwise
     */

    /**
     * get clockwise
     * @name getClockwise
     * @method
     * @memberof Kinetic.Wedge.prototype
     * @returns {Boolean}
     */
})();
;(function() {

    // CONSTANTS
    var IMAGE = 'Image',
        CROP = 'crop',
        SET = 'set';

    /**
     * Image constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {ImageObject} config.image
     * @param {Object} [config.crop]
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var imageObj = new Image();<br>
     * imageObj.onload = function() {<br>
     *   var image = new Kinetic.Image({<br>
     *     x: 200,<br>
     *     y: 50,<br>
     *     image: imageObj,<br>
     *     width: 100,<br>
     *     height: 100<br>
     *   });<br>
     * };<br>
     * imageObj.src = '/path/to/image.jpg'
     */
    Kinetic.Image = function(config) {
        this.___init(config);
    };

    Kinetic.Image.prototype = {
        ___init: function(config) {
            var that = this;

            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = IMAGE;
        },
        _useBufferCanvas: function() {
            return (this.hasShadow() || this.getAbsoluteOpacity() !== 1) && this.hasStroke();
        },
        drawFunc: function(context) {
            var width = this.getWidth(),
                height = this.getHeight(),
                that = this,
                crop,
                params,
                image;

            //TODO: this logic needs to hook int othe new caching system

            // if a filter is set, and the filter needs to be updated, reapply
            if (this.getFilter() && this._applyFilter) {
                this.applyFilter();
                this._applyFilter = false;
            }

            // NOTE: this.filterCanvas may be set by the above code block
            // In that case, cropping is already applied.
            if (this.filterCanvas) {
                image = this.filterCanvas._canvas;
                params = [image, 0, 0, width, height];
            }
            else {
                image = this.getImage();

                if (image) {
                    crop = this.getCrop();
                    if (crop) {
                        crop.x = crop.x || 0;
                        crop.y = crop.y || 0;
                        crop.width = crop.width || image.width - crop.x;
                        crop.height = crop.height || image.height - crop.y;
                        params = [image, crop.x, crop.y, crop.width, crop.height, 0, 0, width, height];
                    } else {
                        params = [image, 0, 0, width, height];
                    }
                }
            }

            context.beginPath();
            context.rect(0, 0, width, height);
            context.closePath();
            context.fillStrokeShape(this);

            if (image) {
                context.drawImage.apply(context, params);
            }
        },
        drawHitFunc: function(context) {
            var width = this.getWidth(),
                height = this.getHeight(),
                imageHitRegion = this.imageHitRegion;

            if(imageHitRegion) {
                context.drawImage(imageHitRegion, 0, 0, width, height);
                context.beginPath();
                context.rect(0, 0, width, height);
                context.closePath();
                context.strokeShape(this);
            }
            else {
                context.beginPath();
                context.rect(0, 0, width, height);
                context.closePath();
                context.fillStrokeShape(this);
            }
        },
        applyFilter: function() {
            var image = this.getImage(),
                that = this,
                width = this.getWidth(),
                height = this.getHeight(),
                filter = this.getFilter(),
                crop = this.getCrop() || {},
                filterCanvas, context, imageData;

            // Determine the region we are cropping
            crop.x = crop.x || 0;
            crop.y = crop.y || 0;
            crop.width = crop.width || image.width - crop.x;
            crop.height = crop.height || image.height - crop.y;

            // Make a filterCanvas the same size as the cropped image
            if (this.filterCanvas &&
                this.filterCanvas.getWidth() === crop.width &&
                this.filterCanvas.getHeight() === crop.height) {
                filterCanvas = this.filterCanvas;
                filterCanvas.getContext().clear();
            }
            else {
                filterCanvas = this.filterCanvas = new Kinetic.SceneCanvas({
                    width: crop.width,
                    height: crop.height,
                    pixelRatio: 1,
                });
            }

            context = filterCanvas.getContext();

            try {
                // Crop the image onto the filterCanvas then apply
                // the filter to the filterCanvas
                context.drawImage(image, crop.x, crop.y, crop.width, crop.height, 0,0,crop.width, crop.height);
                imageData = context.getImageData(0, 0, crop.width, crop.height);
                filter.call(this, imageData);
                context.putImageData(imageData, 0, 0);
            }
            catch(e) {
                this.clearFilter();
                Kinetic.Util.warn('Unable to apply filter. ' + e.message);
            }
        },
        /**
         * clear filter
         * @method
         * @memberof Kinetic.Image.prototype
         */
        clearFilter: function() {
            this.filterCanvas = null;
            this._applyFilter = false;
        },
        /**
         * create image hit region which enables more accurate hit detection mapping of the image
         *  by avoiding event detections for transparent pixels
         * @method
         * @memberof Kinetic.Image.prototype
         * @param {Function} [callback] callback function to be called once
         *  the image hit region has been created
         * @example
         * image.createImageHitRegion(function() {<br>
         *   layer.drawHit();<br>
         * });
         */
        createImageHitRegion: function(callback) {
            var that = this,
                width = this.getWidth(),
                height = this.getHeight(),
                canvas = new Kinetic.SceneCanvas({
                    width: width,
                    height: height
                }),
                _context = canvas.getContext()._context,
                image = this.getImage(),
                imageData, data, rgbColorKey, i, len;

            _context.drawImage(image, 0, 0);

            try {
                imageData = _context.getImageData(0, 0, width, height);
                data = imageData.data;
                len = data.length;
                rgbColorKey = Kinetic.Util._hexToRgb(this.colorKey);

                // replace non transparent pixels with color key
                for(i = 0; i < len; i += 4) {
                    if (data[i + 3] > 0) {
                        data[i] = rgbColorKey.r;
                        data[i + 1] = rgbColorKey.g;
                        data[i + 2] = rgbColorKey.b;
                    }
                }

                Kinetic.Util._getImage(imageData, function(imageObj) {
                    that.imageHitRegion = imageObj;
                    if(callback) {
                        callback();
                    }
                });
            }
            catch(e) {
                Kinetic.Util.warn('Unable to create image hit region. ' + e.message);
            }
        },
        /**
         * clear image hit region
         * @method
         * @memberof Kinetic.Image.prototype
         */
        clearImageHitRegion: function() {
            delete this.imageHitRegion;
        },
        getWidth: function() {
            var image = this.getImage();
            return this.attrs.width || (image ? image.width : 0);
        },
        getHeight: function() {
            var image = this.getImage();
            return this.attrs.height || (image ? image.height : 0);
        }
    };
    Kinetic.Util.extend(Kinetic.Image, Kinetic.Shape);


    Kinetic.Factory.addFilterGetterSetter = function(constructor, attr, def) {
        this.addGetter(constructor, attr, def);
        this.addFilterSetter(constructor, attr);
    };

    Kinetic.Factory.addFilterSetter = function(constructor, attr) {
        var that = this,
            method = SET + Kinetic.Util._capitalize(attr);

        constructor.prototype[method] = function(val) {
            this._setAttr(attr, val);
            this._applyFilter = true;
        };
    };

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Image, 'image');

    /**
     * set image
     * @name setImage
     * @method
     * @memberof Kinetic.Image.prototype
     * @param {ImageObject} image
     */

    /**
     * get image
     * @name getImage
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {ImageObject}
     */

    Kinetic.Factory.addBoxGetterSetter(Kinetic.Image, 'crop');
    /**
     * set crop
     * @method
     * @name setCrop
     * @memberof Kinetic.Image.prototype
     * @param {Object|Array}
     * @example
     * // set crop x, y, width and height with an array<br>
     * image.setCrop([20, 20, 100, 100]);<br><br>
     *
     * // set crop x, y, width and height with an object<br>
     * image.setCrop({<br>
     *   x: 20,<br>
     *   y: 20,<br>
     *   width: 20,<br>
     *   height: 20<br>
     * });
     */

    /**
     * set cropX
     * @method
     * @name setCropX
     * @memberof Kinetic.Image.prototype
     * @param {Number} x
     */

    /**
     * set cropY
     * @name setCropY
     * @method
     * @memberof Kinetic.Image.prototype
     * @param {Number} y
     */

    /**
     * set cropWidth
     * @name setCropWidth
     * @method
     * @memberof Kinetic.Image.prototype
     * @param {Number} width
     */

    /**
     * set cropHeight
     * @name setCropHeight
     * @method
     * @memberof Kinetic.Image.prototype
     * @param {Number} height
     */

    /**
     * get crop
     * @name getCrop
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Object}
     */

    /**
     * get crop x
     * @name getCropX
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Number}
     */

    /**
     * get crop y
     * @name getCropY
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Number}
     */

    /**
     * get crop width
     * @name getCropWidth
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Number}
     */

    /**
     * get crop height
     * @name getCropHeight
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filter');

    /**
     * set filter
     * @name setFilter
     * @method
     * @memberof Kinetic.Image.prototype
     * @param {Function} filter
     */

    /**
     * get filter
     * @name getFilter
     * @method
     * @memberof Kinetic.Image.prototype
     * @returns {Function}
     */
})();
;(function() {
    /**
     * Polygon constructor.&nbsp; Polygons are defined by an array of points
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Array} config.points can be a flattened array of points, an array of point arrays, or an array of point objects.
     *  e.g. [0,1,2,3], [[0,1],[2,3]] and [{x:0,y:1},{x:2,y:3}] are equivalent
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var polygon = new Kinetic.Polygon({<br>
     *   points: [73, 192, 73, 160, 340, 23, 500, 109, 499, 139, 342, 93],<br>
     *   fill: '#00D2FF',<br>
     *   stroke: 'black',<br>
     *   strokeWidth: 5<br>
     * });
     */
    Kinetic.Polygon = function(config) {
        this.___init(config);
    };

    Kinetic.Polygon.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Polygon';
        },
        drawFunc: function(context) {
            var points = this.getPoints(),
                length = points.length;

            context.beginPath();
            context.moveTo(points[0].x, points[0].y);
            for(var n = 1; n < length; n++) {
                context.lineTo(points[n].x, points[n].y);
            }
            context.closePath();
            context.fillStrokeShape(this);
        }
    };
    Kinetic.Util.extend(Kinetic.Polygon, Kinetic.Shape);

    Kinetic.Factory.addPointsGetterSetter(Kinetic.Polygon, 'points');
    /**
     * set points array
     * @name setPoints
     * @method
     * @memberof Kinetic.Polygon.prototype
     * @param {Array} can be an array of point objects or an array
     *  of Numbers.  e.g. [{x:1,y:2},{x:3,y:4}] or [1,2,3,4]
     */

    /**
     * get points array
     * @name getPoints
     * @method
     * @memberof Kinetic.Polygon.prototype
     * @returns {Array}
     */
})();
;(function() {
    // constants
    var AUTO = 'auto',
        CALIBRI = 'Calibri',
        CANVAS = 'canvas',
        CENTER = 'center',
        CHANGE_KINETIC = 'Change.kinetic',
        CONTEXT_2D = '2d',
        DASH = '-',
        EMPTY_STRING = '',
        LEFT = 'left',
        NEW_LINE = '\n',
        TEXT = 'text',
        TEXT_UPPER = 'Text',
        TOP = 'top',
        MIDDLE = 'middle',
        NORMAL = 'normal',
        PX_SPACE = 'px ',
        SPACE = ' ',
        RIGHT = 'right',
        WORD = 'word',
        CHAR = 'char',
        NONE = 'none',
        ATTR_CHANGE_LIST = ['fontFamily', 'fontSize', 'fontStyle', 'padding', 'align', 'lineHeight', 'text', 'width', 'height', 'wrap'],

    // cached variables
        attrChangeListLen = ATTR_CHANGE_LIST.length,
        dummyContext = document.createElement(CANVAS).getContext(CONTEXT_2D);

    /**
     * Text constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {String} [config.fontFamily] default is Calibri
     * @param {Number} [config.fontSize] in pixels.  Default is 12
     * @param {String} [config.fontStyle] can be normal, bold, or italic.  Default is normal
     * @param {String} config.text
     * @param {String} [config.align] can be left, center, or right
     * @param {Number} [config.padding]
     * @param {Number} [config.width] default is auto
     * @param {Number} [config.height] default is auto
     * @param {Number} [config.lineHeight] default is 1
     * @param {String} [config.wrap] can be word, char, or none. Default is word
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var text = new Kinetic.Text({<br>
     *   x: stage.getWidth() / 2,<br>
     *   y: 15,<br>
     *   text: 'Simple Text',<br>
     *   fontSize: 30,<br>
     *   fontFamily: 'Calibri',<br>
     *   fill: 'green'<br>
     * });
     */
    Kinetic.Text = function(config) {
        this.___init(config);
    };
    function _fillFunc(context) {
        context.fillText(this.partialText, 0, 0);
    }
    function _strokeFunc(context) {
        context.strokeText(this.partialText, 0, 0);
    }

    Kinetic.Text.prototype = {
        ___init: function(config) {
            var that = this;

            if (config.width === undefined) {
                config.width = AUTO;
            }
            if (config.height === undefined) {
                config.height = AUTO;
            }

            // call super constructor
            Kinetic.Shape.call(this, config);

            this._fillFunc = _fillFunc;
            this._strokeFunc = _strokeFunc;
            this.className = TEXT_UPPER;

            // update text data for certain attr changes
            for(var n = 0; n < attrChangeListLen; n++) {
                this.on(ATTR_CHANGE_LIST[n] + CHANGE_KINETIC, that._setTextData);
            }

            this._setTextData();
        },
        drawFunc: function(context) {
            var p = this.getPadding(),
                fontStyle = this.getFontStyle(),
                fontSize = this.getFontSize(),
                fontFamily = this.getFontFamily(),
                textHeight = this.getTextHeight(),
                lineHeightPx = this.getLineHeight() * textHeight,
                textArr = this.textArr,
                textArrLen = textArr.length,
                totalWidth = this.getWidth();

            context.setAttr('font', this._getContextFont());
            context.setAttr('textBaseline', MIDDLE);
            context.setAttr('textAlign', LEFT);
            context.save();
            context.translate(p, 0);
            context.translate(0, p + textHeight / 2);

            // draw text lines
            for(var n = 0; n < textArrLen; n++) {
                var obj = textArr[n],
                    text = obj.text,
                    width = obj.width;

                // horizontal alignment
                context.save();
                if(this.getAlign() === RIGHT) {
                    context.translate(totalWidth - width - p * 2, 0);
                }
                else if(this.getAlign() === CENTER) {
                    context.translate((totalWidth - width - p * 2) / 2, 0);
                }

                this.partialText = text;
                context.fillStrokeShape(this);
                context.restore();
                context.translate(0, lineHeightPx);
            }
            context.restore();
        },
        drawHitFunc: function(context) {
            var width = this.getWidth(),
                height = this.getHeight();

            context.beginPath();
            context.rect(0, 0, width, height);
            context.closePath();
            context.fillStrokeShape(this);
        },
        /**
         * set text
         * @method
         * @memberof Kinetic.Text.prototype
         * @param {String} text
         */
        setText: function(text) {
            var str = Kinetic.Util._isString(text) ? text : text.toString();
            this._setAttr(TEXT, str);
        },
        /**
         * get width of text area, which includes padding
         * @method
         * @memberof Kinetic.Text.prototype
         * @returns {Number}
         */
        getWidth: function() {
            return this.attrs.width === AUTO ? this.getTextWidth() + this.getPadding() * 2 : this.attrs.width;
        },
        /**
         * get the height of the text area, which takes into account multi-line text, line heights, and padding
         * @method
         * @memberof Kinetic.Text.prototype
         * @returns {Number}
         */
        getHeight: function() {
            return this.attrs.height === AUTO ? (this.getTextHeight() * this.textArr.length * this.getLineHeight()) + this.getPadding() * 2 : this.attrs.height;
        },
        /**
         * get text width
         * @method
         * @memberof Kinetic.Text.prototype
         * @returns {Number}
         */
        getTextWidth: function() {
            return this.textWidth;
        },
        /**
         * get text height
         * @method
         * @memberof Kinetic.Text.prototype
         * @returns {Number}
         */
        getTextHeight: function() {
            return this.textHeight;
        },
        _getTextSize: function(text) {
            var _context = dummyContext,
                fontSize = this.getFontSize(),
                metrics;

            _context.save();
            _context.font = this._getContextFont();

            metrics = _context.measureText(text);
            _context.restore();
            return {
                width: metrics.width,
                height: parseInt(fontSize, 10)
            };
        },
        _getContextFont: function() {
            return this.getFontStyle() + SPACE + this.getFontSize() + PX_SPACE + this.getFontFamily();
        },
        _addTextLine: function (line, width, height) {
            return this.textArr.push({text: line, width: width});
        },
        _getTextWidth: function (text) {
            return dummyContext.measureText(text).width;
        },
        _setTextData: function () {
            var lines = this.getText().split('\n'),
                fontSize = +this.getFontSize(),
                textWidth = 0,
                lineHeightPx = this.getLineHeight() * fontSize,
                width = this.attrs.width,
                height = this.attrs.height,
                fixedWidth = width !== AUTO,
                fixedHeight = height !== AUTO,
                padding = this.getPadding(),
                maxWidth = width - padding * 2,
                maxHeightPx = height - padding * 2,
                currentHeightPx = 0,
                wrap = this.getWrap(),
                shouldWrap = wrap !== NONE,
                wrapAtWord = wrap !==  CHAR && shouldWrap;

            this.textArr = [];
            dummyContext.save();
            dummyContext.font = this.getFontStyle() + SPACE + fontSize + PX_SPACE + this.getFontFamily();
            for (var i = 0, max = lines.length; i < max; ++i) {
                var line = lines[i],
                    lineWidth = this._getTextWidth(line);
                if (fixedWidth && lineWidth > maxWidth) {
                    /*
                     * if width is fixed and line does not fit entirely
                     * break the line into multiple fitting lines
                     */
                    while (line.length > 0) {
                        /*
                         * use binary search to find the longest substring that
                         * that would fit in the specified width
                         */
                        var low = 0, high = line.length,
                            match = '', matchWidth = 0;
                        while (low < high) {
                            var mid = (low + high) >>> 1,
                                substr = line.slice(0, mid + 1),
                                substrWidth = this._getTextWidth(substr);
                            if (substrWidth <= maxWidth) {
                                low = mid + 1;
                                match = substr;
                                matchWidth = substrWidth;
                            } else {
                                high = mid;
                            }
                        }
                        /*
                         * 'low' is now the index of the substring end
                         * 'match' is the substring
                         * 'matchWidth' is the substring width in px
                         */
                        if (match) {
                            // a fitting substring was found
                            if (wrapAtWord) {
                                // try to find a space or dash where wrapping could be done
                                var wrapIndex = Math.max(match.lastIndexOf(SPACE),
                                    match.lastIndexOf(DASH)) + 1;
                                if (wrapIndex > 0) {
                                    // re-cut the substring found at the space/dash position
                                    low = wrapIndex;
                                    match = match.slice(0, low);
                                    matchWidth = this._getTextWidth(match);
                                }
                            }
                            this._addTextLine(match, matchWidth);
                            currentHeightPx += lineHeightPx;
                            if (!shouldWrap ||
                                (fixedHeight && currentHeightPx + lineHeightPx > maxHeightPx)) {
                                /*
                                 * stop wrapping if wrapping is disabled or if adding
                                 * one more line would overflow the fixed height
                                 */
                                break;
                            }
                            line = line.slice(low);
                            if (line.length > 0) {
                                // Check if the remaining text would fit on one line
                                lineWidth = this._getTextWidth(line);
                                if (lineWidth <= maxWidth) {
                                    // if it does, add the line and break out of the loop
                                    this._addTextLine(line, lineWidth);
                                    currentHeightPx += lineHeightPx;
                                    break;
                                }
                            }
                        } else {
                            // not even one character could fit in the element, abort
                            break;
                        }
                    }
                } else {
                    // element width is automatically adjusted to max line width
                    this._addTextLine(line, lineWidth);
                    currentHeightPx += lineHeightPx;
                    textWidth = Math.max(textWidth, lineWidth);
                }
                // if element height is fixed, abort if adding one more line would overflow
                if (fixedHeight && currentHeightPx + lineHeightPx > maxHeightPx) {
                    break;
                }
            }
            dummyContext.restore();
            this.textHeight = fontSize;
            this.textWidth = textWidth;
        }
    };
    Kinetic.Util.extend(Kinetic.Text, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'fontFamily', CALIBRI);

    /**
     * set font family
     * @name setFontFamily
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {String} fontFamily
     */

    /**
     * get font family
     * @name getFontFamily
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {String}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'fontSize', 12);

    /**
     * set font size in pixels
     * @name setFontSize
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {Number} fontSize
     */

    /**
     * get font size
     * @name getFontSize
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'fontStyle', NORMAL);

    /**
     * set font style.  Can be 'normal', 'italic', or 'bold'.  'normal' is the default.
     * @name setFontStyle
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {String} fontStyle
     */

    /**
     * get font style
     * @name getFontStyle
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {String}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'padding', 0);

    /**
     * set padding
     * @name setPadding
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {int} padding
     */

    /**
     * get padding
     * @name getPadding
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'align', LEFT);

    /**
     * set horizontal align of text
     * @name setAlign
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {String} align align can be 'left', 'center', or 'right'
     */

    /**
     * get horizontal align
     * @name getAlign
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {String}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'lineHeight', 1);

    /**
     * set line height
     * @name setLineHeight
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {Number} lineHeight default is 1
     */

    /**
     * get line height
     * @name getLineHeight
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {Number}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Text, 'wrap', WORD);

    /**
     * set wrap
     * @name setWrap
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {String} wrap can be word, char, or none. Default is word
     */

    /**
     * get wrap
     * @name getWrap
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {String}
     */

    Kinetic.Factory.addGetter(Kinetic.Text, TEXT, EMPTY_STRING);

    /**
     * get text
     * @name getText
     * @method
     * @memberof Kinetic.Text.prototype
     * @returns {String}
     */

    Kinetic.Factory.addSetter(Kinetic.Text, 'width');

    /**
     * set width
     * @name setWidth
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {Number|String} width default is auto
     */

    Kinetic.Factory.addSetter(Kinetic.Text, 'height');

    /**
     * set height
     * @name setHeight
     * @method
     * @memberof Kinetic.Text.prototype
     * @param {Number|String} height default is auto
     */
})();
;(function() {
    /**
     * Line constructor.&nbsp; Lines are defined by an array of points
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Array} config.points can be a flattened array of points, an array of point arrays, or an array of point objects.
     *  e.g. [0,1,2,3], [[0,1],[2,3]] and [{x:0,y:1},{x:2,y:3}] are equivalent
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * // simple line
     * var line = new Kinetic.Line({<br>
     *   x: 100,<br>
     *   y: 50,<br>
     *   points: [73, 70, 340, 23, 450, 60, 500, 20],<br>
     *   stroke: 'red'<br>
     * });<br><br>
     *
     * // dashed line with shadow<br>
     * var line = new Kinetic.Line({<br>
     *   x: 100,<br>
     *   y: 50,<br>
     *   points: [73, 70, 340, 23, 450, 60, 500, 20],<br>
     *   stroke: 'red',<br>
     *   dashArray: [33, 10],<br>
     *   shadowColor: 'black',<br>
     *   shadowBlur: 10,<br>
     *   shadowOffset: 10,<br>
     *   shadowOpacity: 0.5<br>
     * });
     */
    Kinetic.Line = function(config) {
        this.___init(config);
    };

    Kinetic.Line.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Line';
        },
        drawFunc: function(context) {
            var points = this.getPoints(),
                length = points.length,
                n, point;

            context.beginPath();
            context.moveTo(points[0].x, points[0].y);

            for(n = 1; n < length; n++) {
                point = points[n];
                context.lineTo(point.x, point.y);
            }

            context.strokeShape(this);
        }
    };
    Kinetic.Util.extend(Kinetic.Line, Kinetic.Shape);

    Kinetic.Factory.addPointsGetterSetter(Kinetic.Line, 'points');
    /**
     * set points array
     * @name setPoints
     * @method
     * @memberof Kinetic.Line.prototype
     * @param {Array} can be an array of point objects or an array
     *  of Numbers.  e.g. [{x:1,y:2},{x:3,y:4}] or [1,2,3,4]
     */

    /**
     * get points array
     * @name getPoints
     * @method
     * @memberof Kinetic.Line.prototype
     * @returns {Array}
     */
})();
;(function() {
    /**
     * Spline constructor.&nbsp; Splines are defined by an array of points and
     *  a tension
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Array} config.points can be a flattened array of points, an array of point arrays, or an array of point objects.
     *  e.g. [0,1,2,3], [[0,1],[2,3]] and [{x:0,y:1},{x:2,y:3}] are equivalent
     * @param {Number} [config.tension] default value is 1.  Higher values will result in a more curvy line.  A value of 0 will result in no interpolation.
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var spline = new Kinetic.Spline({<br>
     *   x: 100,<br>
     *   y: 50,<br>
     *   points: [73, 70, 340, 23, 450, 60, 500, 20],<br>
     *   stroke: 'red',<br>
     *   tension: 1<br>
     * });
     */
    Kinetic.Spline = function(config) {
        this.___init(config);
    };

    Kinetic.Spline.prototype = {
        ___init: function(config) {
            var that = this;
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Spline';

            this.on('pointsChange.kinetic tensionChange.kinetic', function() {
                that._setAllPoints();
            });

            this._setAllPoints();
        },
        drawFunc: function(context) {
            var points = this.getPoints(),
                length = points.length,
                tension = this.getTension(),
                ap, len, n, point;

            context.beginPath();
            context.moveTo(points[0].x, points[0].y);

            // tension
            if(tension !== 0 && length > 2) {
                ap = this.allPoints;
                len = ap.length;
                n = 2;

                context.quadraticCurveTo(ap[0].x, ap[0].y, ap[1].x, ap[1].y);

                while(n < len - 1) {
                    context.bezierCurveTo(ap[n].x, ap[n++].y, ap[n].x, ap[n++].y, ap[n].x, ap[n++].y);
                }

                context.quadraticCurveTo(ap[len - 1].x, ap[len - 1].y, points[length - 1].x, points[length - 1].y);
            }
            // no tension
            else {
                for(n = 1; n < length; n++) {
                    point = points[n];
                    context.lineTo(point.x, point.y);
                }
            }

            context.strokeShape(this);
        },
        _setAllPoints: function() {
            this.allPoints = Kinetic.Util._expandPoints(this.getPoints(), this.getTension());
        }
    };
    Kinetic.Util.extend(Kinetic.Spline, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Spline, 'tension', 1);

    /**
     * get tension
     * @name getTension
     * @method
     * @memberof Kinetic.Spline.prototype
     * @returns {Number}
     */

    /**
     * set tension
     * @name setTension
     * @method
     * @memberof Kinetic.Spline.prototype
     * @param {Number} tension
     */

    Kinetic.Factory.addPointsGetterSetter(Kinetic.Spline, 'points');
    /**
     * get points array
     * @name getPoints
     * @method
     * @memberof Kinetic.Spline.prototype
     * @returns {Array}
     */

    /**
     * set points array
     * @name setPoints
     * @method
     * @memberof Kinetic.Spline.prototype
     * @param {Array} can be an array of point objects or an array
     *  of Numbers.  e.g. [{x:1,y:2},{x:3,y:4}] or [1,2,3,4]
     */
})();
;(function() {
    /**
     * Blob constructor.  Blobs are defined by an array of points and
     *  a tension
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Array} config.points can be a flattened array of points, an array of point arrays, or an array of point objects.
     *  e.g. [0,1,2,3], [[0,1],[2,3]] and [{x:0,y:1},{x:2,y:3}] are equivalent
     * @param {Number} [config.tension] default value is 1.  Higher values will result in a more curvy line.  A value of 0 will result in no interpolation.
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var blob = new Kinetic.Blob({<br>
     *   points: [73, 140, 340, 23, 500, 109, 300, 170],<br>
     *   tension: 0.8,<br>
     *   fill: 'red',<br>
     *   stroke: 'black'<br>
     *   strokeWidth: 5<br>
     * });
     */
    Kinetic.Blob = function(config) {
        this.___init(config);
    };

    Kinetic.Blob.prototype = {
        ___init: function(config) {
            var that = this;
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Blob';

            this.on('pointsChange.kinetic tensionChange.kinetic', function() {
                that._setAllPoints();
            });

            this._setAllPoints();
        },
        drawFunc: function(context) {
            var points = this.getPoints(),
                length = points.length,
                tension = this.getTension(),
                ap, len, n, point;

            context.beginPath();
            context.moveTo(points[0].x, points[0].y);

            // tension
            if(tension !== 0 && length > 2) {
                ap = this.allPoints;
                len = ap.length;
                n = 0;

                while(n < len-1) {
                    context.bezierCurveTo(ap[n].x, ap[n++].y, ap[n].x, ap[n++].y, ap[n].x, ap[n++].y);
                }
            }
            // no tension
            else {
                for(n = 1; n < length; n++) {
                    point = points[n];
                    context.lineTo(point.x, point.y);
                }
            }

            context.closePath();
            context.fillStrokeShape(this);
        },
        _setAllPoints: function() {
            var points = this.getPoints(),
                length = points.length,
                tension = this.getTension(),
                util = Kinetic.Util,
                firstControlPoints = util._getControlPoints(points[length - 1], points[0], points[1], tension),
                lastControlPoints = util._getControlPoints(points[length - 2], points[length - 1], points[0], tension);

            this.allPoints = Kinetic.Util._expandPoints(this.getPoints(), this.getTension());

            // prepend control point
            this.allPoints.unshift(firstControlPoints[1]);

            // append cp, point, cp, cp, first point
            this.allPoints.push(lastControlPoints[0]);
            this.allPoints.push(points[length - 1]);
            this.allPoints.push(lastControlPoints[1]);
            this.allPoints.push(firstControlPoints[0]);
            this.allPoints.push(points[0]);
        }
    };

    Kinetic.Util.extend(Kinetic.Blob, Kinetic.Shape);

    Kinetic.Factory.addGetterSetter(Kinetic.Blob, 'tension', 1);
    /**
     * get tension
     * @name getTension
     * @method
     * @memberof Kinetic.Blob.prototype
     * @returns {Number}
     */

    /**
     * set tension
     * @name setTension
     * @method
     * @memberof Kinetic.Blob.prototype
     * @param {Number} tension
     */

    Kinetic.Factory.addPointsGetterSetter(Kinetic.Blob, 'points');
    /**
     * get points array
     * @name getPoints
     * @method
     * @memberof Kinetic.Blob.prototype
     * @returns {Array}
     */

    /**
     * set points array
     * @name setPoints
     * @method
     * @memberof Kinetic.Blob.prototype
     * @param {Array} can be an array of point objects or an array
     *  of Numbers.  e.g. [{x:1,y:2},{x:3,y:4}] or [1,2,3,4]
     */
})();
;(function() {
    /**
     * Sprite constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {String} config.animation animation key
     * @param {Object} config.animations animation map
     * @param {Integer} [config.index] animation index
     * @param {Image} config.image image object
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var animations = {<br>
     *   idle: [{<br>
     *     x: 2,<br>
     *     y: 2,<br>
     *     width: 70,<br>
     *     height: 119<br>
     *   }, {<br>
     *     x: 71,<br>
     *     y: 2,<br>
     *     width: 74,<br>
     *     height: 119<br>
     *   }, {<br>
     *     x: 146,<br>
     *     y: 2,<br>
     *     width: 81,<br>
     *     height: 119<br>
     *   }, {<br>
     *     x: 226,<br>
     *     y: 2,<br>
     *     width: 76,<br>
     *     height: 119<br>
     *   }],<br>
     *   punch: [{<br>
     *     x: 2,<br>
     *     y: 138,<br>
     *     width: 74,<br>
     *     height: 122<br>
     *   }, {<br>
     *     x: 76,<br>
     *     y: 138,<br>
     *     width: 84,<br>
     *     height: 122<br>
     *   }, {<br>
     *     x: 346,<br>
     *     y: 138,<br>
     *     width: 120,<br>
     *     height: 122<br>
     *   }]<br>
     * };<br><br>
     *
     * var imageObj = new Image();<br>
     * imageObj.onload = function() {<br>
     *   var sprite = new Kinetic.Sprite({<br>
     *     x: 200,<br>
     *     y: 100,<br>
     *     image: imageObj,<br>
     *     animation: 'idle',<br>
     *     animations: animations,<br>
     *     frameRate: 7,<br>
     *     index: 0<br>
     *   });<br>
     * };<br>
     * imageObj.src = '/path/to/image.jpg'
     */
    Kinetic.Sprite = function(config) {
        this.___init(config);
    };

    Kinetic.Sprite.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Sprite';

            this.anim = new Kinetic.Animation();
            var that = this;
            this.on('animationChange.kinetic', function() {
                // reset index when animation changes
                that.setIndex(0);
            });
        },
        drawFunc: function(context) {
            var anim = this.getAnimation(),
                index = this.getIndex(),
                f = this.getAnimations()[anim][index],
                image = this.getImage();

            if(image) {
                context.drawImage(image, f.x, f.y, f.width, f.height, 0, 0, f.width, f.height);
            }
        },
        drawHitFunc: function(context) {
            var anim = this.getAnimation(),
                index = this.getIndex(),
                f = this.getAnimations()[anim][index];

            context.beginPath();
            context.rect(0, 0, f.width, f.height);
            context.closePath();
            context.fillShape(this);
        },
        _useBufferCanvas: function() {
            return (this.hasShadow() || this.getAbsoluteOpacity() !== 1) && this.hasStroke();
        },
        /**
         * start sprite animation
         * @method
         * @memberof Kinetic.Sprite.prototype
         */
        start: function() {
            var that = this;
            var layer = this.getLayer();

            /*
             * animation object has no executable function because
             *  the updates are done with a fixed FPS with the setInterval
             *  below.  The anim object only needs the layer reference for
             *  redraw
             */
            this.anim.setLayers(layer);

            this.interval = setInterval(function() {
                var index = that.getIndex();
                that._updateIndex();
                if(that.afterFrameFunc && index === that.afterFrameIndex) {
                    that.afterFrameFunc();
                    delete that.afterFrameFunc;
                    delete that.afterFrameIndex;
                }
            }, 1000 / this.getFrameRate());

            this.anim.start();
        },
        /**
         * stop sprite animation
         * @method
         * @memberof Kinetic.Sprite.prototype
         */
        stop: function() {
            this.anim.stop();
            clearInterval(this.interval);
        },
        /**
         * set after frame event handler
         * @method
         * @memberof Kinetic.Sprite.prototype
         * @param {Integer} index frame index
         * @param {Function} func function to be executed after frame has been drawn
         */
        afterFrame: function(index, func) {
            this.afterFrameIndex = index;
            this.afterFrameFunc = func;
        },
        _updateIndex: function() {
            var index = this.getIndex(),
                animation = this.getAnimation(),
                animations = this.getAnimations(),
                anim = animations[animation],
                len = anim.length;

            if(index < len - 1) {
                this.setIndex(index + 1);
            }
            else {
                this.setIndex(0);
            }
        }
    };
    Kinetic.Util.extend(Kinetic.Sprite, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Sprite, 'animation');

    /**
     * set animation key
     * @name setAnimation
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @param {String} anim animation key
     */

    /**
     * get animation key
     * @name getAnimation
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @returns {String}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Sprite, 'animations');

    /**
     * set animations map
     * @name setAnimations
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @param {Object} animations
     */

    /**
     * get animations map
     * @name getAnimations
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @returns {Object}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Sprite, 'image');

    /**
     * set image
     * @name setImage
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @param {Image} image
     */

    /**
     * get image
     * @name getImage
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @returns {ImageObject}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Sprite, 'index', 0);

    /**
     * set animation frame index
     * @name setIndex
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @param {Integer} index frame index
     */

    /**
     * get animation frame index
     * @name getIndex
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @returns {Integer}
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Sprite, 'frameRate', 17);

    /**
     * set frame rate in frames / second.  Default is 17 frames per second.  Increase this number to make the sprite
     *  animation run faster, and decrease the number to make the sprite animation run slower
     * @name setFrameRate
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @param {Integer} frameRate
     */

    /**
     * get frame rate
     * @name getFrameRate
     * @method
     * @memberof Kinetic.Sprite.prototype
     * @returns {Number}
     */

})();
;(function () {
    /**
     * Path constructor.
     * @author Jason Follas
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {String} config.data SVG data string
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var path = new Kinetic.Path({<br>
     *   x: 240,<br>
     *   y: 40,<br>
     *   data: 'M12.582,9.551C3.251,16.237,0.921,29.021,7.08,38.564l-2.36,1.689l4.893,2.262l4.893,2.262l-0.568-5.36l-0.567-5.359l-2.365,1.694c-4.657-7.375-2.83-17.185,4.352-22.33c7.451-5.338,17.817-3.625,23.156,3.824c5.337,7.449,3.625,17.813-3.821,23.152l2.857,3.988c9.617-6.893,11.827-20.277,4.935-29.896C35.591,4.87,22.204,2.658,12.582,9.551z',<br>
     *   fill: 'green',<br>
     *   scale: 2<br>
     * });
     */
    Kinetic.Path = function (config) {
        this.___init(config);
    };

    Kinetic.Path.prototype = {
        ___init: function (config) {
            this.dataArray = [];
            var that = this;

            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Path';

            this.dataArray = Kinetic.Path.parsePathData(this.getData());
            this.on('dataChange.kinetic', function () {
                that.dataArray = Kinetic.Path.parsePathData(this.getData());
            });
        },
        drawFunc: function (context) {
            var ca = this.dataArray,
                closedPath = false;

            // context position
            context.beginPath();
            for (var n = 0; n < ca.length; n++) {
                var c = ca[n].command;
                var p = ca[n].points;
                switch (c) {
                    case 'L':
                        context.lineTo(p[0], p[1]);
                        break;
                    case 'M':
                        context.moveTo(p[0], p[1]);
                        break;
                    case 'C':
                        context.bezierCurveTo(p[0], p[1], p[2], p[3], p[4], p[5]);
                        break;
                    case 'Q':
                        context.quadraticCurveTo(p[0], p[1], p[2], p[3]);
                        break;
                    case 'A':
                        var cx = p[0], cy = p[1], rx = p[2], ry = p[3], theta = p[4], dTheta = p[5], psi = p[6], fs = p[7];

                        var r = (rx > ry) ? rx : ry;
                        var scaleX = (rx > ry) ? 1 : rx / ry;
                        var scaleY = (rx > ry) ? ry / rx : 1;

                        context.translate(cx, cy);
                        context.rotate(psi);
                        context.scale(scaleX, scaleY);
                        context.arc(0, 0, r, theta, theta + dTheta, 1 - fs);
                        context.scale(1 / scaleX, 1 / scaleY);
                        context.rotate(-psi);
                        context.translate(-cx, -cy);

                        break;
                    case 'z':
                        context.closePath();
                        closedPath = true;
                        break;
                }
            }

            if (closedPath) {
                context.fillStrokeShape(this);
            }
            else {
                context.strokeShape(this);
            }
        }
    };
    Kinetic.Util.extend(Kinetic.Path, Kinetic.Shape);

    Kinetic.Path.getLineLength = function(x1, y1, x2, y2) {
        return Math.sqrt((x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1));
    };
    Kinetic.Path.getPointOnLine = function(dist, P1x, P1y, P2x, P2y, fromX, fromY) {
        if(fromX === undefined) {
            fromX = P1x;
        }
        if(fromY === undefined) {
            fromY = P1y;
        }

        var m = (P2y - P1y) / ((P2x - P1x) + 0.00000001);
        var run = Math.sqrt(dist * dist / (1 + m * m));
        if(P2x < P1x)
            run *= -1;
        var rise = m * run;
        var pt;

        if (P2x === P1x) { // vertical line
            pt = {
                x: fromX,
                y: fromY + rise
            };
        } else if((fromY - P1y) / ((fromX - P1x) + 0.00000001) === m) {
            pt = {
                x: fromX + run,
                y: fromY + rise
            };
        }
        else {
            var ix, iy;

            var len = this.getLineLength(P1x, P1y, P2x, P2y);
            if(len < 0.00000001) {
                return undefined;
            }
            var u = (((fromX - P1x) * (P2x - P1x)) + ((fromY - P1y) * (P2y - P1y)));
            u = u / (len * len);
            ix = P1x + u * (P2x - P1x);
            iy = P1y + u * (P2y - P1y);

            var pRise = this.getLineLength(fromX, fromY, ix, iy);
            var pRun = Math.sqrt(dist * dist - pRise * pRise);
            run = Math.sqrt(pRun * pRun / (1 + m * m));
            if(P2x < P1x)
                run *= -1;
            rise = m * run;
            pt = {
                x: ix + run,
                y: iy + rise
            };
        }

        return pt;
    };

    Kinetic.Path.getPointOnCubicBezier = function(pct, P1x, P1y, P2x, P2y, P3x, P3y, P4x, P4y) {
        function CB1(t) {
            return t * t * t;
        }
        function CB2(t) {
            return 3 * t * t * (1 - t);
        }
        function CB3(t) {
            return 3 * t * (1 - t) * (1 - t);
        }
        function CB4(t) {
            return (1 - t) * (1 - t) * (1 - t);
        }
        var x = P4x * CB1(pct) + P3x * CB2(pct) + P2x * CB3(pct) + P1x * CB4(pct);
        var y = P4y * CB1(pct) + P3y * CB2(pct) + P2y * CB3(pct) + P1y * CB4(pct);

        return {
            x: x,
            y: y
        };
    };
    Kinetic.Path.getPointOnQuadraticBezier = function(pct, P1x, P1y, P2x, P2y, P3x, P3y) {
        function QB1(t) {
            return t * t;
        }
        function QB2(t) {
            return 2 * t * (1 - t);
        }
        function QB3(t) {
            return (1 - t) * (1 - t);
        }
        var x = P3x * QB1(pct) + P2x * QB2(pct) + P1x * QB3(pct);
        var y = P3y * QB1(pct) + P2y * QB2(pct) + P1y * QB3(pct);

        return {
            x: x,
            y: y
        };
    };
    Kinetic.Path.getPointOnEllipticalArc = function(cx, cy, rx, ry, theta, psi) {
        var cosPsi = Math.cos(psi), sinPsi = Math.sin(psi);
        var pt = {
            x: rx * Math.cos(theta),
            y: ry * Math.sin(theta)
        };
        return {
            x: cx + (pt.x * cosPsi - pt.y * sinPsi),
            y: cy + (pt.x * sinPsi + pt.y * cosPsi)
        };
    };
    /*
     * get parsed data array from the data
     *  string.  V, v, H, h, and l data are converted to
     *  L data for the purpose of high performance Path
     *  rendering
     */
    Kinetic.Path.parsePathData = function(data) {
        // Path Data Segment must begin with a moveTo
        //m (x y)+  Relative moveTo (subsequent points are treated as lineTo)
        //M (x y)+  Absolute moveTo (subsequent points are treated as lineTo)
        //l (x y)+  Relative lineTo
        //L (x y)+  Absolute LineTo
        //h (x)+    Relative horizontal lineTo
        //H (x)+    Absolute horizontal lineTo
        //v (y)+    Relative vertical lineTo
        //V (y)+    Absolute vertical lineTo
        //z (closepath)
        //Z (closepath)
        //c (x1 y1 x2 y2 x y)+ Relative Bezier curve
        //C (x1 y1 x2 y2 x y)+ Absolute Bezier curve
        //q (x1 y1 x y)+       Relative Quadratic Bezier
        //Q (x1 y1 x y)+       Absolute Quadratic Bezier
        //t (x y)+    Shorthand/Smooth Relative Quadratic Bezier
        //T (x y)+    Shorthand/Smooth Absolute Quadratic Bezier
        //s (x2 y2 x y)+       Shorthand/Smooth Relative Bezier curve
        //S (x2 y2 x y)+       Shorthand/Smooth Absolute Bezier curve
        //a (rx ry x-axis-rotation large-arc-flag sweep-flag x y)+     Relative Elliptical Arc
        //A (rx ry x-axis-rotation large-arc-flag sweep-flag x y)+  Absolute Elliptical Arc

        // return early if data is not defined
        if(!data) {
            return [];
        }

        // command string
        var cs = data;

        // command chars
        var cc = ['m', 'M', 'l', 'L', 'v', 'V', 'h', 'H', 'z', 'Z', 'c', 'C', 'q', 'Q', 't', 'T', 's', 'S', 'a', 'A'];
        // convert white spaces to commas
        cs = cs.replace(new RegExp(' ', 'g'), ',');
        // create pipes so that we can split the data
        for(var n = 0; n < cc.length; n++) {
            cs = cs.replace(new RegExp(cc[n], 'g'), '|' + cc[n]);
        }
        // create array
        var arr = cs.split('|');
        var ca = [];
        // init context point
        var cpx = 0;
        var cpy = 0;
        for( n = 1; n < arr.length; n++) {
            var str = arr[n];
            var c = str.charAt(0);
            str = str.slice(1);
            // remove ,- for consistency
            str = str.replace(new RegExp(',-', 'g'), '-');
            // add commas so that it's easy to split
            str = str.replace(new RegExp('-', 'g'), ',-');
            str = str.replace(new RegExp('e,-', 'g'), 'e-');
            var p = str.split(',');
            if(p.length > 0 && p[0] === '') {
                p.shift();
            }
            // convert strings to floats
            for(var i = 0; i < p.length; i++) {
                p[i] = parseFloat(p[i]);
            }
            while(p.length > 0) {
                if(isNaN(p[0]))// case for a trailing comma before next command
                    break;

                var cmd = null;
                var points = [];
                var startX = cpx, startY = cpy;
                // Move var from within the switch to up here (jshint)
                var prevCmd, ctlPtx, ctlPty;     // Ss, Tt
                var rx, ry, psi, fa, fs, x1, y1; // Aa


                // convert l, H, h, V, and v to L
                switch (c) {

                    // Note: Keep the lineTo's above the moveTo's in this switch
                    case 'l':
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'L';
                        points.push(cpx, cpy);
                        break;
                    case 'L':
                        cpx = p.shift();
                        cpy = p.shift();
                        points.push(cpx, cpy);
                        break;

                    // Note: lineTo handlers need to be above this point
                    case 'm':
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'M';
                        points.push(cpx, cpy);
                        c = 'l';
                        // subsequent points are treated as relative lineTo
                        break;
                    case 'M':
                        cpx = p.shift();
                        cpy = p.shift();
                        cmd = 'M';
                        points.push(cpx, cpy);
                        c = 'L';
                        // subsequent points are treated as absolute lineTo
                        break;

                    case 'h':
                        cpx += p.shift();
                        cmd = 'L';
                        points.push(cpx, cpy);
                        break;
                    case 'H':
                        cpx = p.shift();
                        cmd = 'L';
                        points.push(cpx, cpy);
                        break;
                    case 'v':
                        cpy += p.shift();
                        cmd = 'L';
                        points.push(cpx, cpy);
                        break;
                    case 'V':
                        cpy = p.shift();
                        cmd = 'L';
                        points.push(cpx, cpy);
                        break;
                    case 'C':
                        points.push(p.shift(), p.shift(), p.shift(), p.shift());
                        cpx = p.shift();
                        cpy = p.shift();
                        points.push(cpx, cpy);
                        break;
                    case 'c':
                        points.push(cpx + p.shift(), cpy + p.shift(), cpx + p.shift(), cpy + p.shift());
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'C';
                        points.push(cpx, cpy);
                        break;
                    case 'S':
                        ctlPtx = cpx, ctlPty = cpy;
                        prevCmd = ca[ca.length - 1];
                        if(prevCmd.command === 'C') {
                            ctlPtx = cpx + (cpx - prevCmd.points[2]);
                            ctlPty = cpy + (cpy - prevCmd.points[3]);
                        }
                        points.push(ctlPtx, ctlPty, p.shift(), p.shift());
                        cpx = p.shift();
                        cpy = p.shift();
                        cmd = 'C';
                        points.push(cpx, cpy);
                        break;
                    case 's':
                        ctlPtx = cpx, ctlPty = cpy;
                        prevCmd = ca[ca.length - 1];
                        if(prevCmd.command === 'C') {
                            ctlPtx = cpx + (cpx - prevCmd.points[2]);
                            ctlPty = cpy + (cpy - prevCmd.points[3]);
                        }
                        points.push(ctlPtx, ctlPty, cpx + p.shift(), cpy + p.shift());
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'C';
                        points.push(cpx, cpy);
                        break;
                    case 'Q':
                        points.push(p.shift(), p.shift());
                        cpx = p.shift();
                        cpy = p.shift();
                        points.push(cpx, cpy);
                        break;
                    case 'q':
                        points.push(cpx + p.shift(), cpy + p.shift());
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'Q';
                        points.push(cpx, cpy);
                        break;
                    case 'T':
                        ctlPtx = cpx, ctlPty = cpy;
                        prevCmd = ca[ca.length - 1];
                        if(prevCmd.command === 'Q') {
                            ctlPtx = cpx + (cpx - prevCmd.points[0]);
                            ctlPty = cpy + (cpy - prevCmd.points[1]);
                        }
                        cpx = p.shift();
                        cpy = p.shift();
                        cmd = 'Q';
                        points.push(ctlPtx, ctlPty, cpx, cpy);
                        break;
                    case 't':
                        ctlPtx = cpx, ctlPty = cpy;
                        prevCmd = ca[ca.length - 1];
                        if(prevCmd.command === 'Q') {
                            ctlPtx = cpx + (cpx - prevCmd.points[0]);
                            ctlPty = cpy + (cpy - prevCmd.points[1]);
                        }
                        cpx += p.shift();
                        cpy += p.shift();
                        cmd = 'Q';
                        points.push(ctlPtx, ctlPty, cpx, cpy);
                        break;
                    case 'A':
                        rx = p.shift(), ry = p.shift(), psi = p.shift(), fa = p.shift(), fs = p.shift();
                        x1 = cpx, y1 = cpy; cpx = p.shift(), cpy = p.shift();
                        cmd = 'A';
                        points = this.convertEndpointToCenterParameterization(x1, y1, cpx, cpy, fa, fs, rx, ry, psi);
                        break;
                    case 'a':
                        rx = p.shift(), ry = p.shift(), psi = p.shift(), fa = p.shift(), fs = p.shift();
                        x1 = cpx, y1 = cpy; cpx += p.shift(), cpy += p.shift();
                        cmd = 'A';
                        points = this.convertEndpointToCenterParameterization(x1, y1, cpx, cpy, fa, fs, rx, ry, psi);
                        break;
                }

                ca.push({
                    command: cmd || c,
                    points: points,
                    start: {
                        x: startX,
                        y: startY
                    },
                    pathLength: this.calcLength(startX, startY, cmd || c, points)
                });
            }

            if(c === 'z' || c === 'Z') {
                ca.push({
                    command: 'z',
                    points: [],
                    start: undefined,
                    pathLength: 0
                });
            }
        }

        return ca;
    };
    Kinetic.Path.calcLength = function(x, y, cmd, points) {
        var len, p1, p2;
        var path = Kinetic.Path;

        switch (cmd) {
            case 'L':
                return path.getLineLength(x, y, points[0], points[1]);
            case 'C':
                // Approximates by breaking curve into 100 line segments
                len = 0.0;
                p1 = path.getPointOnCubicBezier(0, x, y, points[0], points[1], points[2], points[3], points[4], points[5]);
                for( t = 0.01; t <= 1; t += 0.01) {
                    p2 = path.getPointOnCubicBezier(t, x, y, points[0], points[1], points[2], points[3], points[4], points[5]);
                    len += path.getLineLength(p1.x, p1.y, p2.x, p2.y);
                    p1 = p2;
                }
                return len;
            case 'Q':
                // Approximates by breaking curve into 100 line segments
                len = 0.0;
                p1 = path.getPointOnQuadraticBezier(0, x, y, points[0], points[1], points[2], points[3]);
                for( t = 0.01; t <= 1; t += 0.01) {
                    p2 = path.getPointOnQuadraticBezier(t, x, y, points[0], points[1], points[2], points[3]);
                    len += path.getLineLength(p1.x, p1.y, p2.x, p2.y);
                    p1 = p2;
                }
                return len;
            case 'A':
                // Approximates by breaking curve into line segments
                len = 0.0;
                var start = points[4];
                // 4 = theta
                var dTheta = points[5];
                // 5 = dTheta
                var end = points[4] + dTheta;
                var inc = Math.PI / 180.0;
                // 1 degree resolution
                if(Math.abs(start - end) < inc) {
                    inc = Math.abs(start - end);
                }
                // Note: for purpose of calculating arc length, not going to worry about rotating X-axis by angle psi
                p1 = path.getPointOnEllipticalArc(points[0], points[1], points[2], points[3], start, 0);
                if(dTheta < 0) {// clockwise
                    for( t = start - inc; t > end; t -= inc) {
                        p2 = path.getPointOnEllipticalArc(points[0], points[1], points[2], points[3], t, 0);
                        len += path.getLineLength(p1.x, p1.y, p2.x, p2.y);
                        p1 = p2;
                    }
                }
                else {// counter-clockwise
                    for( t = start + inc; t < end; t += inc) {
                        p2 = path.getPointOnEllipticalArc(points[0], points[1], points[2], points[3], t, 0);
                        len += path.getLineLength(p1.x, p1.y, p2.x, p2.y);
                        p1 = p2;
                    }
                }
                p2 = path.getPointOnEllipticalArc(points[0], points[1], points[2], points[3], end, 0);
                len += path.getLineLength(p1.x, p1.y, p2.x, p2.y);

                return len;
        }

        return 0;
    };
    Kinetic.Path.convertEndpointToCenterParameterization = function(x1, y1, x2, y2, fa, fs, rx, ry, psiDeg) {
        // Derived from: http://www.w3.org/TR/SVG/implnote.html#ArcImplementationNotes
        var psi = psiDeg * (Math.PI / 180.0);
        var xp = Math.cos(psi) * (x1 - x2) / 2.0 + Math.sin(psi) * (y1 - y2) / 2.0;
        var yp = -1 * Math.sin(psi) * (x1 - x2) / 2.0 + Math.cos(psi) * (y1 - y2) / 2.0;

        var lambda = (xp * xp) / (rx * rx) + (yp * yp) / (ry * ry);

        if(lambda > 1) {
            rx *= Math.sqrt(lambda);
            ry *= Math.sqrt(lambda);
        }

        var f = Math.sqrt((((rx * rx) * (ry * ry)) - ((rx * rx) * (yp * yp)) - ((ry * ry) * (xp * xp))) / ((rx * rx) * (yp * yp) + (ry * ry) * (xp * xp)));

        if(fa == fs) {
            f *= -1;
        }
        if(isNaN(f)) {
            f = 0;
        }

        var cxp = f * rx * yp / ry;
        var cyp = f * -ry * xp / rx;

        var cx = (x1 + x2) / 2.0 + Math.cos(psi) * cxp - Math.sin(psi) * cyp;
        var cy = (y1 + y2) / 2.0 + Math.sin(psi) * cxp + Math.cos(psi) * cyp;

        var vMag = function(v) {
            return Math.sqrt(v[0] * v[0] + v[1] * v[1]);
        };
        var vRatio = function(u, v) {
            return (u[0] * v[0] + u[1] * v[1]) / (vMag(u) * vMag(v));
        };
        var vAngle = function(u, v) {
            return (u[0] * v[1] < u[1] * v[0] ? -1 : 1) * Math.acos(vRatio(u, v));
        };
        var theta = vAngle([1, 0], [(xp - cxp) / rx, (yp - cyp) / ry]);
        var u = [(xp - cxp) / rx, (yp - cyp) / ry];
        var v = [(-1 * xp - cxp) / rx, (-1 * yp - cyp) / ry];
        var dTheta = vAngle(u, v);

        if(vRatio(u, v) <= -1) {
            dTheta = Math.PI;
        }
        if(vRatio(u, v) >= 1) {
            dTheta = 0;
        }
        if(fs === 0 && dTheta > 0) {
            dTheta = dTheta - 2 * Math.PI;
        }
        if(fs == 1 && dTheta < 0) {
            dTheta = dTheta + 2 * Math.PI;
        }
        return [cx, cy, rx, ry, theta, dTheta, psi, fs];
    };
    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Path, 'data');

    /**
     * set SVG path data string.  This method
     *  also automatically parses the data string
     *  into a data array.  Currently supported SVG data:
     *  M, m, L, l, H, h, V, v, Q, q, T, t, C, c, S, s, A, a, Z, z
     * @name setData
     * @method
     * @memberof Kinetic.Path.prototype
     * @param {String} SVG path command string
     */

    /**
     * get SVG path data string
     * @name getData
     * @method
     * @memberof Kinetic.Path.prototype
     */
})();
;(function() {
    var EMPTY_STRING = '',
        CALIBRI = 'Calibri',
        NORMAL = 'normal';

    /**
     * Path constructor.
     * @author Jason Follas
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {String} [config.fontFamily] default is Calibri
     * @param {Number} [config.fontSize] default is 12
     * @param {String} [config.fontStyle] can be normal, bold, or italic.  Default is normal
     * @param {String} config.text
     * @param {String} config.data SVG data string
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var textpath = new Kinetic.TextPath({<br>
     *   x: 100,<br>
     *   y: 50,<br>
     *   fill: '#333',<br>
     *   fontSize: '24',<br>
     *   fontFamily: 'Arial',<br>
     *   text: 'All the world\'s a stage, and all the men and women merely players.',<br>
     *   data: 'M10,10 C0,0 10,150 100,100 S300,150 400,50'<br>
     * });
     */
    Kinetic.TextPath = function(config) {
        this.___init(config);
    };

    function _fillFunc(context) {
        context.fillText(this.partialText, 0, 0);
    }
    function _strokeFunc(context) {
        context.strokeText(this.partialText, 0, 0);
    }

    Kinetic.TextPath.prototype = {
        ___init: function(config) {
            var that = this;
            this.dummyCanvas = document.createElement('canvas');
            this.dataArray = [];

            // call super constructor
            Kinetic.Shape.call(this, config);

            // overrides
            // TODO: shouldn't this be on the prototype?
            this._fillFunc = _fillFunc;
            this._strokeFunc = _strokeFunc;

            this.className = 'TextPath';

            this.dataArray = Kinetic.Path.parsePathData(this.attrs.data);
            this.on('dataChange.kinetic', function() {
                that.dataArray = Kinetic.Path.parsePathData(this.attrs.data);
            });

            // update text data for certain attr changes
            this.on('textChange.kinetic textStroke.kinetic textStrokeWidth.kinetic', that._setTextData);
            that._setTextData();
        },
        drawFunc: function(context) {
            var charArr = this.charArr;

            context.setAttr('font', this._getContextFont());
            context.setAttr('textBaseline', 'middle');
            context.setAttr('textAlign', 'left');
            context.save();

            var glyphInfo = this.glyphInfo;
            for(var i = 0; i < glyphInfo.length; i++) {
                context.save();

                var p0 = glyphInfo[i].p0;
                var p1 = glyphInfo[i].p1;
                var ht = parseFloat(this.attrs.fontSize);

                context.translate(p0.x, p0.y);
                context.rotate(glyphInfo[i].rotation);
                this.partialText = glyphInfo[i].text;

                context.fillStrokeShape(this);
                context.restore();

                //// To assist with debugging visually, uncomment following
                // context.beginPath();
                // if (i % 2)
                // context.strokeStyle = 'cyan';
                // else
                // context.strokeStyle = 'green';

                // context.moveTo(p0.x, p0.y);
                // context.lineTo(p1.x, p1.y);
                // context.stroke();
            }
            context.restore();
        },
        /**
         * get text width in pixels
         * @method
         * @memberof Kinetic.TextPath.prototype
         */
        getTextWidth: function() {
            return this.textWidth;
        },
        /**
         * get text height in pixels
         * @method
         * @memberof Kinetic.TextPath.prototype
         */
        getTextHeight: function() {
            return this.textHeight;
        },
        /**
         * set text
         * @method
         * @memberof Kinetic.TextPath.prototype
         * @param {String} text
         */
        setText: function(text) {
            Kinetic.Text.prototype.setText.call(this, text);
        },
        _getTextSize: function(text) {
            var dummyCanvas = this.dummyCanvas;
            var _context = dummyCanvas.getContext('2d');

            _context.save();

            _context.font = this._getContextFont();
            var metrics = _context.measureText(text);

            _context.restore();

            return {
                width: metrics.width,
                height: parseInt(this.attrs.fontSize, 10)
            };
        },
        _setTextData: function() {

            var that = this;
            var size = this._getTextSize(this.attrs.text);
            this.textWidth = size.width;
            this.textHeight = size.height;

            this.glyphInfo = [];

            var charArr = this.attrs.text.split('');

            var p0, p1, pathCmd;

            var pIndex = -1;
            var currentT = 0;

            var getNextPathSegment = function() {
                currentT = 0;
                var pathData = that.dataArray;

                for(var i = pIndex + 1; i < pathData.length; i++) {
                    if(pathData[i].pathLength > 0) {
                        pIndex = i;

                        return pathData[i];
                    }
                    else if(pathData[i].command == 'M') {
                        p0 = {
                            x: pathData[i].points[0],
                            y: pathData[i].points[1]
                        };
                    }
                }

                return {};
            };
            var findSegmentToFitCharacter = function(c, before) {

                var glyphWidth = that._getTextSize(c).width;

                var currLen = 0;
                var attempts = 0;
                var needNextSegment = false;
                p1 = undefined;
                while(Math.abs(glyphWidth - currLen) / glyphWidth > 0.01 && attempts < 25) {
                    attempts++;
                    var cumulativePathLength = currLen;
                    while(pathCmd === undefined) {
                        pathCmd = getNextPathSegment();

                        if(pathCmd && cumulativePathLength + pathCmd.pathLength < glyphWidth) {
                            cumulativePathLength += pathCmd.pathLength;
                            pathCmd = undefined;
                        }
                    }

                    if(pathCmd === {} || p0 === undefined)
                        return undefined;

                    var needNewSegment = false;

                    switch (pathCmd.command) {
                        case 'L':
                            if(Kinetic.Path.getLineLength(p0.x, p0.y, pathCmd.points[0], pathCmd.points[1]) > glyphWidth) {
                                p1 = Kinetic.Path.getPointOnLine(glyphWidth, p0.x, p0.y, pathCmd.points[0], pathCmd.points[1], p0.x, p0.y);
                            }
                            else
                                pathCmd = undefined;
                            break;
                        case 'A':

                            var start = pathCmd.points[4];
                            // 4 = theta
                            var dTheta = pathCmd.points[5];
                            // 5 = dTheta
                            var end = pathCmd.points[4] + dTheta;

                            if(currentT === 0)
                                currentT = start + 0.00000001;
                            // Just in case start is 0
                            else if(glyphWidth > currLen)
                                currentT += (Math.PI / 180.0) * dTheta / Math.abs(dTheta);
                            else
                                currentT -= Math.PI / 360.0 * dTheta / Math.abs(dTheta);

                            // Credit for bug fix: @therth https://github.com/ericdrowell/KineticJS/issues/249
                            // Old code failed to render text along arc of this path: "M 50 50 a 150 50 0 0 1 250 50 l 50 0"
                            if(dTheta < 0 && currentT < end || dTheta >= 0 && currentT > end) {
                                currentT = end;
                                needNewSegment = true;
                            }
                            p1 = Kinetic.Path.getPointOnEllipticalArc(pathCmd.points[0], pathCmd.points[1], pathCmd.points[2], pathCmd.points[3], currentT, pathCmd.points[6]);
                            break;
                        case 'C':
                            if(currentT === 0) {
                                if(glyphWidth > pathCmd.pathLength)
                                    currentT = 0.00000001;
                                else
                                    currentT = glyphWidth / pathCmd.pathLength;
                            }
                            else if(glyphWidth > currLen)
                                currentT += (glyphWidth - currLen) / pathCmd.pathLength;
                            else
                                currentT -= (currLen - glyphWidth) / pathCmd.pathLength;

                            if(currentT > 1.0) {
                                currentT = 1.0;
                                needNewSegment = true;
                            }
                            p1 = Kinetic.Path.getPointOnCubicBezier(currentT, pathCmd.start.x, pathCmd.start.y, pathCmd.points[0], pathCmd.points[1], pathCmd.points[2], pathCmd.points[3], pathCmd.points[4], pathCmd.points[5]);
                            break;
                        case 'Q':
                            if(currentT === 0)
                                currentT = glyphWidth / pathCmd.pathLength;
                            else if(glyphWidth > currLen)
                                currentT += (glyphWidth - currLen) / pathCmd.pathLength;
                            else
                                currentT -= (currLen - glyphWidth) / pathCmd.pathLength;

                            if(currentT > 1.0) {
                                currentT = 1.0;
                                needNewSegment = true;
                            }
                            p1 = Kinetic.Path.getPointOnQuadraticBezier(currentT, pathCmd.start.x, pathCmd.start.y, pathCmd.points[0], pathCmd.points[1], pathCmd.points[2], pathCmd.points[3]);
                            break;

                    }

                    if(p1 !== undefined) {
                        currLen = Kinetic.Path.getLineLength(p0.x, p0.y, p1.x, p1.y);
                    }

                    if(needNewSegment) {
                        needNewSegment = false;
                        pathCmd = undefined;
                    }
                }
            };
            for(var i = 0; i < charArr.length; i++) {

                // Find p1 such that line segment between p0 and p1 is approx. width of glyph
                findSegmentToFitCharacter(charArr[i]);

                if(p0 === undefined || p1 === undefined)
                    break;

                var width = Kinetic.Path.getLineLength(p0.x, p0.y, p1.x, p1.y);

                // Note: Since glyphs are rendered one at a time, any kerning pair data built into the font will not be used.
                // Can foresee having a rough pair table built in that the developer can override as needed.

                var kern = 0;
                // placeholder for future implementation

                var midpoint = Kinetic.Path.getPointOnLine(kern + width / 2.0, p0.x, p0.y, p1.x, p1.y);

                var rotation = Math.atan2((p1.y - p0.y), (p1.x - p0.x));
                this.glyphInfo.push({
                    transposeX: midpoint.x,
                    transposeY: midpoint.y,
                    text: charArr[i],
                    rotation: rotation,
                    p0: p0,
                    p1: p1
                });
                p0 = p1;
            }
        }
    };

    // map TextPath methods to Text
    Kinetic.TextPath.prototype._getContextFont = Kinetic.Text.prototype._getContextFont;

    Kinetic.Util.extend(Kinetic.TextPath, Kinetic.Shape);

    // add setters and getters
    Kinetic.Factory.addGetterSetter(Kinetic.TextPath, 'fontFamily', CALIBRI);

    /**
     * set font family
     * @name setFontFamily
     * @method
     * @memberof Kinetic.TextPath.prototype
     * @param {String} fontFamily
     */

    /**
     * get font family
     * @name getFontFamily
     * @method
     * @memberof Kinetic.TextPath.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.TextPath, 'fontSize', 12);

    /**
     * set font size
     * @name setFontSize
     * @method
     * @memberof Kinetic.TextPath.prototype
     * @param {int} fontSize
     */

    /**
     * get font size
     * @name getFontSize
     * @method
     * @memberof Kinetic.TextPath.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.TextPath, 'fontStyle', NORMAL);

    /**
     * set font style.  Can be 'normal', 'italic', or 'bold'.  'normal' is the default.
     * @name setFontStyle
     * @method
     * @memberof Kinetic.TextPath.prototype
     * @param {String} fontStyle
     */

    /**
     * get font style
     * @name getFontStyle
     * @method
     * @memberof Kinetic.TextPath.prototype
     */

    Kinetic.Factory.addGetter(Kinetic.TextPath, 'text', EMPTY_STRING);

    /**
     * get text
     * @name getText
     * @method
     * @memberof Kinetic.TextPath.prototype
     */
})();
;(function() {
    /**
     * RegularPolygon constructor.&nbsp; Examples include triangles, squares, pentagons, hexagons, etc.
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Number} config.sides
     * @param {Number} config.radius
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var hexagon = new Kinetic.RegularPolygon({<br>
     *   x: 100,<br>
     *   y: 200,<br>
     *   sides: 6,<br>
     *   radius: 70,<br>
     *   fill: 'red',<br>
     *   stroke: 'black',<br>
     *   strokeWidth: 4<br>
     * });
     */
    Kinetic.RegularPolygon = function(config) {
        this.___init(config);
    };

    Kinetic.RegularPolygon.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'RegularPolygon';
        },
        drawFunc: function(context) {
            var sides = this.attrs.sides,
                radius = this.attrs.radius,
                n, x, y;

            context.beginPath();
            context.moveTo(0, 0 - radius);

            for(n = 1; n < sides; n++) {
                x = radius * Math.sin(n * 2 * Math.PI / sides);
                y = -1 * radius * Math.cos(n * 2 * Math.PI / sides);
                context.lineTo(x, y);
            }
            context.closePath();
            context.fillStrokeShape(this);
        }
    };
    Kinetic.Util.extend(Kinetic.RegularPolygon, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.RegularPolygon, 'radius', 0);

    /**
     * set radius
     * @name setRadius
     * @method
     * @memberof Kinetic.RegularPolygon.prototype
     * @param {Number} radius
     */

    /**
     * get radius
     * @name getRadius
     * @method
     * @memberof Kinetic.RegularPolygon.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.RegularPolygon, 'sides', 0);

    /**
     * set number of sides
     * @name setSides
     * @method
     * @memberof Kinetic.RegularPolygon.prototype
     * @param {int} sides
     */

    /**
     * get number of sides
     * @name getSides
     * @method
     * @memberof Kinetic.RegularPolygon.prototype
     */
})();
;(function() {
    /**
     * Star constructor
     * @constructor
     * @memberof Kinetic
     * @augments Kinetic.Shape
     * @param {Object} config
     * @param {Integer} config.numPoints
     * @param {Number} config.innerRadius
     * @param {Number} config.outerRadius
     * @param {String} [config.fill] fill color
     * @param {Object} [config.fillRGB] set fill color with an object literal containing an r, g, and b component
     * @param {Integer} [config.fillR] set fill red component
     * @param {Integer} [config.fillG] set fill green component
     * @param {Integer} [config.fillB] set fill blue component
     * @param {Image} [config.fillPatternImage] fill pattern image
     * @param {Number} [config.fillPatternX]
     * @param {Number} [config.fillPatternY]
     * @param {Number|Array|Object} [config.fillPatternOffset] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternOffsetX]
     * @param {Number} [config.fillPatternOffsetY]
     * @param {Number|Array|Object} [config.fillPatternScale] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillPatternScaleX]
     * @param {Number} [config.fillPatternScaleY]
     * @param {Number} [config.fillPatternRotation]
     * @param {String} [config.fillPatternRepeat] can be 'repeat', 'repeat-x', 'repeat-y', or 'no-repeat'.  The default is 'no-repeat'
     * @param {Number|Array|Object} [config.fillLinearGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientStartPointX]
     * @param {Number} [config.fillLinearGradientStartPointY]
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number|Array|Object} [config.fillLinearGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillLinearGradientEndPointX]
     * @param {Number} [config.fillLinearGradientEndPointY]
     * @param {Array} [config.fillLinearGradientColorStops] array of color stops
     * @param {Number|Array|Object} [config.fillRadialGradientStartPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientStartPointX]
     * @param {Number} [config.fillRadialGradientStartPointY]
     * @param {Number|Array|Object} [config.fillRadialGradientEndPoint] number, array with two elements, or object with x and y component
     * @param {Number} [config.fillRadialGradientEndPointX]
     * @param {Number} [config.fillRadialGradientEndPointY]
     * @param {Number} [config.fillRadialGradientStartRadius]
     * @param {Number} [config.fillRadialGradientEndRadius]
     * @param {Array} [config.fillRadialGradientColorStops] array of color stops
     * @param {Boolean} [config.fillEnabled] flag which enables or disables the fill.  The default value is true
     * @param {String} [config.fillPriority] can be color, linear-gradient, radial-graident, or pattern.  The default value is color.  The fillPriority property makes it really easy to toggle between different fill types.  For example, if you want to toggle between a fill color style and a fill pattern style, simply set the fill property and the fillPattern properties, and then use setFillPriority('color') to render the shape with a color fill, or use setFillPriority('pattern') to render the shape with the pattern fill configuration
     * @param {String} [config.stroke] stroke color
     * @param {Object} [config.strokeRGB] set stroke color with an object literal containing an r, g, and b component
     * @param {Integer} [config.strokeR] set stroke red component
     * @param {Integer} [config.strokeG] set stroke green component
     * @param {Integer} [config.strokeB] set stroke blue component
     * @param {Number} [config.strokeWidth] stroke width
     * @param {Boolean} [config.strokeScaleEnabled] flag which enables or disables stroke scale.  The default is true
     * @param {Boolean} [config.strokeEnabled] flag which enables or disables the stroke.  The default value is true
     * @param {String} [config.lineJoin] can be miter, round, or bevel.  The default
     *  is miter
     * @param {String} [config.lineCap] can be butt, round, or sqare.  The default
     *  is butt
     * @param {String} [config.shadowColor]
     * @param {Object} [config.shadowColorRGB] set shadowColor color with an object literal containing an r, g, and b component
     * @param {Integer} [config.shadowColorR] set shadowColor red component
     * @param {Integer} [config.shadowColorG] set shadowColor green component
     * @param {Integer} [config.shadowColorB] set shadowColor blue component
     * @param {Number} [config.shadowBlur]
     * @param {Object} [config.shadowOffset]
     * @param {Number} [config.shadowOffsetX]
     * @param {Number} [config.shadowOffsetY]
     * @param {Number} [config.shadowOpacity] shadow opacity.  Can be any real number
     *  between 0 and 1
     * @param {Boolean} [config.shadowEnabled] flag which enables or disables the shadow.  The default value is true
     * @param {Array} [config.dashArray]
     * @param {Boolean} [config.dashArrayEnabled] flag which enables or disables the dashArray.  The default value is true
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * var star = new Kinetic.Star({<br>
     *   x: 100,<br>
     *   y: 200,<br>
     *   numPoints: 5,<br>
     *   innerRadius: 70,<br>
     *   outerRadius: 70,<br>
     *   fill: 'red',<br>
     *   stroke: 'black',<br>
     *   strokeWidth: 4<br>
     * });
     */
    Kinetic.Star = function(config) {
        this.___init(config);
    };

    Kinetic.Star.prototype = {
        ___init: function(config) {
            // call super constructor
            Kinetic.Shape.call(this, config);
            this.className = 'Star';
        },
        drawFunc: function(context) {
            var _context = context._context,
                innerRadius = this.attrs.innerRadius,
                outerRadius = this.attrs.outerRadius,
                numPoints = this.attrs.numPoints;

            _context.beginPath();
            _context.moveTo(0, 0 - this.attrs.outerRadius);

            for(var n = 1; n < numPoints * 2; n++) {
                var radius = n % 2 === 0 ? outerRadius : innerRadius;
                var x = radius * Math.sin(n * Math.PI / numPoints);
                var y = -1 * radius * Math.cos(n * Math.PI / numPoints);
                _context.lineTo(x, y);
            }
            _context.closePath();

            context.fillStrokeShape(this);
        }
    };
    Kinetic.Util.extend(Kinetic.Star, Kinetic.Shape);

    // add getters setters
    Kinetic.Factory.addGetterSetter(Kinetic.Star, 'numPoints', 0);

    /**
     * set number of points
     * @name setNumPoints
     * @method
     * @memberof Kinetic.Star.prototype
     * @param {Integer} points
     */

    /**
     * get number of points
     * @name getNumPoints
     * @method
     * @memberof Kinetic.Star.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Star, 'innerRadius', 0);

    /**
     * set inner radius
     * @name setInnerRadius
     * @method
     * @memberof Kinetic.Star.prototype
     * @param {Number} radius
     */

    /**
     * get inner radius
     * @name getInnerRadius
     * @method
     * @memberof Kinetic.Star.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Star, 'outerRadius', 0);

    /**
     * set outer radius
     * @name setOuterRadius
     * @method
     * @memberof Kinetic.Star.prototype
     * @param {Number} radius
     */

    /**
     * get outer radius
     * @name getOuterRadius
     * @method
     * @memberof Kinetic.Star.prototype
     */
})();
;(function() {
    // constants
    var ATTR_CHANGE_LIST = ['fontFamily', 'fontSize', 'fontStyle', 'padding', 'lineHeight', 'text'],
        CHANGE_KINETIC = 'Change.kinetic',
        NONE = 'none',
        UP = 'up',
        RIGHT = 'right',
        DOWN = 'down',
        LEFT = 'left',
        LABEL = 'Label',

    // cached variables
        attrChangeListLen = ATTR_CHANGE_LIST.length;

    /**
     * Label constructor.&nbsp; Labels are groups that contain a Text and Tag shape
     * @constructor
     * @memberof Kinetic
     * @param {Object} config
     * @param {Number} [config.x]
     * @param {Number} [config.y]
     * @param {Number} [config.width]
     * @param {Number} [config.height]
     * @param {Boolean} [config.visible]
     * @param {Boolean} [config.listening] whether or not the node is listening for events
     * @param {String} [config.id] unique id
     * @param {String} [config.name] non-unique name
     * @param {Number} [config.opacity] determines node opacity.  Can be any number between 0 and 1
     * @param {Object} [config.scale] set scale
     * @param {Number} [config.scaleX] set scale x
     * @param {Number} [config.scaleY] set scale y
     * @param {Number} [config.rotation] rotation in radians
     * @param {Number} [config.rotationDeg] rotation in degrees
     * @param {Object} [config.offset] offset from center point and rotation point
     * @param {Number} [config.offsetX] set offset x
     * @param {Number} [config.offsetY] set offset y
     * @param {Boolean} [config.draggable] makes the node draggable.  When stages are draggable, you can drag and drop
     *  the entire stage by dragging any portion of the stage
     * @param {Function} [config.dragBoundFunc]
     * @example
     * // create label
     * var label = new Kinetic.Label({<br>
     *   x: 100,<br>
     *   y: 100, <br>
     *   draggable: true<br>
     * });<br><br>
     *
     * // add a tag to the label<br>
     * label.add(new Kinetic.Tag({<br>
     *   fill: '#bbb',<br>
     *   stroke: '#333',<br>
     *   shadowColor: 'black',<br>
     *   shadowBlur: 10,<br>
     *   shadowOffset: [10, 10],<br>
     *   shadowOpacity: 0.2,<br>
     *   lineJoin: 'round',<br>
     *   pointerDirection: 'up',<br>
     *   pointerWidth: 20,<br>
     *   pointerHeight: 20,<br>
     *   cornerRadius: 5<br>
     * }));<br><br>
     *
     * // add text to the label<br>
     * label.add(new Kinetic.Text({<br>
     *   text: 'Hello World!',<br>
     *   fontSize: 50,<br>
     *   lineHeight: 1.2,<br>
     *   padding: 10,<br>
     *   fill: 'green'<br>
     *  }));
     */
    Kinetic.Label = function(config) {
        this.____init(config);
    };

    Kinetic.Label.prototype = {
        ____init: function(config) {
            var that = this;

            this.className = LABEL;
            Kinetic.Group.call(this, config);

            this.on('add.kinetic', function(evt) {
                that._addListeners(evt.child);
                that._sync();
            });
        },
        /**
         * get Text shape for the label.  You need to access the Text shape in order to update
         * the text properties
         * @name getText
         * @method
         * @memberof Kinetic.Label.prototype
         */
        getText: function() {
            return this.find('Text')[0];
        },
        /**
         * get Tag shape for the label.  You need to access the Tag shape in order to update
         * the pointer properties and the corner radius
         * @name getTag
         * @method
         * @memberof Kinetic.Label.prototype
         */
        getTag: function() {
            return this.find('Tag')[0];
        },
        _addListeners: function(text) {
            var that = this,
                n;
            var func = function(){
                that._sync();
            };

            // update text data for certain attr changes
            for(n = 0; n < attrChangeListLen; n++) {
                text.on(ATTR_CHANGE_LIST[n] + CHANGE_KINETIC, func);
            }
        },
        getWidth: function() {
            return this.getText().getWidth();
        },
        getHeight: function() {
            return this.getText().getHeight();
        },
        _sync: function() {
            var text = this.getText(),
                tag = this.getTag(),
                width, height, pointerDirection, pointerWidth, x, y;

            if (text && tag) {
                width = text.getWidth(),
                    height = text.getHeight(),
                    pointerDirection = tag.getPointerDirection(),
                    pointerWidth = tag.getPointerWidth(),
                    pointerHeight = tag.getPointerHeight(),
                    x = 0,
                    y = 0;

                switch(pointerDirection) {
                    case UP:
                        x = width / 2;
                        y = -1 * pointerHeight;
                        break;
                    case RIGHT:
                        x = width + pointerWidth;
                        y = height / 2;
                        break;
                    case DOWN:
                        x = width / 2;
                        y = height + pointerHeight;
                        break;
                    case LEFT:
                        x = -1 * pointerWidth;
                        y = height / 2;
                        break;
                }

                tag.setAttrs({
                    x: -1 * x,
                    y: -1 * y,
                    width: width,
                    height: height
                });

                text.setAttrs({
                    x: -1 * x,
                    y: -1 * y
                });
            }
        }
    };

    Kinetic.Util.extend(Kinetic.Label, Kinetic.Group);

    /**
     * Tag constructor.&nbsp; A Tag can be configured
     *  to have a pointer element that points up, right, down, or left
     * @constructor
     * @memberof Kinetic
     * @param {Object} config
     * @param {String} [config.pointerDirection] can be up, right, down, left, or none; the default
     *  is none.  When a pointer is present, the positioning of the label is relative to the tip of the pointer.
     * @param {Number} [config.pointerWidth]
     * @param {Number} [config.pointerHeight]
     * @param {Number} [config.cornerRadius]
     */
    Kinetic.Tag = function(config) {
        this.___init(config);
    };

    Kinetic.Tag.prototype = {
        ___init: function(config) {
            Kinetic.Shape.call(this, config);
            this.className = 'Tag';
        },
        drawFunc: function(context) {
            var width = this.getWidth(),
                height = this.getHeight(),
                pointerDirection = this.getPointerDirection(),
                pointerWidth = this.getPointerWidth(),
                pointerHeight = this.getPointerHeight(),
                cornerRadius = this.getCornerRadius();

            context.beginPath();
            context.moveTo(0,0);

            if (pointerDirection === UP) {
                context.lineTo((width - pointerWidth)/2, 0);
                context.lineTo(width/2, -1 * pointerHeight);
                context.lineTo((width + pointerWidth)/2, 0);
            }

            context.lineTo(width, 0);

            if (pointerDirection === RIGHT) {
                context.lineTo(width, (height - pointerHeight)/2);
                context.lineTo(width + pointerWidth, height/2);
                context.lineTo(width, (height + pointerHeight)/2);
            }

            context.lineTo(width, height);

            if (pointerDirection === DOWN) {
                context.lineTo((width + pointerWidth)/2, height);
                context.lineTo(width/2, height + pointerHeight);
                context.lineTo((width - pointerWidth)/2, height);
            }

            context.lineTo(0, height);

            if (pointerDirection === LEFT) {
                context.lineTo(0, (height + pointerHeight)/2);
                context.lineTo(-1 * pointerWidth, height/2);
                context.lineTo(0, (height - pointerHeight)/2);
            }

            context.closePath();
            context.fillStrokeShape(this);
        }
    };

    Kinetic.Util.extend(Kinetic.Tag, Kinetic.Shape);
    Kinetic.Factory.addGetterSetter(Kinetic.Tag, 'pointerDirection', NONE);

    /**
     * set pointer Direction
     * @name setPointerDirection
     * @method
     * @memberof Kinetic.Tag.prototype
     * @param {String} pointerDirection can be up, right, down, left, or none.  The
     *  default is none
     */

    /**
     * get pointer Direction
     * @name getPointerDirection
     * @method
     * @memberof Kinetic.Tag.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Tag, 'pointerWidth', 0);

    /**
     * set pointer width
     * @name setPointerWidth
     * @method
     * @memberof Kinetic.Tag.prototype
     * @param {Number} pointerWidth
     */

    /**
     * get pointer width
     * @name getPointerWidth
     * @method
     * @memberof Kinetic.Tag.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Tag, 'pointerHeight', 0);

    /**
     * set pointer height
     * @name setPointerHeight
     * @method
     * @memberof Kinetic.Tag.prototype
     * @param {Number} pointerHeight
     */

    /**
     * get pointer height
     * @name getPointerHeight
     * @method
     * @memberof Kinetic.Tag.prototype
     */

    Kinetic.Factory.addGetterSetter(Kinetic.Tag, 'cornerRadius', 0);

    /**
     * set corner radius
     * @name setCornerRadius
     * @method
     * @memberof Kinetic.Tag.prototype
     * @param {Number} corner radius
     */

    /**
     * get corner radius
     * @name getCornerRadius
     * @method
     * @memberof Kinetic.Tag.prototype
     */
})();
;(function() {
    /**
     * Grayscale Filter
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     */
    Kinetic.Filters.Grayscale = function(imageData) {
        var data = imageData.data;
        for(var i = 0; i < data.length; i += 4) {
            var brightness = 0.34 * data[i] + 0.5 * data[i + 1] + 0.16 * data[i + 2];
            // red
            data[i] = brightness;
            // green
            data[i + 1] = brightness;
            // blue
            data[i + 2] = brightness;
        }
    };
})();
;(function() {
    /**
     * Brighten Filter.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     */
    Kinetic.Filters.Brighten = function(imageData) {
        var brightness = this.getFilterBrightness();
        var data = imageData.data;
        for(var i = 0; i < data.length; i += 4) {
            // red
            data[i] += brightness;
            // green
            data[i + 1] += brightness;
            // blue
            data[i + 2] += brightness;
        }
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterBrightness', 0);
    /**
     * get filter brightness.  The brightness is a number between -255 and 255.&nbsp; Positive values
     *  increase the brightness and negative values decrease the brightness, making the image darker
     * @name getFilterBrightness
     * @method
     * @memberof Kinetic.Image.prototype
     */

    /**
     * set filter brightness
     * @name setFilterBrightness
     * @method
     * @memberof Kinetic.Image.prototype
     */
})();
;(function() {
    /**
     * Invert Filter
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     */
    Kinetic.Filters.Invert = function(imageData) {
        var data = imageData.data;
        for(var i = 0; i < data.length; i += 4) {
            // red
            data[i] = 255 - data[i];
            // green
            data[i + 1] = 255 - data[i + 1];
            // blue
            data[i + 2] = 255 - data[i + 2];
        }
    };
})();
;/*
 the Gauss filter
 master repo: https://github.com/pavelpower/kineticjsGaussFilter/
 */
(function() {
    /*

     StackBlur - a fast almost Gaussian Blur For Canvas

     Version:   0.5
     Author:    Mario Klingemann
     Contact:   mario@quasimondo.com
     Website:   http://www.quasimondo.com/StackBlurForCanvas
     Twitter:   @quasimondo

     In case you find this class useful - especially in commercial projects -
     I am not totally unhappy for a small donation to my PayPal account
     mario@quasimondo.de

     Or support me on flattr:
     https://flattr.com/thing/72791/StackBlur-a-fast-almost-Gaussian-Blur-Effect-for-CanvasJavascript

     Copyright (c) 2010 Mario Klingemann

     Permission is hereby granted, free of charge, to any person
     obtaining a copy of this software and associated documentation
     files (the "Software"), to deal in the Software without
     restriction, including without limitation the rights to use,
     copy, modify, merge, publish, distribute, sublicense, and/or sell
     copies of the Software, and to permit persons to whom the
     Software is furnished to do so, subject to the following
     conditions:

     The above copyright notice and this permission notice shall be
     included in all copies or substantial portions of the Software.

     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
     EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
     OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
     NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
     HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
     WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
     FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
     OTHER DEALINGS IN THE SOFTWARE.
     */

    function BlurStack() {
        this.r = 0;
        this.g = 0;
        this.b = 0;
        this.a = 0;
        this.next = null;
    }

    var mul_table = [
        512,512,456,512,328,456,335,512,405,328,271,456,388,335,292,512,
        454,405,364,328,298,271,496,456,420,388,360,335,312,292,273,512,
        482,454,428,405,383,364,345,328,312,298,284,271,259,496,475,456,
        437,420,404,388,374,360,347,335,323,312,302,292,282,273,265,512,
        497,482,468,454,441,428,417,405,394,383,373,364,354,345,337,328,
        320,312,305,298,291,284,278,271,265,259,507,496,485,475,465,456,
        446,437,428,420,412,404,396,388,381,374,367,360,354,347,341,335,
        329,323,318,312,307,302,297,292,287,282,278,273,269,265,261,512,
        505,497,489,482,475,468,461,454,447,441,435,428,422,417,411,405,
        399,394,389,383,378,373,368,364,359,354,350,345,341,337,332,328,
        324,320,316,312,309,305,301,298,294,291,287,284,281,278,274,271,
        268,265,262,259,257,507,501,496,491,485,480,475,470,465,460,456,
        451,446,442,437,433,428,424,420,416,412,408,404,400,396,392,388,
        385,381,377,374,370,367,363,360,357,354,350,347,344,341,338,335,
        332,329,326,323,320,318,315,312,310,307,304,302,299,297,294,292,
        289,287,285,282,280,278,275,273,271,269,267,265,263,261,259];

    var shg_table = [
        9, 11, 12, 13, 13, 14, 14, 15, 15, 15, 15, 16, 16, 16, 16, 17,
        17, 17, 17, 17, 17, 17, 18, 18, 18, 18, 18, 18, 18, 18, 18, 19,
        19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 19, 20, 20, 20,
        20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 21,
        21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21,
        21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 22, 22, 22, 22, 22, 22,
        22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22,
        22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 23,
        23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23,
        23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23,
        23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23,
        23, 23, 23, 23, 23, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24,
        24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24,
        24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24,
        24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24,
        24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24 ];

    function filterGaussBlurRGBA( imageData, radius) {

        var pixels = imageData.data,
            width = imageData.width,
            height = imageData.height;

        var x, y, i, p, yp, yi, yw, r_sum, g_sum, b_sum, a_sum,
            r_out_sum, g_out_sum, b_out_sum, a_out_sum,
            r_in_sum, g_in_sum, b_in_sum, a_in_sum,
            pr, pg, pb, pa, rbs;

        var div = radius + radius + 1,
            widthMinus1  = width - 1,
            heightMinus1 = height - 1,
            radiusPlus1  = radius + 1,
            sumFactor = radiusPlus1 * ( radiusPlus1 + 1 ) / 2,
            stackStart = new BlurStack(),
            stackEnd = null,
            stack = stackStart,
            stackIn = null,
            stackOut = null,
            mul_sum = mul_table[radius],
            shg_sum = shg_table[radius];

        for ( i = 1; i < div; i++ ) {
            stack = stack.next = new BlurStack();
            if ( i == radiusPlus1 ) stackEnd = stack;
        }

        stack.next = stackStart;

        yw = yi = 0;

        for ( y = 0; y < height; y++ )
        {
            r_in_sum = g_in_sum = b_in_sum = a_in_sum = r_sum = g_sum = b_sum = a_sum = 0;

            r_out_sum = radiusPlus1 * ( pr = pixels[yi] );
            g_out_sum = radiusPlus1 * ( pg = pixels[yi+1] );
            b_out_sum = radiusPlus1 * ( pb = pixels[yi+2] );
            a_out_sum = radiusPlus1 * ( pa = pixels[yi+3] );

            r_sum += sumFactor * pr;
            g_sum += sumFactor * pg;
            b_sum += sumFactor * pb;
            a_sum += sumFactor * pa;

            stack = stackStart;

            for( i = 0; i < radiusPlus1; i++ )
            {
                stack.r = pr;
                stack.g = pg;
                stack.b = pb;
                stack.a = pa;
                stack = stack.next;
            }

            for( i = 1; i < radiusPlus1; i++ )
            {
                p = yi + (( widthMinus1 < i ? widthMinus1 : i ) << 2 );
                r_sum += ( stack.r = ( pr = pixels[p])) * ( rbs = radiusPlus1 - i );
                g_sum += ( stack.g = ( pg = pixels[p+1])) * rbs;
                b_sum += ( stack.b = ( pb = pixels[p+2])) * rbs;
                a_sum += ( stack.a = ( pa = pixels[p+3])) * rbs;

                r_in_sum += pr;
                g_in_sum += pg;
                b_in_sum += pb;
                a_in_sum += pa;

                stack = stack.next;
            }


            stackIn = stackStart;
            stackOut = stackEnd;
            for ( x = 0; x < width; x++ )
            {
                pixels[yi+3] = pa = (a_sum * mul_sum) >> shg_sum;
                if ( pa !== 0 )
                {
                    pa = 255 / pa;
                    pixels[yi]   = ((r_sum * mul_sum) >> shg_sum) * pa;
                    pixels[yi+1] = ((g_sum * mul_sum) >> shg_sum) * pa;
                    pixels[yi+2] = ((b_sum * mul_sum) >> shg_sum) * pa;
                } else {
                    pixels[yi] = pixels[yi+1] = pixels[yi+2] = 0;
                }

                r_sum -= r_out_sum;
                g_sum -= g_out_sum;
                b_sum -= b_out_sum;
                a_sum -= a_out_sum;

                r_out_sum -= stackIn.r;
                g_out_sum -= stackIn.g;
                b_out_sum -= stackIn.b;
                a_out_sum -= stackIn.a;

                p =  ( yw + ( ( p = x + radius + 1 ) < widthMinus1 ? p : widthMinus1 ) ) << 2;

                r_in_sum += ( stackIn.r = pixels[p]);
                g_in_sum += ( stackIn.g = pixels[p+1]);
                b_in_sum += ( stackIn.b = pixels[p+2]);
                a_in_sum += ( stackIn.a = pixels[p+3]);

                r_sum += r_in_sum;
                g_sum += g_in_sum;
                b_sum += b_in_sum;
                a_sum += a_in_sum;

                stackIn = stackIn.next;

                r_out_sum += ( pr = stackOut.r );
                g_out_sum += ( pg = stackOut.g );
                b_out_sum += ( pb = stackOut.b );
                a_out_sum += ( pa = stackOut.a );

                r_in_sum -= pr;
                g_in_sum -= pg;
                b_in_sum -= pb;
                a_in_sum -= pa;

                stackOut = stackOut.next;

                yi += 4;
            }
            yw += width;
        }


        for ( x = 0; x < width; x++ )
        {
            g_in_sum = b_in_sum = a_in_sum = r_in_sum = g_sum = b_sum = a_sum = r_sum = 0;

            yi = x << 2;
            r_out_sum = radiusPlus1 * ( pr = pixels[yi]);
            g_out_sum = radiusPlus1 * ( pg = pixels[yi+1]);
            b_out_sum = radiusPlus1 * ( pb = pixels[yi+2]);
            a_out_sum = radiusPlus1 * ( pa = pixels[yi+3]);

            r_sum += sumFactor * pr;
            g_sum += sumFactor * pg;
            b_sum += sumFactor * pb;
            a_sum += sumFactor * pa;

            stack = stackStart;

            for( i = 0; i < radiusPlus1; i++ )
            {
                stack.r = pr;
                stack.g = pg;
                stack.b = pb;
                stack.a = pa;
                stack = stack.next;
            }

            yp = width;

            for( i = 1; i <= radius; i++ )
            {
                yi = ( yp + x ) << 2;

                r_sum += ( stack.r = ( pr = pixels[yi])) * ( rbs = radiusPlus1 - i );
                g_sum += ( stack.g = ( pg = pixels[yi+1])) * rbs;
                b_sum += ( stack.b = ( pb = pixels[yi+2])) * rbs;
                a_sum += ( stack.a = ( pa = pixels[yi+3])) * rbs;

                r_in_sum += pr;
                g_in_sum += pg;
                b_in_sum += pb;
                a_in_sum += pa;

                stack = stack.next;

                if( i < heightMinus1 )
                {
                    yp += width;
                }
            }

            yi = x;
            stackIn = stackStart;
            stackOut = stackEnd;
            for ( y = 0; y < height; y++ )
            {
                p = yi << 2;
                pixels[p+3] = pa = (a_sum * mul_sum) >> shg_sum;
                if ( pa > 0 )
                {
                    pa = 255 / pa;
                    pixels[p]   = ((r_sum * mul_sum) >> shg_sum ) * pa;
                    pixels[p+1] = ((g_sum * mul_sum) >> shg_sum ) * pa;
                    pixels[p+2] = ((b_sum * mul_sum) >> shg_sum ) * pa;
                } else {
                    pixels[p] = pixels[p+1] = pixels[p+2] = 0;
                }

                r_sum -= r_out_sum;
                g_sum -= g_out_sum;
                b_sum -= b_out_sum;
                a_sum -= a_out_sum;

                r_out_sum -= stackIn.r;
                g_out_sum -= stackIn.g;
                b_out_sum -= stackIn.b;
                a_out_sum -= stackIn.a;

                p = ( x + (( ( p = y + radiusPlus1) < heightMinus1 ? p : heightMinus1 ) * width )) << 2;

                r_sum += ( r_in_sum += ( stackIn.r = pixels[p]));
                g_sum += ( g_in_sum += ( stackIn.g = pixels[p+1]));
                b_sum += ( b_in_sum += ( stackIn.b = pixels[p+2]));
                a_sum += ( a_in_sum += ( stackIn.a = pixels[p+3]));

                stackIn = stackIn.next;

                r_out_sum += ( pr = stackOut.r );
                g_out_sum += ( pg = stackOut.g );
                b_out_sum += ( pb = stackOut.b );
                a_out_sum += ( pa = stackOut.a );

                r_in_sum -= pr;
                g_in_sum -= pg;
                b_in_sum -= pb;
                a_in_sum -= pa;

                stackOut = stackOut.next;

                yi += width;
            }
        }
    }

    /**
     * Blur Filter
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     */
    Kinetic.Filters.Blur = function(imageData) {
        var radius = this.getFilterRadius() | 0;

        if (radius > 0) {
            filterGaussBlurRGBA(imageData, radius);
        }
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterRadius', 0);

})();
;(function() {

    function pixelAt(idata, x, y) {
        var idx = (y * idata.width + x) * 4;
        var d = [];
        d.push(idata.data[idx++], idata.data[idx++], idata.data[idx++], idata.data[idx++]);
        return d;
    }

    function rgbDistance(p1, p2) {
        return Math.sqrt(Math.pow(p1[0] - p2[0], 2) + Math.pow(p1[1] - p2[1], 2) + Math.pow(p1[2] - p2[2], 2));
    }

    function rgbMean(pTab) {
        var m = [0, 0, 0];

        for (var i = 0; i < pTab.length; i++) {
            m[0] += pTab[i][0];
            m[1] += pTab[i][1];
            m[2] += pTab[i][2];
        }

        m[0] /= pTab.length;
        m[1] /= pTab.length;
        m[2] /= pTab.length;

        return m;
    }

    function backgroundMask(idata, threshold) {
        var rgbv_no = pixelAt(idata, 0, 0);
        var rgbv_ne = pixelAt(idata, idata.width - 1, 0);
        var rgbv_so = pixelAt(idata, 0, idata.height - 1);
        var rgbv_se = pixelAt(idata, idata.width - 1, idata.height - 1);


        var thres = threshold || 10;
        if (rgbDistance(rgbv_no, rgbv_ne) < thres && rgbDistance(rgbv_ne, rgbv_se) < thres && rgbDistance(rgbv_se, rgbv_so) < thres && rgbDistance(rgbv_so, rgbv_no) < thres) {

            // Mean color
            var mean = rgbMean([rgbv_ne, rgbv_no, rgbv_se, rgbv_so]);

            // Mask based on color distance
            var mask = [];
            for (var i = 0; i < idata.width * idata.height; i++) {
                var d = rgbDistance(mean, [idata.data[i * 4], idata.data[i * 4 + 1], idata.data[i * 4 + 2]]);
                mask[i] = (d < thres) ? 0 : 255;
            }

            return mask;
        }
    }

    function applyMask(idata, mask) {
        for (var i = 0; i < idata.width * idata.height; i++) {
            idata.data[4 * i + 3] = mask[i];
        }
    }

    function erodeMask(mask, sw, sh) {

        var weights = [1, 1, 1, 1, 0, 1, 1, 1, 1];
        var side = Math.round(Math.sqrt(weights.length));
        var halfSide = Math.floor(side / 2);

        var maskResult = [];
        for (var y = 0; y < sh; y++) {
            for (var x = 0; x < sw; x++) {

                var so = y * sw + x;
                var a = 0;
                for (var cy = 0; cy < side; cy++) {
                    for (var cx = 0; cx < side; cx++) {
                        var scy = y + cy - halfSide;
                        var scx = x + cx - halfSide;

                        if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {

                            var srcOff = scy * sw + scx;
                            var wt = weights[cy * side + cx];

                            a += mask[srcOff] * wt;
                        }
                    }
                }

                maskResult[so] = (a === 255 * 8) ? 255 : 0;
            }
        }

        return maskResult;
    }

    function dilateMask(mask, sw, sh) {

        var weights = [1, 1, 1, 1, 1, 1, 1, 1, 1];
        var side = Math.round(Math.sqrt(weights.length));
        var halfSide = Math.floor(side / 2);

        var maskResult = [];
        for (var y = 0; y < sh; y++) {
            for (var x = 0; x < sw; x++) {

                var so = y * sw + x;
                var a = 0;
                for (var cy = 0; cy < side; cy++) {
                    for (var cx = 0; cx < side; cx++) {
                        var scy = y + cy - halfSide;
                        var scx = x + cx - halfSide;

                        if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {

                            var srcOff = scy * sw + scx;
                            var wt = weights[cy * side + cx];

                            a += mask[srcOff] * wt;
                        }
                    }
                }

                maskResult[so] = (a >= 255 * 4) ? 255 : 0;
            }
        }

        return maskResult;
    }

    function smoothEdgeMask(mask, sw, sh) {

        var weights = [1 / 9, 1 / 9, 1 / 9, 1 / 9, 1 / 9, 1 / 9, 1 / 9, 1 / 9, 1 / 9];
        var side = Math.round(Math.sqrt(weights.length));
        var halfSide = Math.floor(side / 2);

        var maskResult = [];
        for (var y = 0; y < sh; y++) {
            for (var x = 0; x < sw; x++) {

                var so = y * sw + x;
                var a = 0;
                for (var cy = 0; cy < side; cy++) {
                    for (var cx = 0; cx < side; cx++) {
                        var scy = y + cy - halfSide;
                        var scx = x + cx - halfSide;

                        if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {

                            var srcOff = scy * sw + scx;
                            var wt = weights[cy * side + cx];

                            a += mask[srcOff] * wt;
                        }
                    }
                }

                maskResult[so] = a;
            }
        }

        return maskResult;
    }

    /**
     * Mask Filter
     *
     * Only crop unicolor background images for instance
     *
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     */
    Kinetic.Filters.Mask = function(idata) {
        // Detect pixels close to the background color
        var threshold = this.getFilterThreshold(),
            mask = backgroundMask(idata, threshold);
        if (mask) {
            // Erode
            mask = erodeMask(mask, idata.width, idata.height);

            // Dilate
            mask = dilateMask(mask, idata.width, idata.height);

            // Gradient
            mask = smoothEdgeMask(mask, idata.width, idata.height);

            // Apply mask
            applyMask(idata, mask);

            // todo : Update hit region function according to mask
        }

        return idata;
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterThreshold', 0);

    //threshold The RGB euclidian distance threshold (default : 10)

})();
;(function() {

    var rgb_to_hsl = function(r,g,b){
        // Input colors are in 0-255, calculations are between 0-1
        r /= 255; g /= 255; b /= 255;

        // http://en.wikipedia.org/wiki/HSL_and_HSV
        // Convert to HSL
        var max = Math.max(r,g,b),
            min = Math.min(r,g,b),
            chroma = max - min,
            luminance = chroma / 2,
            saturation = chroma / (1 - Math.abs(2*luminance-1)),
            hue = 0;

        if( max == r ){ hue = ((g-b)/chroma) % 6; }else
        if( max == g ){ hue =  (b-r)/chroma  + 2; }else
        if( max == b ){ hue =  (r-g)/chroma  + 4; }

        return [(hue*60+360) % 360, saturation, luminance];
    };

    var pixelShiftHue = function(r,g,b,deg){

        // Input colors are in 0-255, calculations are between 0-1
        r /= 255; g /= 255; b /= 255;

        // http://en.wikipedia.org/wiki/HSL_and_HSV
        // Convert to HSL
        var max = Math.max(r,g,b),
            min = Math.min(r,g,b),
            chroma = max - min,
            luminance = chroma / 2,
            saturation = chroma / (1 - Math.abs(2*luminance-1)),
            hue = 0;

        if( max == r ){ hue = ((g-b)/chroma) % 6; }else
        if( max == g ){ hue =  (b-r)/chroma  + 2; }else
        if( max == b ){ hue =  (r-g)/chroma  + 4; }

        hue *= 60;
        hue %= 360;

        // Shift hue
        hue += deg;
        hue %= 360;
        //hue /= 360;

        // hsl to rgb:
        hue /= 60;
        var rR = 0, rG = 0, rB = 0,
        //chroma = saturation*(1 - Math.abs(2*luminance - 1)),
            tmp = chroma * (1-Math.abs(hue % 2 - 1)),
            m = luminance - chroma/2;

        if( 0 <= hue && hue < 1 ){ rR = chroma; rG = tmp; }else
        if( 1 <= hue && hue < 2 ){ rG = chroma; rR = tmp; }else
        if( 2 <= hue && hue < 3 ){ rG = chroma; rB = tmp; }else
        if( 3 <= hue && hue < 4 ){ rB = chroma; rG = tmp; }else
        if( 4 <= hue && hue < 5 ){ rB = chroma; rR = tmp; }else
        if( 5 <= hue && hue < 6 ){ rR = chroma; rB = tmp; }

        rR += m; rG += m; rB += m;
        rR = (255*rR);
        rG = (255*rG);
        rB = (255*rB);

        return [rR,rG,rB];
    };

    var shift_hue = function(imageData,deg){
        var data = imageData.data,
            pixel;
        for(var i = 0; i < data.length; i += 4) {
            pixel = pixelShiftHue(data[i+0],data[i+1],data[i+2],deg);
            data[i+0] = pixel[0];
            data[i+1] = pixel[1];
            data[i+2] = pixel[2];
        }
    };

    /**
     * Shift Hue Filter.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.ShiftHue = function(imageData) {
        shift_hue(imageData, this.getFilterHueShiftDeg() % 360 );
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterHueShiftDeg', 0);
    /**
     * get hue shift amount.  The shift amount is a number between 0 and 360.
     * @name getFilterBrightness
     * @method
     * @memberof Kinetic.Image.prototype
     */

    /**
     * set hue shift amount
     * @name setFilterBrightness
     * @method
     * @memberof Kinetic.Image.prototype
     */


    /**
     * Colorize Filter.
     *  colorizes the image so that it is just varying shades of the specified color
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.Colorize = function(imageData) {
        var data = imageData.data;

        // First we'll colorize it red, then shift by the hue specified
        var color = this.getFilterColorizeColor(),
            hsl = rgb_to_hsl(color[0],color[1],color[2]),
            hue = hsl[0];

        // Color it red, by removing green and blue
        for(var i = 0; i < data.length; i += 4) {
            data[i + 1] = 0;
            data[i + 2] = 0;
        }

        // Shift by the hue
        shift_hue(imageData,hue);
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterColorizeColor', [255,0,0] );
    /**
     * Gets the colorizing color.
     * @name getFilterColorizeColor
     * @method
     * @memberof Kinetic.Image.prototype
     */

    /**
     * Gets the colorizing color. Should be an array [r,g,b] ie [255,0,128].
     *  note that white [255,255,255] black [0,0,0] and greys [r,r,r] get treated as red.
     * @name setFilterColorizeColor
     * @method
     * @memberof Kinetic.Image.prototype
     */

})();

;(function() {

    var convolve_internal = function(imageData,matrix){
        // Input data
        var pixels = imageData.data,
            imageSizeX = imageData.width,
            imageSizeY = imageData.height,
            nPixels = imageSizeX*imageSizeY,
            pixel;

        // An array for storing the result
        var result = [];
        result.length = imageSizeX*imageSizeY*4;

        // Determine the size and demsionality of the matrix
        // Note: it should be square and odd (3,5,7,9 etc...)
        var matrixSizeX = matrix.length,
            matrixSizeY = matrix[0].length,
            matrixMidX = Math.floor(matrix.length/2),
            matrixMidY = Math.floor(matrix[0].length/2);

        // Accumlators and positions for iterating
        var r,g,b,a, x,y, px,py, pos, i,j;

        // Handle the 2D matrix
        for( y=0; y<imageSizeY; y+=1){
            for( x=0; x<imageSizeX; x+=1){

                // Perform the convolution
                r = 0; g = 0; b = 0; a = 0;
                for( i=0; i<matrixSizeX; i+=1){
                    for( j=0; j<matrixSizeY; j+=1){

                        // tile the image to account for pixels past the
                        // edge (and make sure they are positive)
                        px = (x+i-matrixMidX) % imageSizeX;
                        px = (px>0)?px:-px;
                        py = (y+i-matrixMidY) % imageSizeY;
                        py = (py>0)?py:-py;

                        // get the pixel and convolve
                        pos = (py*imageSizeX + px)*4;
                        r += matrix[j][i]*pixels[pos+0];
                        g += matrix[j][i]*pixels[pos+1];
                        b += matrix[j][i]*pixels[pos+2];
                        //a += matrix[j][i]*pixels[pos+3];
                    }
                }

                // Store the result
                pos = (y*imageSizeX+x)*4;
                result[pos+0] = r;
                result[pos+1] = g;
                result[pos+2] = b;
                //result[pos+3] = a;
            }
        }

        // copy the result to the original canvas
        var lastPos = nPixels*4;
        for( pos=0; pos<lastPos; pos+=4 ){
            pixels[pos+0] = result[pos+0];
            pixels[pos+1] = result[pos+1];
            pixels[pos+2] = result[pos+2];
            //pixels[pos+3] = result[pos+3];
        }
    };

    // Definition of a gaussian function
    var gaussian = function(x,mean,sigma){
        var dx = x - mean;
        return Math.pow(Math.E, -dx*dx / (2*sigma*sigma));
    };

    var make_blur_kernel = function( size, scale, sigma ){

        // make sure size is odd:
        if( size % 2 === 0 ){ size += 1; }

        // Generate the kernel, we can just multiply 2 single dimensional
        // gaussians to get a 2D guassian
        var kernel = [], i,j, row;
        for( i=0; i<size; i+=1 ){
            row = [];
            for( j=0; j<size; j+=1 ){
                row.push( scale * gaussian(i,size/2,sigma) * gaussian(j,size/2,sigma) );
            }
            kernel.push(row);
        }

        return kernel;
    };

    var make_edge_detect_kernel = function( size, scale, sigma ){

        // make sure size is odd:
        if( size % 2 === 0 ){ size += 1; }

        // Create a difference-of-gaussians kernel (by subtracting gaussians)
        // 1.6 is a good sigma ratio to approximate a laplacian of gaussian
        var kernel = [], i,j, row, g;
        for( i=0; i<size; i+=1 ){
            row = [];
            for( j=0; j<size; j+=1 ){
                g1 = gaussian(i,size/2,sigma) * gaussian(j,size/2,sigma);
                g2 = gaussian(i,size/2,sigma*1.6) * gaussian(j,size/2,sigma*1.6);
                row.push( scale * (g2-g1) );
            }
            kernel.push(row);
        }

        return kernel;
    };

    var make_soft_blur_kernel = function( size, percent ){
        // A soft blur is achieve by blurring the image then
        // merging the blured and unblurred image (ie 60/40).
        // Instead of that we've scaling the blur kernel (ie 60)
        // and adding the identity scaled (ie 40) to the kernel
        var kernel = make_blur_kernel( size, percent, 1 ),
            mid = Math.floor(size/2);
        kernel[mid][mid] += 1-percent;
        return kernel;
    };

    var make_unsharp_kernel = function( size, percent ){
        // An 'unsharp mask' is made by blurring the inverted image
        // and combining it with the original (like a soft blur but
        // with the blur negated). We can achieve this by negating
        // blur kernel, and adding twice the identity to that kernel.
        var kernel = make_blur_kernel( size, -percent, 1 ),
            mid = Math.floor(size/2);
        kernel[mid][mid] += 1+percent;
        return kernel;
    };

    Kinetic.Factory.addFilterGetterSetter(Kinetic.Image, 'filterAmount', 50);
    /**
     * get the current filter amount
     * @name getFilterAmount
     * @method
     * @memberof Kinetic.Image.prototype
     */
    /**
     * set the current filter amount 0 = no filter, 100 = max filter
     * @name setFilterAmount
     * @method
     * @memberof Kinetic.Image.prototype
     */


    /**
     * Unsharp Mask Filter.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.UnsharpMask = function(imageData) {
        convolve_internal(imageData,
            make_unsharp_kernel(5,this.getFilterAmount()/100)
        );
    };

    /**
     * Soft Blur Filter.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.SoftBlur = function(imageData) {
        convolve_internal(imageData,
            make_soft_blur_kernel(5,this.getFilterAmount()/100)
        );
    };


    /**
     * Edge Filter.
     *  Makes edges more noticable.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.Edge = function(imageData) {
        var s = this.getFilterAmount()/100;
        if( s === 0 ){ return; }
        convolve_internal(imageData,[
            [ 0,  -1*s, 0],
            [-1*s,(1-s)+4*s,-1*s],
            [ 0,  -1*s, 0]
        ]);
    };

    /**
     * Emboss Filter.
     *  Makes the image apear to have some depth.
     * @function
     * @memberof Kinetic.Filters
     * @param {Object} imageData
     * @author ippo615
     */
    Kinetic.Filters.Emboss = function(imageData) {
        var s = this.getFilterAmount()/100;
        if( s === 0 ){ return; }
        convolve_internal(imageData,[
            [-1*s,-0.5*s, 0],
            [-0.5*s,1+0.5*s, 0.5*s],
            [ 0,    0.5*s, 1*s]
        ]);
    };

})();
