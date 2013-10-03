module.exports = function(grunt) {
	var config = {};


	// If concrete5 is installed in a subfolder and if you want to use javascript sourcemap you should change this variable.
	// For instance if your concrete5 installation is at http://www.domain.com/c5subfolder, you should change it to:
	// config.DIR_REL = '/c5subfolder';
	config.DIR_REL = '';

	// The path to the web folder, relative to this Gruntfile
	config.DIR_BASE = '../web';

	// Options for the generation of JavaScripts. See https://github.com/gruntjs/grunt-contrib-uglify
	var jsOptions = {
		mangle: true,
		compress: true,
		beautify: false,
		report: 'min',
		preserveComments: false,
		banner: '',
		footer: ''
	};

	// List of the JavaScripts to be generated
	var js = {
		bootstrap: {
			dest: '<%= DIR_BASE %>/concrete/js/bootstrap.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/bootstrap/bootstrap.tooltip.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/bootstrap.popover.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/bootstrap.dropdown.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/bootstrap.transitions.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/bootstrap.alert.js'
			]
		},
		jquery_cookie: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.cookie.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/jquery.cookie.js'
		},
		ccm_dashboard: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.dashboard.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/dashboard.js'
		},
		dynatree: {
			dest: '<%= DIR_BASE %>/concrete/js/dynatree.js',
			src: '<%= DIR_BASE %>/concrete/js/dynatree/dynatree.js'
		},
		ccm_app: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.app.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.colorpicker.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.hoverIntent.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.liveupdate.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.metadata.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/chosen.jquery.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/filemanager.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.cookie.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/layouts.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/legacy_dialog.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/newsflow.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/page_reindexing.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/group_tree.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/quicksilver.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/remote_marketplace.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/search.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/sitemap.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/status_bar.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/tabs.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/tinymce_integration.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/ui.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/toolbar.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/themes.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/composer.js'
			]
		}
	};

	// Options for the generation of the CSS files. See https://github.com/gruntjs/grunt-contrib-less
	var cssOptions = {
		ieCompat: true,
		optimization: null,
		strictImports: false,
		syncImport: false,
		dumpLineNumbers: false,
		relativeUrls: false,
		report: 'min'
	};

	// List of the CSS files to be generated
	var css = {
		'<%= DIR_BASE %>/concrete/css/jquery.ui.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/jquery.ui.less',
		'<%= DIR_BASE %>/concrete/css/jquery.rating.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/jquery.rating.less',
		'<%= DIR_BASE %>/concrete/css/ccm.default.theme.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.default.theme.less',
		'<%= DIR_BASE %>/concrete/css/ccm.dashboard.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.dashboard.less',
		'<%= DIR_BASE %>/concrete/css/ccm.dashboard.1200.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.dashboard.1200.less',
		'<%= DIR_BASE %>/concrete/css/ccm.colorpicker.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.colorpicker.less',
		'<%= DIR_BASE %>/concrete/css/ccm.app.mobile.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.app.mobile.less',
		'<%= DIR_BASE %>/concrete/css/ccm.app.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.app.less'
	};
	// Let's include the dependencies
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');

	// Now let's build the final configuration for Grunt.
	var extend = require('util')._extend;
	var fs = require('fs');

	// Let's define the uglify section (for generating JavaScripts)
	var jsTargets = {release: [], debug: []};
	config.uglify = {options: jsOptions};
	for(var key in js) {
		var target = {files: {}};
		target.files[js[key].dest] = js[key].src;
		config.uglify[key + '_release'] = extend({}, target);
		jsTargets.release.push('uglify:' + key + '_release');
		target.options = {};
		target.options.sourceMap = js[key].dest + '.map';
		target.options.sourceMappingURL = target.options.sourceMap.replace(/<%=\s*DIR_BASE\s*%>/g, '<%= DIR_REL %>');
		target.options.sourceMapRoot = '<%= DIR_REL %>/';
		target.options.sourceMapPrefix = 1 + config.DIR_BASE.replace(/\/\/+/g, '/').replace(/[^\/]/g, '').length;
		config.uglify[key + '_debug'] = target;
		jsTargets.debug.push('uglify:' + key + '_debug');
	}

	// Let's define the less section (for generating CSS files)
	config.less = {
		options: cssOptions,
		debug: {
			options: {
				compress: true,
				yuicompress: false
			},
			files: css
		},
		release: {
			options: {
				compress: true,
				yuicompress: true
			},
			files: css
		}
	};
	
	// Download and compile translations
	function getC5Config() {
		var cfg = null;
		if(fs.existsSync('./Gruntfile.c5config.js')) {
			cfg = require('./Gruntfile.c5config.js');
		}
		return cfg || {};
	}

	// Set Grunt tasks
	grunt.initConfig(config);
	grunt.registerTask('js:debug', jsTargets.debug);
	grunt.registerTask('js:release', jsTargets.release);
	grunt.registerTask('js', 'js:release');
	
	grunt.registerTask('css:debug', 'less:debug');
	grunt.registerTask('css:release', 'less:release');
	grunt.registerTask('css', 'css:release');
	
	grunt.registerTask('debug', ['js:debug', 'css:debug']);
	grunt.registerTask('release', ['js:release', 'css:release']);

	grunt.registerTask('translations', 'Download and compile translations.', function() {
		var done = this.async();
		var cfg = getC5Config();
		if(!cfg.txUsername) {
			process.stderr.write('Transifex username not defined. Define a txUsername variable in Gruntfile.c5config.js file.\n');
			done(false);
			return;
		}
		if(!cfg.txPassword) {
			process.stderr.write('Transifex password not defined. Define a txPassword variable in Gruntfile.c5config.js file.\n');
			done(false);
			return;
		}
		if(!cfg.txResource) {
			process.stderr.write('Transifex resource not defined. Define a txResource variable in Gruntfile.c5config.js file.\n');
			done(false);
			return;
		}
		var execFile = require('child_process').execFile;
		var https = require('https');
		var path = require('path');
		function get(urlPath, options, callback) {
			https.get(
				{
					hostname: 'www.transifex.com',
					path: urlPath,
					auth: cfg.txUsername + ':' + cfg.txPassword
				},
				function(response) {
					var data = [], fd = null, cleanup = function() {};
					var goodResponse = response.statusCode < 300;
					if(goodResponse) {
						switch(options.type) {
							case 'file':
								response.setEncoding('binary')
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
									fs.writeSync(fd, chunk.toString('binary'));
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

		process.stdout.write('Retrieving available locales for Transifex resource ' + cfg.txResource + '... ');
		get('/api/2/project/concrete5/resource/' + cfg.txResource + '/?details', {type: 'json'}, function(data) {
			var allLocales = [];
			data.available_languages.forEach(function(available_language) {
				switch(available_language.code) {
					case 'en': // Transifex returns this too
						break;
					default:
						allLocales.push({code: available_language.code, name: available_language.name});
						break;
				}
			});
			process.stdout.write(allLocales.length + ' locales found.\n');
			var locales = [];
			function getLocaleInfo(localeIndex, callback) {
				if(localeIndex >= allLocales.length) {
					callback();
					return;
				}
				var locale = allLocales[localeIndex];
				process.stdout.write('Locale ' + locale.code + ' [' + locale.name + ']... ');
				get('/api/2/project/concrete5/resource/' + cfg.txResource + '/stats/' + locale.code + '/', {type: 'json'}, function(data) {
					var tot = data.translated_entities + data.untranslated_entities;
					var perc = tot ? Math.round(data.translated_entities * 100 / tot) : 0;
					var passed = (perc >= 95);
					process.stdout.write(' progress: ' + perc + '% -> ' + (passed ? 'ok' : 'skipped') + ' \n');
					if(!passed) {
						getLocaleInfo(localeIndex + 1, callback);
						return;
					}
					locale.poFile = path.join(config.DIR_BASE, 'languages/' + locale.code + '/LC_MESSAGES/messages.po');
					locale.moFile = path.join(config.DIR_BASE, 'languages/' + locale.code + '/LC_MESSAGES/messages.mo');
					locales.push(locale);
					process.stdout.write('\tdownloading .po file... ');
					get('/api/2/project/concrete5/resource/' + cfg.txResource + '/translation/' + locale.code + '/?file', {type: 'file', filename: locale.poFile}, function() {
						process.stdout.write('done.\n');
						process.stdout.write('\tcompiling .mo file... ');
						execFile(
							'msgfmt', ['-o', locale.moFile, locale.poFile], {}, function(error, stdout, stderr) {
								if(error !== null) {
									process.stderr.write(error.message || error);
									done(false);
									return;
								}
								process.stdout.write('done.\n');
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
								getLocaleInfo(localeIndex + 1, callback);
							}
						);
					});
				});
			}
			getLocaleInfo(0, function() {
				done(true);
			});
		});
		
	});

	grunt.registerTask('default', 'release');
};