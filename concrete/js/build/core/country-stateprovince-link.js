/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_ACTIVE_LOCALE, CCM_DISPATCHER_FILENAME */

;(function(global, $) {
    'use strict';

    if (global.ccmCountryStateprovinceLink) {
        return;
    }
    
    var USE_MUTATIONOBSERVER = window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe ? true : false;
    
    function loadStateprovincesForCountry(countryCode, callback) {
        if (typeof countryCode !== 'string' || $.trim(countryCode) === '') {
            callback(countryCode, []);
            return;
        }
        if (loadStateprovincesForCountry.cache.hasOwnProperty(countryCode)) {
            callback(countryCode, loadStateprovincesForCountry.cache[countryCode]);
            return;
        }
        callback(countryCode, []);
        $.ajax({
            cache: true, // Needed because we may change the current locale
            data: {
                countryCode: countryCode,
                activeLocale: CCM_ACTIVE_LOCALE
            },
            dataType: 'json',
            method: 'GET',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/country-stateprovince-link/get_stateprovinces'
        })
        .fail(function(xhr, status, error) {
            if (window.console && window.console.error) {
                window.console.error(xhr.responseJSON || error);
            }
            loadStateprovincesForCountry.cache[countryCode] = [];
        })
        .success(function(data) {
            loadStateprovincesForCountry.cache[countryCode] = data instanceof Array ? data : [];
        })
        .always(function() {
            callback(countryCode, loadStateprovincesForCountry.cache[countryCode]);
        });
    }
    loadStateprovincesForCountry.cache = {};
    
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
    
    function Link($country, $stateprovince) {
        var me = this;
        me.$country = $country;
        me.$stateprovince = $stateprovince;
        me.replacer = new TextReplacer(me.$stateprovince);
        me.$stateprovinceSelect = me.replacer.$select;
        me.$country
            .on('change', function() {
                me.countryChanged();
            })
            .trigger('change')
        ;
        me.$stateprovinceSelect.on('change', function() {
            me.$stateprovince
                .val(me.$stateprovinceSelect.val())
                .trigger('change')
            ;
        });
    }
    Link.prototype = {
        countryChanged: function() {
            var me = this;
            loadStateprovincesForCountry(me.$country.val(), function(countryCode, stateprovinceList) {
                if (me.$country.val() !== countryCode) {
                    return;
                }
                me.$stateprovinceSelect.empty();
                var n = stateprovinceList.length;
                if (n === 0) {
                    me.replacer.setEnabled(false);
                } else {
                    var selectedStateprovince = $.trim(me.$stateprovince.val());
                    me.$stateprovinceSelect.append($('<option value="" selected="selected" />').text(''));
                    for (var i = 0, $o; i < n; i++) {
                        $o = $('<option />')
                            .val(stateprovinceList[i][0])
                            .text(stateprovinceList[i][1])
                        ;
                        if (stateprovinceList[i][0] === selectedStateprovince) {
                            $o.attr('selected', 'selected');
                        }
                        me.$stateprovinceSelect.append($o);
                    }
                    me.replacer.setEnabled(true);
                }
            });
        }
    };
    Link.withCountryField = function ($country) {
        var $parent = $country.closest('form');
        if ($parent.length === 0) {
            $parent = $(document.body);
        }
        var result = [];
        $parent.find('input[data-countryfield="' + $country.attr('id') + '"]').each(function() {
            result.push(new Link($country, $(this)));
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
    
    global.ccmCountryStateprovinceLink = Link;

})(this, jQuery);
