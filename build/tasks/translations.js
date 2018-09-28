/* jshint unused:vars, undef:true, node:true */

module.exports = function(grunt, config, parameters, done) {

	var fs = require('fs');
	var url = require('url'), https = require('https');
	var path = require('path');

	function mkdir(dir, mode) {
		try {
			fs.mkdirSync(dir, mode);
		}
		catch(e) {
			if(e.errno !== 34 && e.errno !== -4058 && e.errno !== -2) {
				throw e;
			}
			mkdir(path.dirname(dir), mode);
			mkdir(dir, mode);
		}
	}

	function createFile(filename) {
		filename = path.normalize(filename);
		var dir = path.dirname(filename);
		if(!fs.existsSync(dir)) {
			mkdir(dir);
		}
		return fs.createWriteStream(
		    filename,
		    {
		        flags: 'w',
		        defaultEncoding: 'binary',
		        autoClose: false
		    }
		);
	}

	function get(urlPath, options, callback) {
        var fullURL = url.parse(parameters.ctEntryPoint.replace(/\/+$/, '') + '/' + urlPath.replace(/^\/+/, ''));
        var getOptions = {
            protocol: fullURL.protocol,
            hostname: fullURL.hostname,
            path: fullURL.path,
        };
        if (fullURL.port) {
            getOptions.port = fullURL.port;
        }
        if (parameters.ctAPIToken) {
            getOptions.headers = {
                'API-Token': parameters.ctAPIToken
            };
        }
        var cleanup = function() {}, fd = null;
        https.get(
            getOptions,
            function (response) {
                var goodResponse = response.statusCode < 300;
                if (goodResponse && options.type === 'file') {
                    cleanup = function(ok) {
                        if (fd !== null) {
                            try {
                                fs.closeSync(fd);
                            }
                            catch(foo) {
                            }
                            fd = null;
                        }
                        if (ok) {
                            return;
                        }
                        try {
                            fs.unlinkSync(options.filename);
                        }
                        catch(foo) {
                        }
                    };
                    try {
                        fd = createFile(options.filename);
                    }
                    catch(e) {
                        process.stderr.write('Error creating file ' + options.filename + ': ' + e.message + '\n');
                        done(false);
                        return;
                    }
                    response.pipe(fd);
                    fd.on('finish', function() {
                        cleanup(true);
                        callback();
                    });
                    return;
                }
                var data = [];
                response
                    .on('data', function(chunk) {
                        data.push(chunk);
                    })
                    .on('end', function() {
                        if(!goodResponse) {
                            var msg = '';
                            if(response.headers['content-type'] && /^text\/plain(;|$)/i.test(response.headers['content-type'])) {
                                msg = data.join('');
                            }
                            process.stderr.write('Error retrieving ' + urlPath + ': ' + (msg || response.statusCode) + '\n');
                            done(false);
                            return;
                        }
                        try {
                            data = JSON.parse(data.join(''));
                        }
                        catch(e) {
                            process.stderr.write('Error parsing response from ' + urlPath + ': ' + e.message + '\n');
                            done(false);
                            return;
                        }
                        callback(data);
                    })
                ;
            }
        ).on('error', function(e) {
            cleanup();
            process.stderr.write(e.message + '\n');
            done(false);
        });
	}

	if(!parameters.ctEntryPoint) {
		process.stderr.write('Community Translation API Entry point not defined. Define a ctEntryPoint variable in Gruntfile.parameters.js file or use a --ctEntryPoint=... command line parameter.\n');
		done(false);
		return;
	}
    if(!parameters.ctPackage) {
        process.stderr.write('Community Translation package handle not defined. Define a ctPackage variable in Gruntfile.parameters.js file or use a --ctPackage=... command line parameter.\n');
        done(false);
        return;
    }
    if(!parameters.ctPackageVersion) {
        process.stderr.write('Community Translation package version not defined. Define a ctPackageVersion variable in Gruntfile.parameters.js file or use a --ctPackageVersion=... command line parameter.\n');
        done(false);
        return;
    }

	var ctProgressLimit = parseFloat(parameters.ctProgressLimit);
	if(isNaN(ctProgressLimit) || ctProgressLimit < 0) {
	    ctProgressLimit = 90;
	}

	var getLocales;
	if(parameters.ctLocales && parameters.ctLocales.length) {
	    getLocales = function(callback) {
			var cfgLocales = [];
			var ctLocales = (typeof parameters.ctLocales == 'string') ? parameters.ctLocales.split(',') : parameters.ctLocales;
			ctLocales.forEach(function(id) {
				cfgLocales.push({id: id, name: id});
			});
			callback(cfgLocales);
		};
	}
	else {
	    getLocales = function(callback) {
			process.stdout.write('Retrieving locales for ' + parameters.ctPackage + ' ' + parameters.ctPackageVersion + ' with a progress >= ' + ctProgressLimit + '... ');
			get('package/' + parameters.ctPackage + '/' + parameters.ctPackageVersion + '/locales/' + ctProgressLimit, {type: 'json'}, function(locales) {
				process.stdout.write(locales.length + ' locales found.\n');
				callback(locales);
			});
		};
	}

	function processLocale(locales, localeIndex, callback) {
		if(localeIndex >= locales.length) {
			callback();
			return;
		}
		var locale = locales[localeIndex];
		var destinationFolder;

		if (parameters.destination) {
			destinationFolder = path.resolve(__dirname, '..', parameters.destination);
		} else {
			destinationFolder = path.resolve(__dirname, config.DIR_BASE);
		}

		process.stdout.write('Downloading translations for ' + locale.name + '... ');
		var moFile = path.join(destinationFolder, 'application/languages/' + locale.id + '/LC_MESSAGES/messages.mo');
		get('package/' + parameters.ctPackage + '/' + parameters.ctPackageVersion + '/translations/' + locale.id + '/mo', {type: 'file', filename: moFile}, function(data) {
		    process.stdout.write('done.\n');
			processLocale(locales, localeIndex + 1, callback);
		});
	}

	getLocales(function(locales) {
	    processLocale(locales, 0, function() {
			done(true);
		});
	});

};
