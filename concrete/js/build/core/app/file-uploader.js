/* jshint unused:vars, undef:true, jquery:true, browser:true */
/* global ConcreteAjaxRequest, ConcreteAlert, ConcreteEvent, NProgress */

/*
 * Usage:
 * var options = {
 *     // Defines where to display the file previews
 *     previewsContainer: <false|HTMLElement|string>,
 *     // Defines what can be clicked to open the system file secttor
 *     clickable: <false|HTMLElement|string>
 *     // Can be used to limit the maximum number of files that will be handled by this Dropzone
 *     maxFiles: <Number|null>
 *     // Called when the upload starts
 *     uploadStarted: <Function|null>; parameters: file, xhr, formData
 *     // Called when one or more files have been uploaded
 *     uploadCompleted: <Function|null>; parameters: files
 *     // Called when all the queued uploads completed
 *     uploadQueueCompleted: <Function|null>; parameters: none
 *     // Called when all the upload completed
 *     uploadFailed: <Function|null>; parameters: message
 *     // When replacing a file, its ID
 *     replacingFileID: <Number|Function|null>
 *     // The page associated to the files being uploaded
 *     originalPageID: <Number|Function|null>
 *     // The ID of the tree node of the folder where the files should be uploaded to
 *     folderID: <Number|Function|null>
 * };
 * window.ccm_fileUploader.start(options);
 * window.ccm_fileUploader.stop(options);
 */
