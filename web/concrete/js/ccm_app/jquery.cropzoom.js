/*
CropZoom v1.1
Release Date: April 17, 2010

Copyright (c) 2010 Gaston Robledo

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 */
;
(function($) {

	$.fn.cropzoom = function(options) {

		return this
				.each(function() {

					var _self = null;
					var tMovement = null;

					var $selector = null;
					var $image = null;
					var $svg = null;

					var defaults = {
						width : 500,
						height : 375,
						bgColor : '#000',
						overlayColor : '#000',
						selector : {
							x : 0,
							y : 0,
							w : 229,
							h : 100,
							aspectRatio : false,
							centered : false,
							borderColor : 'yellow',
							borderColorHover : 'red',
							bgInfoLayer : '#FFF',
							infoFontSize : 10,
							infoFontColor : 'blue',
							showPositionsOnDrag : true,
							showDimetionsOnDrag : true,
							maxHeight : null,
							maxWidth : null,
							minHeight: null,
							minWidth : null,
							startWithOverlay : false,
							hideOverlayOnDragAndResize : true,
							onSelectorDrag : null,
							onSelectorDragStop : null,
							onSelectorResize : null,
							onSelectorResizeStop : null
						},
						image : {
							source : '',
							rotation : 0,
							width : 0,
							height : 0,
							minZoom : 10,
							maxZoom : 150,
							startZoom : 0,
							x : 0,
							y : 0,
							useStartZoomAsMinZoom : false,
							snapToContainer : false,
							onZoom : null,
							onRotate : null,
							onImageDrag : null
						},
						enableRotation : true,
						enableZoom : true,
						zoomSteps : 1,
						rotationSteps : 5,
						expose : {
							slidersOrientation : 'vertical',
							zoomElement : '',
							rotationElement : '',
							elementMovement : '',
							movementSteps : 5
						}
					};

					var $options = $.extend(true, defaults, options);
					// Verificamos que esten los plugins necesarios
					if (!$.isFunction($.fn.draggable)
							|| !$.isFunction($.fn.resizable)
							|| !$.isFunction($.fn.slider)) {
						alert("You must include ui.draggable, ui.resizable and ui.slider to use cropZoom");
						return;
					}

					if ($options.image.source == ''
							|| $options.image.width == 0
							|| $options.image.height == 0) {
						alert('You must set the source, witdth and height of the image element');
						return;
					}

					_self = $(this);
					_self.empty();
					_self.css({
						'width' : $options.width,
						'height' : $options.height,
						'background-color' : $options.bgColor,
						'overflow' : 'hidden',
						'position' : 'relative',
						'border' : '2px solid #333'
					});

					setData('image', {
						h : $options.image.height,
						w : $options.image.width,
						posY : $options.image.y,
						posX : $options.image.x,
						scaleX : 0,
						scaleY : 0,
						rotation : $options.image.rotation,
						source : $options.image.source,
						bounds : [ 0, 0, 0, 0 ],
						id : 'image_to_crop_' + _self[0].id
					});

					calculateFactor();
					getCorrectSizes();

					setData(
							'selector',
							{
								x : $options.selector.x,
								y : $options.selector.y,
								w : ($options.selector.maxWidth != null ? ($options.selector.w > $options.selector.maxWidth ? $options.selector.maxWidth
										: $options.selector.w)
										: $options.selector.w),
								h : ($options.selector.maxHeight != null ? ($options.selector.h > $options.selector.maxHeight ? $options.selector.maxHeight
										: $options.selector.h)
										: $options.selector.h)
							});

					if ($.browser.msie) {

						// Add VML includes and namespace
						_self[0].ownerDocument.namespaces
								.add('v', 'urn:schemas-microsoft-com:vml',
										"#default#VML");
						// Add required css rules
						var style = document.createStyleSheet();
						style
								.addRule('v\\:image',
										"behavior: url(#default#VML);display:inline-block");
						style.addRule('v\\:image', "antiAlias: false;");

						$svg = $("<div />").attr("id", "k").css({
							'width' : $options.width,
							'height' : $options.height,
							'position' : 'absolute'
						});
						if ($.support.leadingWhitespace) {
							$image = document.createElement('img');
						} else {
							$image = document.createElement('v:image');
						}
						$image.setAttribute('src', $options.image.source);
						$image.setAttribute('gamma', '0');

						$($image).css({
							'position' : 'absolute',
							'left' : getData('image').posX,
							'top' : getData('image').posY,
							'width' : getData('image').w,
							'height' : getData('image').h
						});
						$image.setAttribute('coordsize', '21600,21600');
						$image.outerHTML = $image.outerHTML;

						var ext = getExtensionSource();
						if (ext == 'png' || ext == 'gif')
							$image.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"
									+ $options.image.source
									+ "',sizingMethod='scale');";
						$svg.append($image);

					} else {
						$svg = _self[0].ownerDocument.createElementNS(
								'http://www.w3.org/2000/svg', 'svg');
						$svg.setAttribute('id', 'k');
						$svg.setAttribute('width', $options.width);
						$svg.setAttribute('height', $options.height);
						$svg.setAttribute('preserveAspectRatio', 'none');
						$image = _self[0].ownerDocument.createElementNS(
								'http://www.w3.org/2000/svg', 'image');
						$image.setAttributeNS('http://www.w3.org/1999/xlink',
								'href', $options.image.source);
						$image.setAttribute('width', getData('image').w);
						$image.setAttribute('height', getData('image').h);
						$image.setAttribute('preserveAspectRatio', 'none');
						$($image).attr('x', 0);
						$($image).attr('y', 0);
						$svg.appendChild($image);
					}
					// $($image).data('container_id', _self[0].id);
					_self.append($svg);

					calculateTranslationAndRotation();

					// Bindear el drageo a la imagen a cortar
					$($image).draggable({
						refreshPositions : true,
						start : function(event, ui) {
							// calculateTranslationAndRotation();
						},
						drag : function(event, ui) {
							getData('image').posY = ui.position.top;
							getData('image').posX = ui.position.left;
							if ($options.image.snapToContainer)
								limitBounds(ui);
							else
								calculateTranslationAndRotation();
							// Fire the callback
							if ($options.image.onImageDrag != null)
								$options.image.onImageDrag($image);

						},
						stop : function(event, ui) {
							if ($options.image.snapToContainer)
								limitBounds(ui);
						}
					});

					// Creamos el selector
					createSelector();
					// Cambiamos el resizable por un color solido
					_self.find('.ui-icon-gripsmall-diagonal-se').css({
						'background' : '#FFF',
						'border' : '1px solid #000',
						'width' : 8,
						'height' : 8
					});
					// Creamos la Capa de oscurecimiento
					createOverlay();

					if ($options.selector.startWithOverlay) {
						/* Make Overlays at Start */
						var ui_object = {
							position : {
								top : $selector.position().top,
								left : $selector.position().left
							}
						};
						makeOverlayPositions(ui_object);
					}
					/* End Make Overlay at start */

					// Creamos el Control de Zoom
					if ($options.enableZoom)
						createZoomSlider();
					// Creamos el Control de Rotacion
					if ($options.enableRotation)
						createRotationSlider();
					if ($options.expose.elementMovement != '')
						createMovementControls();

					// Methods
					/*
					 * function getSelf(){ return _self; }
					 * 
					 * function getOptions(){ return $options; }
					 */

					function limitBounds(ui) {
						if (ui.position.top > 0)
							getData('image').posY = 0;
						if (ui.position.left > 0)
							getData('image').posX = 0;

						var bottom = -(getData('image').h - ui.helper.parent()
								.parent().height()), right = -(getData('image').w - ui.helper
								.parent().parent().width());
						if (ui.position.top < bottom)
							getData('image').posY = bottom;
						if (ui.position.left < right)
							getData('image').posX = right;
						calculateTranslationAndRotation();
					}

					function getExtensionSource() {
						var parts = $options.image.source.split('.');
						return parts[parts.length - 1];
					}
					;

					function calculateFactor() {
						getData('image').scaleX = ($options.width / getData('image').w);
						getData('image').scaleY = ($options.height / getData('image').h);
					}
					;

					function getCorrectSizes() {
						if ($options.image.startZoom != 0) {
							var zoomInPx_width = (($options.image.width * Math
									.abs($options.image.startZoom)) / 100);
							var zoomInPx_height = (($options.image.height * Math
									.abs($options.image.startZoom)) / 100);
							getData('image').h = zoomInPx_height;
							getData('image').w = zoomInPx_width;
							//Checking if the position was set before
							if (getData('image').posY != 0
									&& getData('image').posX != 0) {
								if (getData('image').h > $options.height)
									getData('image').posY = Math
											.abs(($options.height / 2)
													- (getData('image').h / 2));
								else
									getData('image').posY = (($options.height / 2) - (getData('image').h / 2));
								if (getData('image').w > $options.width)
									getData('image').posX = Math
											.abs(($options.width / 2)
													- (getData('image').w / 2));
								else
									getData('image').posX = (($options.width / 2) - (getData('image').w / 2));
							}
						} else {
							var scaleX = getData('image').scaleX;
							var scaleY = getData('image').scaleY;
							if (scaleY < scaleX) {
								getData('image').h = $options.height;
								getData('image').w = Math
										.round(getData('image').w * scaleY);
							} else {
								getData('image').h = Math
										.round(getData('image').h * scaleX);
								getData('image').w = $options.width;
							}
						}

						// Disable snap to container if is little
						if (getData('image').w < $options.width
								&& getData('image').h < $options.height) {
							$options.image.snapToContainer = false;
						}
						calculateTranslationAndRotation();

					}
					;

					function calculateTranslationAndRotation() {
						var rotacion = "";
						var traslacion = "";
						$(function() {
							// console.log(imageData.id);
							if ($.browser.msie) {
								if ($.support.leadingWhitespace) {
									rotacion = "rotate("
											+ getData('image').rotation
											+ "deg)";/*
														 * +
														 * (getData('image').posX +
														 * (getData('image').w /
														 * 2 )) + "," +
														 * (getData('image').posY +
														 * (getData('image').h /
														 * 2)) +
														 */
									$($image).css({
										'msTransform' : rotacion,
										'top' : getData('image').posY,
										'left' : getData('image').posX
									});

								} else {
									rotacion = getData('image').rotation;
									$($image).css({
										'rotation' : rotacion,
										'top' : getData('image').posY,
										'left' : getData('image').posX
									});
								}
							} else {
								rotacion = "rotate("
										+ getData('image').rotation
										+ ","
										+ (getData('image').posX + (getData('image').w / 2))
										+ ","
										+ (getData('image').posY + (getData('image').h / 2))
										+ ")";
								traslacion = " translate("
										+ getData('image').posX + ","
										+ getData('image').posY + ")";
								rotacion += traslacion;
								$($image).attr("transform", rotacion);
							}
						});
					}
					;

					function createRotationSlider() {

						var rotationContainerSlider = $("<div />").attr('id',
								'rotationContainer').mouseover(function() {
							$(this).css('opacity', 1);
						}).mouseout(function() {
							$(this).css('opacity', 0.6);
						});

						var rotMin = $('<div />').attr('id', 'rotationMin')
								.html("0");
						var rotMax = $('<div />').attr('id', 'rotationMax')
								.html("360");

						var $slider = $("<div />").attr('id', 'rotationSlider');
						// Aplicamos el Slider
						var orientation = 'vertical';
						var value = Math.abs(360 - $options.image.rotation);

						if ($options.expose.slidersOrientation == 'horizontal') {
							orientation = 'horizontal';
							value = $options.image.rotation;
						}

						$slider
								.slider({
									orientation : orientation,
									value : value,
									range : "max",
									min : 0,
									max : 359,
									step : (($options.rotationSteps > 360 || $options.rotationSteps < 0) ? 1
											: $options.rotationSteps),
									slide : function(event, ui) {
										getData('image').rotation = (value == 360 ? Math
												.abs(360 - ui.value)
												: Math.abs(ui.value));
										calculateTranslationAndRotation();
										if ($options.image.onRotate != null)
											$options.image.onRotate($slider,
													getData('image').rotation);
									},
									change : function(event, ui) {
										getData('image').rotation = (value == 360 ? Math
												.abs(360 - ui.value)
												: Math.abs(ui.value));
										calculateTranslationAndRotation();
										if ($options.image.onRotate != null)
											$options.image.onRotate($slider,
													getData('image').rotation);
									}
									
								});

						rotationContainerSlider.append(rotMin);
						rotationContainerSlider.append($slider);
						rotationContainerSlider.append(rotMax);

						if ($options.expose.rotationElement != '') {
							$slider
									.addClass($options.expose.slidersOrientation);
							rotationContainerSlider
									.addClass($options.expose.slidersOrientation);
							rotMin.addClass($options.expose.slidersOrientation);
							rotMax.addClass($options.expose.slidersOrientation);
							$($options.expose.rotationElement).append(
									rotationContainerSlider);
						} else {
							$slider.addClass('vertical');
							rotationContainerSlider.addClass('vertical');
							rotMin.addClass('vertical');
							rotMax.addClass('vertical');
							rotationContainerSlider.css({
								'position' : 'absolute',
								'top' : 5,
								'left' : 5,
								'opacity' : 0.6
							});
							_self.append(rotationContainerSlider);
						}
					}
					;

					
					function createZoomSlider() {

						var zoomContainerSlider = $("<div />").attr('id',
								'zoomContainer').mouseover(function() {
							$(this).css('opacity', 1);
						}).mouseout(function() {
							$(this).css('opacity', 0.6);
						});

						var zoomMin = $('<div />').attr('id', 'zoomMin').html(
								"<b>-</b>");
						var zoomMax = $('<div />').attr('id', 'zoomMax').html(
								"<b>+</b>");

						var $slider = $("<div />").attr('id', 'zoomSlider');

						// Aplicamos el Slider
						/*
						console.log($options.image.startZoom);
						console.log($options.image.maxZoom);
						console.log(getPercentOfZoom(getData('image')));
						*/
						$slider
								.slider({
									orientation : ($options.expose.zoomElement != '' ? $options.expose.slidersOrientation
											: 'vertical'),
									value : ($options.image.startZoom != 0 ? $options.image.startZoom
											: getPercentOfZoom(getData('image'))),
									min : ($options.image.useStartZoomAsMinZoom ? $options.image.startZoom
											: $options.image.minZoom),
									max : $options.image.maxZoom,
									step : (($options.zoomSteps > $options.image.maxZoom || $options.zoomSteps < 0) ? 1
											: $options.zoomSteps),
									slide : function(event, ui) {
										var value = ($options.expose.slidersOrientation == 'vertical' ? ($options.image.maxZoom - ui.value)
												: ui.value);
										var zoomInPx_width = ($options.image.width
												* Math.abs(value) / 100);
										var zoomInPx_height = ($options.image.height
												* Math.abs(value) / 100);
										if ($.browser.msie) {
											$($image)
													.css(
															{
																'width' : zoomInPx_width
																		+ "px",
																'height' : zoomInPx_height
																		+ "px"
															});

										} else {
											$($image).attr('width',
													zoomInPx_width + "px");
											$($image).attr('height',
													zoomInPx_height + "px");
										}

										var difX = (getData('image').w / 2)
												- (zoomInPx_width / 2);
										var difY = (getData('image').h / 2)
												- (zoomInPx_height / 2);

										var newX = (difX > 0 ? getData('image').posX
												+ Math.abs(difX)
												: getData('image').posX
														- Math.abs(difX));
										var newY = (difY > 0 ? getData('image').posY
												+ Math.abs(difY)
												: getData('image').posY
														- Math.abs(difY));
										getData('image').posX = newX;
										getData('image').posY = newY;
										getData('image').w = zoomInPx_width;
										getData('image').h = zoomInPx_height;
										calculateFactor();
										calculateTranslationAndRotation();
										if ($options.image.onZoom != null) {
											$options.image.onZoom($image,
													getData('image'));
										}
									}
								});

						if ($options.slidersOrientation == 'vertical') {
							zoomContainerSlider.append(zoomMax);
							zoomContainerSlider.append($slider);
							zoomContainerSlider.append(zoomMin);
						} else {
							zoomContainerSlider.append(zoomMin);
							zoomContainerSlider.append($slider);
							zoomContainerSlider.append(zoomMax);
						}

						if ($options.expose.zoomElement != '') {
							zoomMin
									.addClass($options.expose.slidersOrientation);
							zoomMax
									.addClass($options.expose.slidersOrientation);
							$slider
									.addClass($options.expose.slidersOrientation);
							zoomContainerSlider
									.addClass($options.expose.slidersOrientation);
							$($options.expose.zoomElement).append(
									zoomContainerSlider);
						} else {
							zoomMin.addClass('vertical');
							zoomMax.addClass('vertical');
							$slider.addClass('vertical');
							zoomContainerSlider.addClass('vertical');
							zoomContainerSlider.css({
								'position' : 'absolute',
								'top' : 5,
								'right' : 5,
								'opacity' : 0.6
							});
							_self.append(zoomContainerSlider);
						}
					}
					;

					function getPercentOfZoom() {
						var percent = 0;
						if (getData('image').w > getData('image').h) {
							percent = $options.image.maxZoom
									- ((getData('image').w * 100) / $options.image.width);
						} else {
							percent = $options.image.maxZoom
									- ((getData('image').h * 100) / $options.image.height);
						}
						
						if ($options.expose.slidersOrientation == 'horizontal') {
							percent = $options.image.maxZoom - percent;
						}

						return percent;
					}
					;

					function createSelector() {
						if ($options.selector.centered) {
							getData('selector').y = ($options.height / 2)
									- (getData('selector').h / 2);
							getData('selector').x = ($options.width / 2)
									- (getData('selector').w / 2);
						}

						$selector = $('<div />')
								.attr('id', _self[0].id + '_selector')
								.css(
										{
											'width' : getData('selector').w,
											'height' : getData('selector').h,
											'top' : getData('selector').y
													+ 'px',
											'left' : getData('selector').x
													+ 'px',
											'border' : '1px dashed '
													+ $options.selector.borderColor,
											'position' : 'absolute',
											'cursor' : 'move'
										})
								.mouseover(
										function() {
											$(this)
													.css(
															{
																'border' : '1px dashed '
																		+ $options.selector.borderColorHover
															})
										})
								.mouseout(
										function() {
											$(this)
													.css(
															{
																'border' : '1px dashed '
																		+ $options.selector.borderColor
															})
										});
						// Aplicamos el drageo al selector
						$selector
								.draggable({
									containment : 'parent',
									iframeFix : true,
									refreshPositions : true,
									drag : function(event, ui) {
										// Actualizamos las posiciones de la
										// mascara
										getData('selector').x = ui.position.left;
										getData('selector').y = ui.position.top;
										makeOverlayPositions(ui);
										showInfo();
										if ($options.selector.onSelectorDrag != null)
											$options.selector.onSelectorDrag(
													$selector,
													getData('selector'));
									},
									stop : function(event, ui) {
										// Ocultar la mascara
										if ($options.selector.hideOverlayOnDragAndResize)
											hideOverlay();
										if ($options.selector.onSelectorDragStop != null)
											$options.selector
													.onSelectorDragStop(
															$selector,
															getData('selector'));
									}
								});
						$selector
								.resizable({
									aspectRatio : $options.selector.aspectRatio,
									maxHeight : $options.selector.maxHeight,
									maxWidth : $options.selector.maxWidth,
									minHeight : $options.selector.minHeight,
									minWidth : $options.selector.minWidth,
									containment : 'parent',
									resize : function(event, ui) {
										// Actualizamos las posiciones de la
										// mascara
										getData('selector').w = $selector
												.width();
										getData('selector').h = $selector
												.height();
										makeOverlayPositions(ui);
										showInfo();
										if ($options.selector.onSelectorResize != null)
											$options.selector.onSelectorResize(
													$selector,
													getData('selector'));
									},
									stop : function(event, ui) {
										if ($options.selector.hideOverlayOnDragAndResize)
											hideOverlay();
										if ($options.selector.onSelectorResizeStop != null)
											$options.selector
													.onSelectorResizeStop(
															$selector,
															getData('selector'));
									}
								});

						showInfo($selector);
						// Agregamos el selector al objeto contenedor
						_self.append($selector);
					}
					;

					function showInfo() {

						var _infoView = null;
						var alreadyAdded = false;
						if ($selector.find("#infoSelector").length > 0) {
							_infoView = $selector.find("#infoSelector");
						} else {
							_infoView = $('<div />')
									.attr('id', 'infoSelector')
									.css(
											{
												'position' : 'absolute',
												'top' : 0,
												'left' : 0,
												'background' : $options.selector.bgInfoLayer,
												'opacity' : 0.6,
												'font-size' : $options.selector.infoFontSize
														+ 'px',
												'font-family' : 'Arial',
												'color' : $options.selector.infoFontColor,
												'width' : '100%'
											});
						}
						if ($options.selector.showPositionsOnDrag) {
							_infoView.html("X:" + Math.round(getData('selector').x)
									+ "px - Y:" + Math.round(getData('selector').y) + "px");
							alreadyAdded = true;
						}
						if ($options.selector.showDimetionsOnDrag) {
							if (alreadyAdded) {
								_infoView.html(_infoView.html() + " | W:"
										+ getData('selector').w + "px - H:"
										+ getData('selector').h + "px");
							} else {
								_infoView.html("W:" + getData('selector').w
										+ "px - H:" + getData('selector').h
										+ "px");
							}
						}
						$selector.append(_infoView);
					}
					;

					function createOverlay() {
						var arr = [ 't', 'b', 'l', 'r' ];
						$.each(arr, function() {
							var divO = $("<div />").attr("id", this).css({
								'overflow' : 'hidden',
								'background' : $options.overlayColor,
								'opacity' : 0.6,
								'position' : 'absolute',
								'z-index' : 2,
								'visibility' : 'visible'
							});
							_self.append(divO);
						});
					}
					;

					function makeOverlayPositions(ui) {

						_self.find("#t").css({
							"display" : "block",
							"width" : $options.width,
							'height' : ui.position.top,
							'left' : 0,
							'top' : 0
						});
						_self.find("#b").css(
								{
									"display" : "block",
									"width" : $options.width,
									'height' : $options.height,
									'top' : (ui.position.top + $selector
											.height())
											+ "px",
									'left' : 0
								});
						_self.find("#l").css({
							"display" : "block",
							'left' : 0,
							'top' : ui.position.top,
							'width' : ui.position.left,
							'height' : $selector.height()
						});
						_self.find("#r").css(
								{
									"display" : "block",
									'top' : ui.position.top,
									'left' : (ui.position.left + $selector
											.width())
											+ "px",
									'width' : $options.width,
									'height' : $selector.height() + "px"
								});
					}
					;

					function hideOverlay() {
						_self.find("#t").hide();
						_self.find("#b").hide();
						_self.find("#l").hide();
						_self.find("#r").hide();
					}

					function setData(key, data) {
						_self.data(key, data);
					}
					;

					function getData(key) {
						return _self.data(key);
					}
					;

					function createMovementControls() {
						var table = $('<table>\
                <tr>\
                <td></td>\
                <td></td>\
                <td></td>\
                </tr>\
                <tr>\
                <td></td>\
                <td></td>\
                <td></td>\
                </tr>\
                <tr>\
                <td></td>\
                <td></td>\
                <td></td>\
                </tr>\
                </table>');
						var btns = [];
						btns.push($('<div />').addClass('mvn_no mvn'));
						btns.push($('<div />').addClass('mvn_n mvn'));
						btns.push($('<div />').addClass('mvn_ne mvn'));
						btns.push($('<div />').addClass('mvn_o mvn'));
						btns.push($('<div />').addClass('mvn_c'));
						btns.push($('<div />').addClass('mvn_e mvn'));
						btns.push($('<div />').addClass('mvn_so mvn'));
						btns.push($('<div />').addClass('mvn_s mvn'));
						btns.push($('<div />').addClass('mvn_se mvn'));
						for ( var i = 0; i < btns.length; i++) {
							btns[i].mousedown(function() {
								moveImage(this);
							}).mouseup(function() {
								clearTimeout(tMovement);
							});
							table.find('td:eq(' + i + ')').append(btns[i]);
							$($options.expose.elementMovement).append(table);

						}
					}
					;

					function moveImage(obj) {

						if ($(obj).hasClass('mvn_no')) {
							getData('image').posX = (getData('image').posX - $options.expose.movementSteps);
							getData('image').posY = (getData('image').posY - $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_n')) {
							getData('image').posY = (getData('image').posY - $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_ne')) {
							getData('image').posX = (getData('image').posX + $options.expose.movementSteps);
							getData('image').posY = (getData('image').posY - $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_o')) {
							getData('image').posX = (getData('image').posX - $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_c')) {
							getData('image').posX = ($options.width / 2)
									- (getData('image').w / 2);
							getData('image').posY = ($options.height / 2)
									- (getData('image').h / 2);
						} else if ($(obj).hasClass('mvn_e')) {
							getData('image').posX = (getData('image').posX + $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_so')) {
							getData('image').posX = (getData('image').posX - $options.expose.movementSteps);
							getData('image').posY = (getData('image').posY + $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_s')) {
							getData('image').posY = (getData('image').posY + $options.expose.movementSteps);
						} else if ($(obj).hasClass('mvn_se')) {
							getData('image').posX = (getData('image').posX + $options.expose.movementSteps);
							getData('image').posY = (getData('image').posY + $options.expose.movementSteps);
						}
						if ($options.image.snapToContainer) {
							if (getData('image').posY > 0) {
								getData('image').posY = 0;
							}
							if (getData('image').posX > 0) {
								getData('image').posX = 0;
							}

							var bottom = -(getData('image').h - _self.height());
							var right = -(getData('image').w - _self.width());
							if (getData('image').posY < bottom) {
								getData('image').posY = bottom;
							}
							if (getData('image').posX < right) {
								getData('image').posX = right;
							}
						}
						calculateTranslationAndRotation();
						tMovement = setTimeout(function() {
							moveImage(obj);
						}, 100);
					}

					$.fn.cropzoom.getParameters = function(_self, custom) {
						var image = _self.data('image');
						var selector = _self.data('selector');
						var fixed_data = {
							'viewPortW' : _self.width(),
							'viewPortH' : _self.height(),
							'imageX' : image.posX,
							'imageY' : image.posY,
							'imageRotate' : image.rotation,
							'imageW' : image.w,
							'imageH' : image.h,
							'imageSource' : image.source,
							'selectorX' : selector.x,
							'selectorY' : selector.y,
							'selectorW' : selector.w,
							'selectorH' : selector.h
						};
						return $.extend(fixed_data, custom);
					};

					$.fn.cropzoom.getSelf = function() {
						return _self;
					}
					$.fn.cropzoom.getOptions = function() {
						return $options;
					}

					// Maintein Chaining
					return this;
				});

	};

	/* Code taken from jquery.svgdom.js */
	/* Support adding class names to SVG nodes. */
	/* Andrew: This plays hell with a lot of stuff and I don't think it's needed ?! */
	/*
	var origAddClass = $.fn.addClass;

	$.fn.addClass = function(classNames) {
		classNames = classNames || '';
		return this.each(function() {
			if (isSVGElem(this)) {
				var node = this;
				$.each(classNames.split(/\s+/), function(i, className) {
					var classes = (node.className ? node.className.baseVal
							: node.getAttribute('class'));
					if ($.inArray(className, classes.split(/\s+/)) == -1) {
						classes += (classes ? ' ' : '') + className;
						(node.className ? node.className.baseVal = classes
								: node.setAttribute('class', classes));
					}
				});
			} else {
				origAddClass.apply($(this), [ classNames ]);
			}
		});
	};
	*/
	/* Support removing class names from SVG nodes. */
	/*
	var origRemoveClass = $.fn.removeClass;

	$.fn.removeClass = function(classNames) {
		classNames = classNames || '';
		return this.each(function() {
			if (isSVGElem(this)) {
				var node = this;
				$.each(classNames.split(/\s+/), function(i, className) {
					var classes = (node.className ? node.className.baseVal
							: node.getAttribute('class'));
					classes = $.grep(classes.split(/\s+/), function(n, i) {
						return n != className;
					}).join(' ');
					(node.className ? node.className.baseVal = classes : node
							.setAttribute('class', classes));
				});
			} else {
				origRemoveClass.apply($(this), [ classNames ]);
			}
		});
	};
	*/
	/* Support toggling class names on SVG nodes. */
	/*
	var origToggleClass = $.fn.toggleClass;

	$.fn.toggleClass = function(className, state) {
		return this.each(function() {
			if (isSVGElem(this)) {
				if (typeof state !== 'boolean') {
					state = !$(this).hasClass(className);
				}
				$(this)[(state ? 'add' : 'remove') + 'Class'](className);
			} else {
				origToggleClass.apply($(this), [ className, state ]);
			}
		});
	};
	*/
	/* Support checking class names on SVG nodes. */
	/*
	var origHasClass = $.fn.hasClass;

	$.fn.hasClass = function(className) {
		className = className || '';
		var found = false;
		this.each(function() {
			if (isSVGElem(this)) {
				var classes = (this.className ? this.className.baseVal : this
						.getAttribute('class')).split(/\s+/);
				found = ($.inArray(className, classes) > -1);
			} else {
				found = (origHasClass.apply($(this), [ className ]));
			}
			return !found;
		});
		return found;
	};
	*/
	/* Support attributes on SVG nodes. */
	/*
	var origAttr = $.fn.attr;
	
	$.fn.attr = function(name, value, type) {
		if (typeof name === 'string' && value === undefined) {
			var val = origAttr.apply(this, [ name, value, type ]);
			return (val && val.baseVal ? val.baseVal.valueAsString : val);
		}
		var options = name;
		if (typeof name === 'string') {
			options = {};
			options[name] = value;
		}
		return this.each(function() {
			if (isSVGElem(this)) {
				for ( var n in options) {
					this.setAttribute(n,
							(typeof options[n] == 'function' ? options[n]()
									: options[n]));
				}
			} else {
				origAttr.apply($(this), [ name, value, type ]);
			}
		});
	};
		*/
		
	/* Support removing attributes on SVG nodes. */
	/*
	var origRemoveAttr = $.fn.removeAttr;

	$.fn.removeAttr = function(name) {
		return this
				.each(function() {
					if (isSVGElem(this)) {
						(this[name] && this[name].baseVal ? this[name].baseVal.value = ''
								: this.setAttribute(name, ''));
					} else {
						origRemoveAttr.apply($(this), [ name ]);
					}
				});
	};

	;
	*/
	/*
	function isSVGElem(node) {
		return (node.nodeType == 1 && node.namespaceURI == 'http://www.w3.org/2000/svg');
	}
	*/
	
	// Css Hooks
	/*
	 * jQuery.cssHooks["MsTransform"] = { set: function( elem, value ) {
	 * elem.style.msTransform = value; } };
	 */

	$.fn.extend({
		// Function to set the selector position and sizes
		setSelector : function(x, y, w, h, animate) {
			
			var _self = $(this);
			if (animate != undefined && animate == true) {
				_self.find('#' + _self[0].id + '_selector').animate({
					'top' : y,
					'left' : x,
					'width' : w,
					'height' : h
				}, 'slow');
			} else {
				_self.find('#' + _self[0].id + '_selector').css({
					'top' : y,
					'left' : x,
					'width' : w,
					'height' : h
				});
			}
			
			_self.data('selector', {
				x : x,
				y : y,
				w : w,
				h : h
			});
			
		},
		// Restore the Plugin
		restore : function() {
			var _self = $(this);
			var $options = $(this).cropzoom.getOptions();
			$(_self).empty();
			$(_self).data('image', {});
			$(_self).data('selector', {});
			if ($options.expose.zoomElement != "") {
				$($options.expose.zoomElement).empty();
			}
			if ($options.expose.rotationElement != "") {
				$($options.expose.rotationElement).empty();
			}
			if ($options.expose.elementMovement != "") {
				$($options.expose.elementMovement).empty();
			}
			_self.cropzoom($options);

		},
		// Send the Data to the Server
		send : function(url, type, custom, onSuccess) {
			var _self = $(this);
			var response = "";
			$.ajax({
				url : url,
				type : type,
				data : (_self.cropzoom.getParameters(_self, custom)),
				success : function(r) {
					_self.data('imageResult', r);
					if (onSuccess !== undefined && onSuccess != null)
						onSuccess(r);
				}
			});
		}
	});

})(jQuery);
