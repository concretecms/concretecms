/* jshint unused:vars, undef:true, node:true */

module.exports = function(grunt, config, parameters, done) {

	var fs = require('fs');
	var execFile = require('child_process').execFile;
	var https = require('https');
	var path = require('path');
	var StringDecoder = require('string_decoder').StringDecoder;
	var decoder = new StringDecoder('utf8');
	var fixPluralRules = {
		// Belarusian
		'be': {
			pluralForms: 3,
			pluralFormula: 'n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2'
		},
		// Polish
		'pl': {
			pluralForms: 3,
			pluralFormula: 'n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2'
		},
		// Russian
		'ru': {
			pluralForms: 3,
			pluralFormula: 'n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2'
		},
		// Ukrainian
		'uk': {
			pluralForms: 3,
			pluralFormula: 'n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2'
		}
	};

	function mkdir(dir, mode) {
		try {
			fs.mkdirSync(dir, mode);
		}
		catch(e) {
			if(e.errno !== 34 && e.errno !== -4058) {
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
							};
							break;
					}
				}
				response
					.on('data', function(chunk) {
						if(fd) {
							try {
								fs.writeSync(fd, decoder.write(chunk));
							}
							catch(e) {
								cleanup(false);
								process.stderr.write('Error saving to file ' + options.filename + ': ' + e.message + '\n');
								done(false);
								return;
							}
						}
						else {
							data.push(chunk);
						}
					})
					.on('end', function() {
						if(!goodResponse) {
							cleanup(false);
							var msg = '';
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
		});
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
		txProgressLimit = 90;
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
		};
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
				var match;
				process.stdout.write('done.\n');
				var fixRules = null;
				if (fixPluralRules.hasOwnProperty(locale.code.toLowerCase())) {
					fixRules = fixPluralRules[locale.code.toLowerCase()];
				} else {
					match = locale.code.match(/^(\w+)[_\-]/);
					if (match && fixPluralRules.hasOwnProperty(match[1].toLowerCase())) {
						fixRules = fixPluralRules[match[1].toLowerCase()];
					}
				}
				if (fixRules === null) {
					callback();
					return;
				}
				process.stdout.write('\tfixing plural rules... ');
				var po = fs.readFileSync(locale.poFile, 'utf8');
				po = po.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
				match = po.match(/\n"Plural-Forms:\s*nplurals\s*=\s*(\d+)\s*; plural\s*=.*?"\s*\n/i);
				var originalPluralForms = match ? parseInt(match[1], 10) : null;
				if (originalPluralForms === null || originalPluralForms === fixRules.pluralForms) {
					process.stdout.write('not needed.\n');
					callback();
					return;
				}
				process.stdout.write(' (converting from ' + originalPluralForms + ' to ' + fixRules.pluralForms + ' plural forms)... ');
				po = po.replace(match[0], '\n"Plural-Forms: nplurals=' + fixRules.pluralForms + '; plural=' + fixRules.pluralFormula + ';\\n"\n');
				var fromLines = po.split('\n');
				var toLines = [];
				if (originalPluralForms > fixRules.pluralForms) {
					// we need to remove extra plural forms
					var keepLines = true;
					fromLines.forEach(function(line, lineIndex) {
						if (line.length > 0 && line.charAt(0) === '"') {
							if (keepLines) {
								toLines.push(line);
							}
							return;
						}
						keepLines = true;
						match = line.match(/^msgstr\[(\d+)\]/);
						if (match) {
							var pluralFormIndex = parseInt(match[1], 10);
							if (pluralFormIndex >= fixRules.pluralForms) {
								keepLines = false;
							}
						}
						if (keepLines) {
							toLines.push(line);
						}
					});
				} else {
					throw new Error('Increasing the plural forms count is not supported (not necessary at the time of writing this)');
				}
				po = toLines.join('\n');
				fs.writeFileSync(locale.poFile, po, 'utf8');
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
			locale.poFile = path.join(destinationFolder, 'application/languages/' + locale.code + '/LC_MESSAGES/messages.po');
			locale.moFile = path.join(destinationFolder, 'application/languages/' + locale.code + '/LC_MESSAGES/messages.mo');
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
