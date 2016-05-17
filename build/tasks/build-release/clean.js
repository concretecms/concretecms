module.exports = function(grunt, config, parameters, done) {
	var workFolder = parameters.releaseWorkFolder || './release/source';
	
	var remove = {
		file: {
			byName: [
				'.coveralls.yml',
				'.DS_Store',
				'.gitattributes',
				'.gitignore',
				'.gitmodules',
				'.php_cs',
				'.scrutinizer.yml',
				'.travis.yml',
				'build.properties.dev',
			],
			byPath: [
				'/concrete/vendor/dapphp/securimage/example_form.ajax.php',
				'/concrete/vendor/dapphp/securimage/example_form.php',
				'/concrete/vendor/doctrine/dbal/SECURITY.md',
				'/concrete/vendor/doctrine/migrations/box.json',
				'/concrete/vendor/doctrine/migrations/package.php',
				'/concrete/vendor/doctrine/migrations/phar-cli-stub.php',
				'/concrete/vendor/doctrine/migrations/UPGRADE-1.0.MD',
				'/concrete/vendor/htmlawed/htmlawed/htmLawedTest.php',
				'/concrete/vendor/imagine/imagine/lib/Imagine/resources/Adobe/Color Profile Bundling License_10.15.08.pdf',
				'/concrete/vendor/imagine/imagine/lib/Imagine/resources/Adobe/Profile Information.pdf',
				'/concrete/vendor/kertz/twitteroauth/callback.php',
				'/concrete/vendor/kertz/twitteroauth/clearsessions.php',
				'/concrete/vendor/kertz/twitteroauth/config-sample.php',
				'/concrete/vendor/kertz/twitteroauth/connect.php',
				'/concrete/vendor/kertz/twitteroauth/html.inc',
				'/concrete/vendor/kertz/twitteroauth/index.php',
				'/concrete/vendor/kertz/twitteroauth/redirect.php',
				'/concrete/vendor/kertz/twitteroauth/test.php',
				'/concrete/vendor/league/flysystem/phpunit.php',
				'/concrete/vendor/michelf/php-markdown/Readme.php',
				'/concrete/vendor/nesbot/carbon/readme.php',
				'/concrete/vendor/simplepie/simplepie/db.sql',
				'/concrete/vendor/tedivm/jshrink/package.xml',
				'/concrete/vendor/voku/urlify/INSTALL',
				'/INSTALL\.md',
			],
			byRX: [
				/^\/concrete\/vendor\/.*\/(changelog|change_log|upgrade|upgrading|readme|contributing|history|roadmap)(\.(font|src))?\.(md|mdown|markdown|txt)$/i,
				/^\/concrete\/vendor\/.*\/composer\.(lock|json)(\.hhvm)?$/,
				/^\/concrete\/vendor\/.*\/phpunit\.(xml|xml\.dist|dist\.xml)(\.hhvm)?$/,
				/^\/concrete\/vendor\/.*\/build\.(xml|properties)(\.dist)?$/,
				/^\/concrete\/vendor\/.*\/UPGRADE_TO_\w+$/,
				/^\/concrete\/vendor\/.*\/UPGRADE$/i,
				/^\/concrete\/vendor\/htmlawed\/htmlawed\/htmLawed_[README|TESTCASE]/,
			]
		},
		dir: {
			byName: [
				'.easymin',
				'.git',
			],
			byPath: [
				'/concrete/vendor/anahkiasen/html-object/examples',
				'/concrete/vendor/anahkiasen/html-object/tests',
				'/concrete/vendor/bin',
				'/concrete/vendor/concrete5/flysystem/tests',
				'/concrete/vendor/dapphp/securimage/examples',
				'/concrete/vendor/doctrine/annotations/tests',
				'/concrete/vendor/doctrine/cache/tests',
				'/concrete/vendor/doctrine/collections/tests',
				'/concrete/vendor/doctrine/common/tests',
				'/concrete/vendor/doctrine/dbal/bin',
				'/concrete/vendor/doctrine/inflector/tests',
				'/concrete/vendor/doctrine/migrations/bin',
				'/concrete/vendor/doctrine/migrations/tests',
				'/concrete/vendor/doctrine/orm/bin',
				'/concrete/vendor/doctrine/orm/docs',
				'/concrete/vendor/egulias/email-validator/documentation',
				'/concrete/vendor/egulias/email-validator/tests',
				'/concrete/vendor/facebook/php-sdk/examples',
				'/concrete/vendor/facebook/php-sdk/tests',
				'/concrete/vendor/filp/whoops/docs',
				'/concrete/vendor/filp/whoops/examples',
				'/concrete/vendor/filp/whoops/tests',
				'/concrete/vendor/gettext/languages/bin',
				'/concrete/vendor/guzzle/guzzle/docs',
				'/concrete/vendor/guzzle/guzzle/tests',
				'/concrete/vendor/hautelook/phpass/lib',
				'/concrete/vendor/hautelook/phpass/Tests',
				'/concrete/vendor/imagine/imagine/docs',
				'/concrete/vendor/imagine/imagine/lib/Imagine/Test',
				'/concrete/vendor/imagine/imagine/tests',
				'/concrete/vendor/league/flysystem/tests',
				'/concrete/vendor/league/url/tests',
				'/concrete/vendor/lusitanian/oauth/examples',
				'/concrete/vendor/lusitanian/oauth/tests',
				'/concrete/vendor/mobiledetect/mobiledetectlib/examples',
				'/concrete/vendor/mobiledetect/mobiledetectlib/tests',
				'/concrete/vendor/monolog/monolog/doc',
				'/concrete/vendor/monolog/monolog/tests',
				'/concrete/vendor/nesbot/carbon/tests',
				'/concrete/vendor/oryzone/oauth-user-data/tests',
				'/concrete/vendor/oyejorge/less.php/bin',
				'/concrete/vendor/oyejorge/less.php/test',
				'/concrete/vendor/pagerfanta/pagerfanta/tests',
				'/concrete/vendor/psr/log/Psr/Log/Test',
				'/concrete/vendor/punic/punic/bin',
				'/concrete/vendor/punic/punic/tests',
				'/concrete/vendor/simplepie/simplepie/build',
				'/concrete/vendor/simplepie/simplepie/compatibility_test',
				'/concrete/vendor/simplepie/simplepie/demo',
				'/concrete/vendor/simplepie/simplepie/tests',
				'/concrete/vendor/sunra/php-simple-html-dom-parser/Src/Sunra/PhpSimple/simplehtmldom_1_5/app',
				'/concrete/vendor/sunra/php-simple-html-dom-parser/Src/Sunra/PhpSimple/simplehtmldom_1_5/example',
				'/concrete/vendor/sunra/php-simple-html-dom-parser/Src/Sunra/PhpSimple/simplehtmldom_1_5/manual',
				'/concrete/vendor/sunra/php-simple-html-dom-parser/Src/Sunra/PhpSimple/simplehtmldom_1_5/testcase',
				'/concrete/vendor/symfony/class-loader/Symfony/Component/ClassLoader/Tests',
				'/concrete/vendor/symfony/console/Symfony/Component/Console/Tests',
				'/concrete/vendor/symfony/debug/Symfony/Component/Debug/Tests',
				'/concrete/vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/Tests',
				'/concrete/vendor/symfony/finder/Symfony/Component/Finder/Tests',
				'/concrete/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Tests',
				'/concrete/vendor/symfony/http-kernel/Symfony/Component/HttpKernel/Tests',
				'/concrete/vendor/symfony/routing/Symfony/Component/Routing/Tests',
				'/concrete/vendor/symfony/serializer/Symfony/Component/Serializer/Tests',
				'/concrete/vendor/tedivm/jshrink/tests',
				'/concrete/vendor/tedivm/stash/tests',
				'/concrete/vendor/true/punycode/tests',
				'/concrete/vendor/voku/urlify/tests',
			],
			byRX: [
			]
		}
	};
	function shouldRemove(kind, rel, name) {
		if(remove[kind].byName.indexOf(name) >= 0) {
			return true;
		}
		if(remove[kind].byPath.indexOf(rel) >= 0) {
			return true;
		}
		for(var i = 0; i < remove[kind].byRX.length; i++) {
			if(remove[kind].byRX[i].test(rel)) {
				return true;
			}
		}
		return false;
	}
	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}
	try {
		var c5fs = require('../../libraries/fs'),
			fs = require('fs'),
			shell = require('shelljs'),
			path = require('path');
		var parser = new c5fs.directoryParser(workFolder);
		parser.excludeDirectoriesByName = [];
		parser.excludeFilesByName = [];
		parser.onDirectory = function(cb, abs, rel, name) {
			var skipContents = false;
			if(shouldRemove('dir', rel, name)) {
				process.stdout.write('Removing directory ' + rel + '\n');
				shell.rm('-rf', abs);
				if(c5fs.fileExists(abs)) {
					throw new Error('Unable to remove ' + abs);
				}
				skipContents = true;
			}
			cb(skipContents);
		};
		parser.onFile = function(cb, abs, rel, name) {
			if(shouldRemove('file', rel, name)) {
				process.stdout.write('Removing file ' + rel + '\n');
				shell.rm('-f', abs);
				if(c5fs.fileExists(abs)) {
					throw new Error('Unable to remove ' + abs);
				}
			}
			cb();
		};
		parser.start(function(error) {
			if(error) {
				endForError(error);
				return;
			}
			var classmapFile = path.join(workFolder, 'concrete/vendor/composer/autoload_classmap.php');
			if(c5fs.isFile(classmapFile)) {
				process.stdout.write('Removing lines from Composer classmap:\n');
				var classmapLines = fs.readFileSync(classmapFile, 'utf8').replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n'),
					linesRemoved = 0;
				remove.dir.byPath.forEach(function(dir) {
					var m = dir.match(/^\/concrete\/vendor\/(.+)$/);
					if(!m) {
						return;
					}
					var removeLinesWith = '=> $vendorDir . \'/' + m[1] + '/';
					for(i = classmapLines.length - 1; i >= 0; i--) {
						if((classmapLines[i].indexOf(removeLinesWith) >= 0) && /^\s*'[^']+' => \$vendorDir . '/.test(classmapLines[i])) {
							process.stdout.write(classmapLines[i] + '\n');
							classmapLines.splice(i, 1);
							linesRemoved++;
						}
					}
				});
				if(linesRemoved > 0) {
					fs.writeFileSync(classmapFile, classmapLines.join('\n'), 'utf8');
				}
			}
			done();
		});
	}
	catch(e) {
		endForError(e);
		return;
	}
};