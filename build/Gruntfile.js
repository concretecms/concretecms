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

	// Options for the tool that will merge the files
	var concatOptions = {
		separator: ''
	};

	// List of the files to be merged
	var concat = {
		image_editor: {
			beforeJS: true,
			dest: '<%= DIR_BASE %>/concrete/js/image_editor/image_editor.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/image_editor/build/kinetic.prototype.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/imageeditor.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/history.js.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/events.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/elements.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/controls.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/save.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/extend.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/background.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/imagestage.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/image.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/actions.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/slideOut.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/jquerybinding.js',
				'<%= DIR_BASE %>/concrete/js/image_editor/build/filters.js'
			]
		}
	};

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
		ccm_app: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.app.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/ccm_app/json.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.form.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/concrete5.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.liveupdate.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/chosen.jquery.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.pep.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/base.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/ajax_request/base.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/ajax_request/form.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/ajax_request/block.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/edit_mode.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.cookie.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/panels.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/dialog.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/alert.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/newsflow.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/editable_field/container.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/page_reindexing.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/in_context_menu.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/quicksilver.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/remote_marketplace.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/search/base.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/progressive_operations.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/custom_style.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/tabs.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/toolbar.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/themes.js'
			]
		},
		filemanager: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.filemanager.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.iframe-transport.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/jquery.fileupload.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/filemanager/search.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/filemanager/selector.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/filemanager/menu.js'
			]
		},
		bootstrap: {
			dest: '<%= DIR_BASE %>/concrete/js/bootstrap.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/bootstrap/alert.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/button.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/tooltip.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/dropdown.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/popover.js',
				'<%= DIR_BASE %>/concrete/js/bootstrap/transitions.js',
			]
		},
		underscore: {
			dest: '<%= DIR_BASE %>/concrete/js/underscore.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/underscore.js'
		},

		jquery_cookie: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.cookie.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/jquery.cookie.js'
		},

		jquery_fileupload: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.fileupload.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/jquery.fileupload.js'
		},


		jquery_form: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.form.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/jquery.form.js'
		},

		jquery_colorpicker: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.colorpicker.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/colorpicker.js'
		},

		kinetic: {
			dest: '<%= DIR_BASE %>/concrete/js/kinetic.js',
			src: '<%= DIR_BASE %>/concrete/js/image_editor/build/kinetic.js'
		},


		jquery_backstretch: {
			dest: '<%= DIR_BASE %>/concrete/js/jquery.backstretch.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/backstretch.js'
		},

		ccm_observer: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.pubsub.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/pubsub.js'
		},

		ccm_composer: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.composer.js',
			src: '<%= DIR_BASE %>/concrete/js/composer/composer.js'
		},

		ccm_sitemap: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.sitemap.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/sitemap.js'
		},

		ccm_topics: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.topics.js',
			src: '<%= DIR_BASE %>/concrete/js/topics/topics.js'
		},

		ccm_groups: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.groups.js',
			src: '<%= DIR_BASE %>/concrete/js/groups/groups.js'
		},

		ccm_layouts: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.layouts.js',
			src: '<%= DIR_BASE %>/concrete/js/layouts/layouts.js'
		},

		ccm_conversations: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.conversations.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/conversations/conversations.js',
				'<%= DIR_BASE %>/concrete/js/conversations/attachments.js'
			]
		},

		bootstrap_editable: {
			dest: '<%= DIR_BASE %>/concrete/js/bootstrap-editable.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/bootstrap-editable/bootstrap3-editable.js',
				'<%= DIR_BASE %>/concrete/js/ccm_app/editable_field/attribute.js'
			]
		},


		ccm_gathering: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.gathering.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/gathering/packery.js',
				'<%= DIR_BASE %>/concrete/js/gathering/gathering.js'
			]
		},

		ccm_dashboard: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.dashboard.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_app/dashboard.js'
		},
		dynatree: {
			dest: '<%= DIR_BASE %>/concrete/js/dynatree.js',
			src: '<%= DIR_BASE %>/concrete/js/dynatree/dynatree.js'
		},
		redactor: {
			dest: '<%= DIR_BASE %>/concrete/js/redactor.js',
			src: [
				'<%= DIR_BASE %>/concrete/js/redactor/redactor.js',
				'<%= DIR_BASE %>/concrete/js/redactor/redactor.concrete5.js'
			]
		},
		account: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm_profile.js',
			src: '<%= DIR_BASE %>/concrete/js/ccm_profile/base.js'
		},
		ccm_imageeditor: {
			dest: '<%= DIR_BASE %>/concrete/js/ccm.imageeditor.js',
			src: '<%= DIR_BASE %>/concrete/js/image_editor/image_editor.js'
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
		'<%= DIR_BASE %>/concrete/css/ccm.app.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.app.less',
		'<%= DIR_BASE %>/concrete/css/ccm.editable.fields.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.editable.fields.less',
		'<%= DIR_BASE %>/concrete/css/jquery.ui.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/jquery.ui.less',
		'<%= DIR_BASE %>/concrete/css/jquery.rating.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/jquery.rating.less',
		'<%= DIR_BASE %>/concrete/css/ccm.default.theme.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.default.theme.less',
		'<%= DIR_BASE %>/concrete/css/ccm.dashboard.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.dashboard.less',
		'<%= DIR_BASE %>/concrete/css/ccm.colorpicker.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.colorpicker.less',
		'<%= DIR_BASE %>/concrete/css/ccm.app.mobile.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.app.mobile.less',
		'<%= DIR_BASE %>/concrete/css/ccm.composer.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.composer.less',
		'<%= DIR_BASE %>/concrete/css/ccm.image_editor.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.image_editor.less',
		'<%= DIR_BASE %>/concrete/css/ccm.account.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.account.less',
		'<%= DIR_BASE %>/concrete/css/dynatree.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/dynatree.less',
		'<%= DIR_BASE %>/concrete/css/ccm.sitemap.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.sitemap.less',
		'<%= DIR_BASE %>/concrete/css/ccm.filemanager.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.filemanager.less',
		'<%= DIR_BASE %>/concrete/css/ccm.conversations.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.conversations.less',
		'<%= DIR_BASE %>/concrete/css/ccm.gathering.display.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.gathering.display.less',
		'<%= DIR_BASE %>/concrete/css/ccm.gathering.base.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/ccm.gathering.base.less',
		'<%= DIR_BASE %>/concrete/css/redactor.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/redactor.less',
		'<%= DIR_BASE %>/concrete/css/ccm.topics.css': '<%= DIR_BASE %>/concrete/css/ccm_app/build/topics.less',
		'<%= DIR_BASE %>/concrete/css/ccm.image_editor.css': '<%= DIR_BASE %>/concrete/css/image_editor/build/image_editor.less',
	};

	// Let's include the dependencies
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Now let's build the final configuration for Grunt.
	var extend = require('util')._extend;

	// Let's define the concat section (for concatenating giles)
	config.concat = extend({options: concatOptions}, concat);

	// Let's define the uglify section (for generating JavaScripts)
	var jsTargets = {release: [], debug: []};
	for(var concatKey in concat) {
		if(concat[concatKey].beforeJS) {
			jsTargets.release.push('concat:' + concatKey);
			jsTargets.debug.push('concat:' + concatKey);
		}
	}

	var watchJS = [];
	var watchCSS = [];

	config.uglify = {options: jsOptions};
	for(var key in js) {
		var target = {files: {}};
		target.files[js[key].dest] = js[key].src;

		var srcFile = js[key].src;
		if (typeof(srcFile) == 'string') {
			watchJS.push(srcFile);
		} else {
			for (i = 0; i < srcFile.length; i++) {
				watchJS.push(srcFile[i]);
			}
		}

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


	config.watch = {
		javascript: {
           	files: watchJS,
            tasks: ['js']
        },

		css: {
           	files: '<%=DIR_BASE%>/concrete/css/**/*.less',
            tasks: ['css']
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
	grunt.registerTask('debug', ['js:debug', 'css:debug']);

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
