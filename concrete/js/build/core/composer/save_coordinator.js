(function(global, $, _) {
    'use strict';

    function SaveCoordinator($form, options) {
        this.init.apply(this, _.toArray(arguments));
    }

    SaveCoordinator.prototype = {

        init: function($form, saver, options) {
            var defaultOptions = {
                // The function used to determine whether
                saveValidator: function(saver) {
                    return saver.canSave();
                },

                debug: false,

                // The time in seconds until idle triggers a save
                idleTimeout: 20,

                // Throttle is when you accept a request, then wait a while until you accept the next request
                // This prevents a save from firing constantly
                saveThrottleEnabled: true,

                // The amount of time in seconds after a successful save before a new save can begin
                saveThrottleTimeout: 10,

                // Debounce is when you wait a second before starting your task to prevent many calls from causing many tasks
                // This helps with things like typing continuously in a textarea.
                saveDebounceEnabled: true,

                // The amount of dead time between a successful request and the last attempt to save
                saveDebounceTimeout: 2,

                // The maximum amount of time saving can be debounced before a save happens. Set to 0 for infinite
                saveDebounceMaximum: 11
            };

            this.lastSerialized = null;
            this.saveThrottleTimer = null;
            this.saveDebounceTimer = null;
            this.saveDebounceBegan = null;
            this.idleTimer = null;
            this.enabled = false;
            this.queuedSave = false;
            this.options = defaultOptions;
            this.options = _(defaultOptions).extend(options);
            this.options.form = $form;
            this.options.saver = saver;
            this.saving = false;
            this.status = {
                idle: 0,
                saving: 1,
                busy: 2,
                throttled: 3,
                debounced: 4,
                saveFailed: 5,
                disabled: 6
            };
            this.cachedForm(this.getFormSerialized());
        },

        /**
         * Find out if this coordinator is enabled
         * @returns {boolean}
         */
        isEnabled: function() {
            return this.enabled;
        },

        /**
         * Enable saving using this coordinator
         */
        enable: function() {
            this.resetIdleTimer();
            this.enabled = true;
        },

        /**
         * Disable saving using this coodinator
         */
        disable: function() {
            this.enabled = false;
            this.disableIdleTimer();
        },

        /**
         * Stop the idle timer
         */
        disableIdleTimer: function() {
            global.clearTimeout(this.idleTimer);
            this.idleTimer = null;
        },

        /**
         * Request a save
         * @param {boolean} queue
         * @returns {integer} See SaveCoordinator.status
         */
        requestSave: function(queue) {
            this.resetIdleTimer();

            if (!this.enabled) {
                return this.status.disabled;
            }

            if (typeof queue !== 'undefined' && queue) {
                return this.requestQueuedSave();
            }

            if (this.saving) {
                this.queuedSave = true;
                return this.status.busy;
            }

            if (this.options.saveValidator(this)) {

                if (this.options.saveThrottleEnabled) {
                    if (this.throttleSave()) {
                        this.resetThrottle();
                        return this.status.throttled;
                    }
                }

                if (this.options.saveDebounceEnabled) {
                    return this.debounceSave();
                } else {
                    this.debug('Handling Save Synchronous');
                    return this.handleSave();
                }
            } else {
                this.debug('Save Not Needed');
                return this.status.saveFailed;
            }
        },

        /**
         * Request a save that will happen now or after throttle expires if one is set
         * @returns {integer} See SaveCoordinator.status
         */
        requestQueuedSave: function() {
            var result = this.requestSave();

            if (result === this.status.throttled) {
                this.debug('Queuing Save');
                this.queuedSave = true;
            }

            return result;
        },

        /**
         * Internal function, should be avoided.
         * Handles calling the configured save routine
         * @returns {number} Returns disabled, busy, or saving
         */
        handleSave: function() {
            if (!this.enabled) {
                return this.status.disabled;
            }

            if (this.saving) {
                return this.status.busy;
            }

            this.saving = true;
            var my = this,
                formData = this.cachedForm(this.getFormSerialized()),
                saveHandler = function() {
                    my.saving = false;
                    my.resetThrottle();
                    my.resetIdleTimer();
                };

            if (!this.options.saver(this, formData, saveHandler)) {
                return this.status.saveFailed;
            }

            return this.status.saving;
        },

        /**
         * Reset the idle timer back to the configured length, this is also used to start the idle timer
         */
        resetIdleTimer: function() {
            var me = this;
            if (this.idleTimer) {
                global.clearTimeout(this.idleTimer);
            }

            this.idleTimer = setTimeout(function() {
                me.requestSave();
                me.resetIdleTimer();
            }, this.options.idleTimeout * 1000)
        },

        /**
         * Default save checker, it sees if the cached version of the form is the same as the version we have now
         * @returns {boolean}
         */
        canSave: function() {
            return !this.cachedFormEquals(this.getFormSerialized());
        },

        /**
         * A function to manage debouncing save requests. This is an internal private function
         * @returns {number} See SaveCoordinator.status
         */
        debounceSave: function() {
            if (!this.enabled) {
                return this.status.disabled;
            }

            if (this.saveDebounceTimer) {
                global.clearTimeout(this.saveDebounceTimer);
            }

            if (!this.saveDebounceBegan) {
                this.saveDebounceBegan = _.now();
            }

            var timePassed = _.now() - this.saveDebounceBegan,
                timeLeft = this.options.saveDebounceMaximum * 1000 - timePassed,
                timeout = Math.max(0, Math.min(this.options.saveDebounceTimeout * 1000, timeLeft)),
                me = this;

            if (!this.options.saveDebounceMaximum) {
                timeout = this.options.saveDebounceTimeout;
            }

            this.saveDebounceTimer = global.setTimeout(function() {
                me.debug('Debouncing Expired, Handling Save');
                me.saveDebounceBegan = null;
                me.handleSave();
            }, timeout);

            this.debug('Debouncing Save for ' + timeout + 'ms');

            return this.status.debounced;
        },

        /**
         * Reset the save throttle to the default length
         */
        resetThrottle: function() {
            this.throttleSave(this.options.saveThrottleTimeout * 1000);
        },

        /**
         * Throttle save and check the status of the save throttle
         * @param {integer} amount If nothing is passed, the status is returned and save isn't throttled
         * @returns {boolean}
         */
        throttleSave: function(amount) {
            var me = this,
                throttled = this.saveThrottleTimer != null;
            if (this.saveThrottleTimer == null && typeof amount != 'undefined') {
                this.saveThrottleTimer = global.setTimeout(function () {
                    me.debug('Throttle Expired');
                    me.saveThrottleTimer = null;
                    if (me.queuedSave) {
                        me.debug('Handling Queued Save');
                        me.handleSave();
                        me.queuedSave = false;
                    }
                }, amount);
                this.debug('Throttling Save');
            }

            return throttled;
        },

        /**
         * Get the form we're coordinating saving for
         * @returns {jQuery}
         */
        getForm: function() {
            return this.options.form;
        },

        /**
         * Get the serialized value of this form
         * @returns {array}
         */
        getFormSerialized: function() {
            return this.getForm().serializeArray();
        },

        /**
         * Get or set the last serialized form value. This is used to coordinate saving.
         * @param {null|array} serialized The serialized form data
         * @returns {null|array}
         */
        cachedForm: function(serialized) {
            if (typeof serialized != 'undefined') {
                this.lastSerialized = serialized
            }

            return this.lastSerialized;
        },

        /**
         * Check if the cached form is equal to the passed in value
         * @param {array} value
         * @returns {boolean}
         */
        cachedFormEquals: function(value) {
            return _(this.cachedForm()).isEqual(value);
        },

        /**
         * Output a message to console if debug is enabled
         * @param message
         */
        debug: function(message) {
            if (this.options.debug) {
                global.console.log("SaverCoordinator: " + message);
            }
        }
    };

    if (!global.Concrete) {
        global.Concrete = {};
    }
    if (!global.Concrete.composer) {
        global.Concrete.composer = {};
    }

    global.Concrete.composer.SaveCoodinator = SaveCoordinator;
    $.fn.saveCoordinator = function(saver, options) {
        return this.each(function() {
            var me = $(this),
                coordinator = new SaveCoordinator(me, saver, options);
            me.data('SaveCoordinator', coordinator);
        });
    };

}(this, jQuery, _));