;(function(global, $) {
'use strict';

if (typeof global.ccm_fileUploader !== 'undefined') {
    return;
}

/**
 * Is the document already loaded?
 */
var documentLoaded = false;

/**
 * The Dropzone instance (NULL if the file uploader is not started).
 */
var dropzone = null;

/**
 * The options stack (empty if the file uploader should be stopped).
 */
var optionsStack = [];

/**
 * The list of uploaded files to be sent to the FileManagerAddFilesComplete event when the upload queue finishes.
 */
var uploadedFiles = [];

/**
 * Get the default options for dropzone (also defines the only keys of the user-defined options that are applicable to Dropzone)
 */
function getDefaultOptions() {
    return {
        previewsContainer: false,
        maxFiles: null,
        previewTemplate: global.Dropzone.prototype.defaultOptions.previewTemplate,
        clickable: false,
    };
}

/**
 * Let's start the drop zone when the document is loaded (if asked so).
 */
$(document).ready(function() {
    documentLoaded = true;
    if (optionsStack.length !== 0) {
        startDropzone(optionsStack[optionsStack.length - 1]);
    }
});

/**
 * Stop the file uploader.
 */
function stopDropzone() {
    if (dropzone !== null) {
        dropzone.destroy();
        dropzone = null;
    }
}

/**
 * (Re) start the file uploader.
 */
function startDropzone(customOptions) {
    stopDropzone();
    var options = {},
        defaultOptions = getDefaultOptions();
    Object.keys(defaultOptions).forEach(function(optionKey) {
        options[optionKey] = customOptions.hasOwnProperty(optionKey) ? customOptions[optionKey] : defaultOptions[optionKey];
    });
    var showProgressbar = options.previewsContainer === false;
    $.extend(options, {
        method: 'POST',
        url: global.CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
        sending: function(file, xhr, formData) {
            if(showProgressbar) {
                NProgress.start();
            }
            formData.append('responseFormat', 'dropzone');
            var options = optionsStack[optionsStack.length - 1];
            if (options.originalPageID) {
                formData.append('ocID', isFunction(options.originalPageID) ? options.originalPageID() : options.originalPageID);
            }
            if (options.replacingFileID) {
                formData.append('fID', isFunction(options.replacingFileID) ? options.replacingFileID() : options.replacingFileID);
            }
            if (options.folderID) {
                formData.append('currentFolder', isFunction(options.folderID) ? options.folderID() : options.folderID);
            }
            if (options.uploadStarted) {
                options.uploadStarted(file, xhr, formData);
            }
        },
        success: function(data, r) {
            handleResponse(r);
        },
        chunksUploaded: function (file, done) {
            if (file.xhr.response) {
                handleResponse(JSON.parse(file.xhr.response));
            }
            done();
        },
        // We may need to allow people to re-try uploading a file if maxFiles === 1 and the upload of the file filed
        error: function(files, message, xhr) {
            this.defaultOptions.error.apply(this, arguments);
            if (this.options.maxFiles === 1 && files) {
                if (!(files instanceof Array)) {
                    files = [files];
                }
                files.forEach(function(file) {
                    if (file && file.accepted) {
                        file.accepted = false;
                    }
                });
            }
            if (optionsStack[optionsStack.length - 1].uploadFailed) {
                optionsStack[optionsStack.length - 1].uploadFailed(message);
            } else {
                ConcreteAlert.error({
                    message: message,
                    appendTo: document.body
                });
            }
        },
        queuecomplete: function() {
            if(showProgressbar) {
                NProgress.done();
            }
            if (optionsStack.length && optionsStack[optionsStack.length - 1].uploadQueueCompleted) {
                optionsStack[optionsStack.length - 1].uploadQueueCompleted();
            }
            if (uploadedFiles.length !== 0) {
                ConcreteEvent.publish('FileManagerAddFilesComplete', {files: uploadedFiles});
                uploadedFiles = [];
            }
        }
    });
    dropzone = new global.Dropzone(
        window.document.body,
        options
    );
}

/**
 * Handles the dropzone succesfull responses.
 */
function handleResponse(response) {
    if (!response) {
        return;
    }
    ConcreteAjaxRequest.validateResponse(
        response,
        function(good) {
            var options = optionsStack[optionsStack.length - 1];
            if (!good) {
                if (response.message) {
                    if (options.uploadFailed) {
                        options.uploadFailed(response.message);
                    }
                    ConcreteAlert.notify({
                        title: response.title,
                        message: response.message,
                        appendTo: document.body
                    });
                }
            } else {
                if (response.files && response.files.length) {
                    if (options.uploadCompleted) {
                        options.uploadCompleted(response.files);
                    } else {
                        var replacingFileID = isFunction(options.replacingFileID) ? options.replacingFileID() : options.replacingFileID;
                        if (replacingFileID) {
                            ConcreteEvent.publish('FileManagerReplaceFileComplete', {files: response.files});
                        } else {
                            response.files.forEach(function (file) {
                                uploadedFiles.push(file);
                            });
                        }
                    }
                }
            }
        }
    );
}

/**
 * Check if an object is a function
 * @see https://jsperf.com/alternative-isfunction-implementations
 */
function isFunction(value) {
    return !!(value && value.constructor && value.call && value.apply);
}

var ccm_fileUploader = {};
Object.defineProperties(ccm_fileUploader, {
    /**
     * Start the file uploader, or re-configure it with specific options.
     * @param Object options
     */
    start: {
        writable: false,
        value: function(options) {
            options = options || {};
            optionsStack.push(options);
            if (documentLoaded) {
                startDropzone(options);
            }
        }
    },
    /**
     * Stop the file uploader (or reset it to a previous state)
     * @param Object options the same "options" variable passed to the start method (it must be the same object instance)
     */
    stop: {
        writable: false,
        value: function(options) {
            var optionsIndex = optionsStack.indexOf(options);
            if (optionsIndex < 0) {
                global.console.error('Invalid options passed to ccm_fileUploader.stop()');
                return;
            }
            var isCurrent = optionsIndex === optionsStack.length - 1;
            optionsStack.splice(optionsIndex, 1);
            if (!isCurrent) {
                return;
            }
            stopDropzone();
            if (documentLoaded && optionsStack.length > 0) {
                startDropzone(optionsStack[optionsStack.length - 1]);
            }
        }
    }
});

Object.defineProperties(global, {
    ccm_fileUploader: {
        writable: false,
        value: ccm_fileUploader
    }
});

})(this, jQuery);
