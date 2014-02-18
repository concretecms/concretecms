module.exports = function(grunt, config, parameters, done) {

	var fs = require('fs');
	var execFile = require('child_process').execFile;
	var https = require('https');
	var path = require('path');

	function mkdir(dir, mode) {
		try {
			fs.mkdirSync(dir, mode);
		}
		catch(e) {
			if(e.errno !== 34) {
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
		return fs.openSync(filename, 'w');
	}

	function get(urlPath, options, callback) {
		https.get(
			{
				hostname: 'www.transifex.com',
				path: urlPath,
				auth: parameters.txUsername + ':' + parameters.txPassword
			},
			function(response) {
				var data = [], fd = null, cleanup = function() {};
				var goodResponse = response.statusCode < 300;
				if(goodResponse) {
					switch(options.type) {
						case 'file':
							try {
								fd = createFile(options.filename);
							}
							catch(e) {
								process.stderr.write('Error creating file ' + options.filename + ': ' + e.message + '\n');
								done(false);
								return;
							}
							cleanup = function(ok) {
								try {
									fs.closeSync(fd);
								}
								catch(foo) {
								}
								if(ok === false) {
									try {
										fs.unlinkSync(options.filename);
									}
									catch(foo) {
									}
								}
							}
							break;
					}
				}
				response
					.on('data', function(chunk) {
						if(fd) {
							try {
								fs.writeSync(fd, chunk.toString());
							}
							catch(e) {
								cleanup(false);
								process.stderr.write('Error saving to file ' + options.filename + ': ' + e.message + '\n');
								done(false);
								return;
							}
						}
						else {
							data.push(chunk)
						}
					})
					.on('end', function() {
						if(!goodResponse) {
							cleanup(false);
							var msg = ''
							if(response.headers['content-type'] == 'text/plain') {
								msg = data.join('');
							}
							process.stderr.write('Error retrieving ' + urlPath + ': ' + (msg || response.statusCode) + '\n');
							done(false);
							return;
						}
						switch(options.type) {
							case 'file':
								cleanup(true);
								callback();
								break;
							case 'json':
								try {
									data = JSON.parse(data.join(''));
								}
								catch(e) {
									process.stderr.write('Error parsing response from ' + urlPath + ': ' + e.message + '\n');
									done(false);
									return;
								}
								callback(data);
								break;
						}
					})
				;
			}
		)
		.on('error', function(e) {
			process.stderr.write(e.message + '\n');
			done(false);
		})
	}

	if(!parameters.txUsername) {
		process.stderr.write('Transifex username not defined. Define a txUsername variable in Gruntfile.parameters.js file or use a --txUsername=... command line parameter.\n');
		done(false);
		return;
	}
	if(!parameters.txPassword) {
		process.stderr.write('Transifex password not defined. Define a txPassword variable in Gruntfile.parameters.js file or use a --txPassword=... command line parameter.\n');
		done(false);
		return;
	}
	if(!parameters.txResource) {
		process.stderr.write('Transifex resource not defined. Define a txResource variable in Gruntfile.parameters.js file or use a --txResource=... command line parameter.\n');
		done(false);
		return;
	}
	
	var txProgressLimit = parseFloat(parameters.txProgressLimit);
	if(isNaN(txProgressLimit)) {
		txProgressLimit = 95;
	}
	
	var getAllLocales;
	if(parameters.txLocales && parameters.txLocales.length) {
		getAllLocales = function(callback) {
			var cfgLocales = [];
			var txLocales = (typeof parameters.txLocales == 'string') ? parameters.txLocales.split(',') : parameters.txLocales;
			txLocales.forEach(function(code) {
				cfgLocales.push({code: code, name: code});
			});
			callback(cfgLocales);
		}
	}
	else {
		getAllLocales = function(callback) {
			process.stdout.write('Retrieving available locales for Transifex resource ' + parameters.txResource + '... ');
			get('/api/2/project/concrete5/resource/' + parameters.txResource + '/?details', {type: 'json'}, function(data) {
				var allLocales = [];
				data.available_languages.forEach(function(available_language) {
					switch(available_language.code) {
						case 'en': // Transifex returns this too
							break;
						default:
							allLocales.push({code: available_language.code, name: available_language.code + ' [' + available_language.name + ']'});
							break;
					}
				});
				process.stdout.write(allLocales.length + ' locales found.\n');
				callback(allLocales);
			});
		};
	}

	function downloadLocale(locale, callback) {
		process.stdout.write('\tdownloading .po file... ');
		get(
			'/api/2/project/concrete5/resource/' + parameters.txResource + '/translation/' + locale.code + '/?file',
			{type: 'file', filename: locale.poFile},
			function() {
				process.stdout.write('done.\n');
				callback();
			}
		);
	}

	function compileLocale(locale, callback) {
		process.stdout.write('\tcompiling .mo file... ');
		execFile(
			'msgfmt', ['-o', locale.moFile, locale.poFile],
			{},
			function(error, stdout, stderr) {
				if(error !== null) {
					process.stderr.write(error.message || error);
					done(false);
					return;
				}
				process.stdout.write('done.\n');
				callback();
			}
		);
	}

	function parseLocale(allLocales, localeIndex, callback) {
		if(localeIndex >= allLocales.length) {
			callback();
			return;
		}
		var locale = allLocales[localeIndex];
		var destinationFolder;

		if (parameters.destination) {
			destinationFolder = path.resolve(__dirname, '..', parameters.destination);
		} else {
			destinationFolder = path.resolve(__dirname, config.DIR_BASE);
		}

		process.stdout.write('Locale ' + locale.name + '... ');
		get('/api/2/project/concrete5/resource/' + parameters.txResource + '/stats/' + locale.code + '/', {type: 'json'}, function(data) {
			var tot = data.translated_entities + data.untranslated_entities;
			locale.percentage = tot ? Math.round(data.translated_entities * 100 / tot) : 0;
			locale.passed = (locale.percentage >= txProgressLimit);
			process.stdout.write(' progress: ' + locale.percentage + '% -> ' + (locale.passed ? 'ok' : ('skipped (less than ' + txProgressLimit + '%)')) + ' \n');
			if(!locale.passed) {
				parseLocale(allLocales, localeIndex + 1, callback);
				return;
			}
			locale.poFile = path.join(destinationFolder, 'languages/' + locale.code + '/LC_MESSAGES/messages.po');
			locale.moFile = path.join(destinationFolder, 'languages/' + locale.code + '/LC_MESSAGES/messages.mo');
			downloadLocale(locale, function() {
				compileLocale(
					locale,
					function() {
						process.stdout.write('\tdeleting .po file... ');
						try {
							fs.unlinkSync(locale.poFile);
						}
						catch(e) {
							process.stderr.write(e.message || e);
							done(false);
							return;
						}
						process.stdout.write('done.\n');
						parseLocale(allLocales, localeIndex + 1, callback);
					}				
				);
			});
		});
	}

	getAllLocales(function(allLocales) {
		parseLocale(allLocales, 0, function() {
			done(true);
		});
	});

};
