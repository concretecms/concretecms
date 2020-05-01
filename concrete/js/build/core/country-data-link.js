/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_ACTIVE_LOCALE, CCM_DISPATCHER_FILENAME */

;(function(global, $) {
    'use strict';

    if (global.ccmCountryDataLink) {
        return;
    }

    var USE_MUTATIONOBSERVER = window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe ? true : false;

    function loadDataForCountry(countryCode, callback) {
        if (typeof countryCode !== 'string' || $.trim(countryCode) === '') {
            callback(countryCode, {
                statesProvices: {},
                addressUsedFields: []
            });
            return;
        }
        if (loadDataForCountry.cache.hasOwnProperty(countryCode)) {
            callback(countryCode, loadDataForCountry.cache[countryCode]);
            return;
        }
        callback(countryCode, {
            statesProvices: {},
            addressUsedFields: []
        });
        $.ajax({
            cache: true, // Needed because we may change the current locale
            data: {
                countryCode: countryCode,
                activeLocale: CCM_ACTIVE_LOCALE
            },
            dataType: 'json',
            method: 'GET',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/country-data-link/all'
        })
        .fail(function(xhr, status, error) {
            if (window.console && window.console.error) {
                window.console.error(xhr.responseJSON || error);
            }
            loadDataForCountry.cache[countryCode] = {
                statesProvices: {},
                addressUsedFields: []
            };
        })
        .success(function(data) {
            var statesProvinces = {};
            if (data.statesProvices instanceof Object) {
                statesProvinces = data.statesProvices;
            }
            var addressUsedFields = [];
            if (data.addressUsedFields instanceof Array) {
                addressUsedFields = data.addressUsedFields;
            }

            loadDataForCountry.cache[countryCode] = {
                statesProvices: statesProvinces,
                addressUsedFields: addressUsedFields
            };
        })
        .always(function() {
            callback(countryCode, loadDataForCountry.cache[countryCode]);
        });
    }
    loadDataForCountry.cache = {};

    function TextReplacer($text) {
        var me = this;
        me.enabled = false;
        me.$text = $text;
        me.$select = $('<select />');
        if (USE_MUTATIONOBSERVER) {
            me.mutationObserver = new window.MutationObserver(function(records) {
                me.updateSelectAttributes();
                me.$text.hide();
                me.$select.show();
            });
        } else {
            me.mutationObserver = null;
        }
        me.originalFocus = me.$text[0].focus;
        me.$text[0].focus = function() {
            if (me.enabled) {
                me.$select.focus();
            } else {
                me.originalFocus.apply(me.$text[0]);
            }
        };
    }
    TextReplacer.prototype = {
        updateSelectAttributes: function() {
            var me = this;
            $.each(['class', 'style', 'required'], function (index, attributeName) {
                var attributeValue = me.$text.attr(attributeName);
                if (typeof attributeValue === 'string') {
                    me.$select.attr(attributeName, attributeValue);
                }
            });
        },
        setEnabled: function(enable) {
            var me = this;
            enable = !!enable;
            if (enable === me.enabled) {
                return;
            }
            if (enable) {
                me.updateSelectAttributes();
                me.$text.before(me.$select);
                me.$text.hide();
                me.enabled = true;
                if (me.mutationObserver !== null) {
                    setTimeout(
                        function() {
                            if (me.enabled !== true) {
                                return;
                            }
                            me.mutationObserver.disconnect();
                            me.mutationObserver.observe(
                                me.$text[0],
                                {
                                    attributes: true
                                }
                            );
                        },
                        0
                    );
                }
            } else {
                if (me.mutationObserver !== null) {
                    me.mutationObserver.disconnect();
                }
                me.enabled = false;
                me.$select.detach();
                me.$text.show();
            }
        }
    };

    function Link($country, $stateprovince, config) {
        var me = this;
        me.$country = $country;
        me.$stateprovinceWrapper = $stateprovince;
        if ($stateprovince.is('input')) {
            me.$stateprovince = $stateprovince;
        } else {
            me.$stateprovince = $stateprovince.find('input:first');
        }
        me.config = config;
        me.replacer = new TextReplacer(me.$stateprovince);
        me.$stateprovinceSelect = me.replacer.$select;
        me.$country
            .on('change', function() {
                me.countryChanged();
            })
        ;
        me.$stateprovinceSelect.on('change', function() {
            me.$stateprovince
                .val(me.$stateprovinceSelect.val())
                .trigger('change')
            ;
        });
        me.countryChanged(true);
    }
    Link.prototype = {
        countryChanged: function(initializing) {
            var me = this;
            loadDataForCountry(me.$country.val(), function(countryCode, countryData) {
                if (me.$country.val() !== countryCode) {
                    return;
                }
                me.$stateprovinceSelect.empty();

                if (!initializing && me.config.clearStateProvinceOnChange) {
                    me.$stateprovince.val('');
                }

                if (me.config.hideUnusedStateProvinceField) {
                    if (countryData.addressUsedFields.indexOf('state_province') > -1) {
                        me.$stateprovinceWrapper.show();
                    } else {
                        me.$stateprovinceWrapper.hide();
                    }
                }

                var n = Object.keys(countryData.statesProvices).length;
                if (n === 0) {
                    me.replacer.setEnabled(false);
                } else {
                    var selectedStateprovince = $.trim(me.$stateprovince.val());
                    me.$stateprovinceSelect.append($('<option value="" selected="selected" />').text(''));
                    $.each(countryData.statesProvices, function(spCode, name) {
                        var $o = $('<option />')
                            .val(spCode)
                            .text(name)
                        ;
                        if (spCode === selectedStateprovince) {
                            $o.attr('selected', 'selected');
                        }
                        me.$stateprovinceSelect.append($o);
                    });
                    me.replacer.setEnabled(true);
                }

                me.$country.trigger('country-data', [countryData]);
            });
        }
    };
    Link.withCountryField = function ($country, config) {
        config = $.extend({
            hideUnusedStateProvinceField: false,
            clearStateProvinceOnChange: false
        }, config);

        var $parent = $country.closest('form');
        if ($parent.length === 0) {
            $parent = $(document.body);
        }
        var result = [];
        $parent.find('[data-countryfield="' + $country.attr('id') + '"]').each(function() {
            result.push(new Link($country, $(this), config));
        });
        switch (result.length) {
            case 0:
                return null;
            case 1:
                return result[0];
            default:
                return result;
        }
    };

    global.ccmCountryDataLink = Link;

})(this, jQuery);
