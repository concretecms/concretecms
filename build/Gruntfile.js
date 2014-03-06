module.exports = function(grunt) {
	var fs = require('fs');

	var parameters = null;
	if(fs.existsSync(__dirname + '/Gruntfile.parameters.js')) {
		parameters = require(__dirname + '/Gruntfile.parameters.js');
	}
	parameters = parameters || {};
	grunt.option.flags().forEach(function(p) {
		var m = /^--(.+?)=(.+)$/.exec(p);
		if(m) {
			parameters[m[1]] = m[2];
		}
	});
	
	var config = {};

	config.DIR_REL = ('DIR_REL' in parameters) ? parameters.DIR_REL : '';
	config.DIR_BASE = ('DIR_BASE' in parameters) ? parameters.DIR_BASE : '../web';

	// Options for the generation of JavaScripts. See https://github.com/gruntjs/grunt-contrib-uglify
	var jsOptions = {
		mangle: true,
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

	// Let's define the uglify section (for generating JavaScripts)
	var jsTargets = {release: [], debug: []};
	config.uglify = {options: jsOptions};
	for(var key in js) {
		var target = {files: {}};
		target.files[js[key].dest] = js[key].src;
		config.uglify[key + '_release'] = extend({options: {compress: {warnings: false}}}, target);
		jsTargets.release.push('uglify:' + key + '_release');
		target.options = {compress: {warnings: true}};
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
		require('./tasks/translations.js')(grunt, config, parameters, this.async());
	});

	grunt.registerTask('remove-short-tags', 'Remove short tags.', function() {
		require('./tasks/remove-short-tags.js')(grunt, config, parameters, this.async());
	});

	grunt.registerTask('build-release-start', 'Create concrete5 release from Git, run various required functions.', function() {
		require('./tasks/build-release/start.js')(grunt, config, parameters, this.async());
	});

	grunt.registerTask('build-release-clean', 'Remove certain dotfiles.', function() {
		require('./tasks/build-release/clean.js')(grunt, config, parameters, this.async());
	});

	grunt.registerTask('build-release-finish', 'Create zip file and finish.', function() {
		require('./tasks/build-release/finish.js')(grunt, config, parameters, this.async());
	});

	var buildTranslationParameters = extend({}, parameters);
	buildTranslationParameters.destination = './release/concrete5-master/web';

	var buildTagParameters = extend({}, parameters);
	buildTagParameters = parameters;
	buildTagParameters.source = './release/concrete5-master/web';

	grunt.registerTask('build-release-translations', 'Downloading Translations.', function() {
		require('./tasks/translations.js')(grunt, config, buildTranslationParameters, this.async());
	});

	grunt.registerTask('build-release-remove-short-tags', 'Remove short tags.', function() {
		require('./tasks/remove-short-tags.js')(grunt, config, buildTagParameters, this.async());
	});

	grunt.registerTask('build-release', ['build-release-start', 'build-release-remove-short-tags', 'build-release-translations', 'build-release-clean', 'build-release-finish']);

	grunt.registerTask('default', 'release');
};