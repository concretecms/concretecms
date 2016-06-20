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
				'.travis.install.sh',
				'.travis.yml',
				'appveyor.yml',
				'build-phar.sh',
				'config.inc.php.SAMPLE',
			],
			byPath: [
				'/concrete/vendor/dapphp/securimage/example_form.ajax.php',
				'/concrete/vendor/dapphp/securimage/example_form.php',
				'/concrete/vendor/htmlawed/htmlawed/htmLawedTest.php',
				'/concrete/vendor/imagine/imagine/lib/Imagine/resources/Adobe/Color Profile Bundling License_10.15.08.pdf',
				'/concrete/vendor/imagine/imagine/lib/Imagine/resources/Adobe/Profile Information.pdf',
				'/concrete/vendor/michelf/php-markdown/Readme.php',
				'/concrete/vendor/ocramius/proxy-manager/index.html',
				'/concrete/vendor/ocramius/proxy-manager/proxy-manager.png',
				'/concrete/vendor/ocramius/proxy-manager/proxy-manager.svg',
				'/concrete/vendor/punic/punic/punic.php',
				'/concrete/vendor/tedivm/stash/autoload.php',
			],
			byRX: [
				/^\/concrete\/vendor\/.*\/(changelog|change_log|changes|upgrade|upgrading|readme|contributing|history|roadmap|security|stability|install)(-\d+(\.\d+)*)?((\.(font|src))?\.(md|mdown|markdown|txt))?$/i,
				/^\/concrete\/vendor\/.*\/composer\.(lock|json)(\.hhvm)?$/,
				/^\/concrete\/vendor\/.*\/(phpunit|phpdox|phpmd)(\.(xml|dist|travis|hhvm))+$/,
				/^\/concrete\/vendor\/.*\/build\.(xml|properties)(\.dist)?$/,
				/^\/concrete\/vendor\/.*\/UPGRADE_TO_\w+$/,
				/^\/concrete\/vendor\/htmlawed\/htmlawed\/htmLawed_(README|TESTCASE)/,
			]
		},
		
		dir: {
			byName: [
				'.easymin',
				'.git',
			],
			byPath: [
				'/concrete/vendor/anahkiasen/html-object/examples',
				'/concrete/vendor/bin',
				'/concrete/vendor/dapphp/securimage/examples',
				'/concrete/vendor/doctrine/cache/tests',
				'/concrete/vendor/doctrine/collections/tests',
				'/concrete/vendor/doctrine/common/lib/vendor',
				'/concrete/vendor/doctrine/dbal/bin',
				'/concrete/vendor/doctrine/dbal/lib/vendor',
				'/concrete/vendor/doctrine/inflector/tests',
				'/concrete/vendor/doctrine/instantiator/tests',
				'/concrete/vendor/doctrine/migrations/bin',
				'/concrete/vendor/doctrine/migrations/tests',
				'/concrete/vendor/doctrine/orm/bin',
				'/concrete/vendor/doctrine/orm/docs',
				'/concrete/vendor/doctrine/orm/lib/vendor',
				'/concrete/vendor/egulias/email-validator/documentation',
				'/concrete/vendor/egulias/email-validator/tests',
				'/concrete/vendor/gettext/languages/bin',
				'/concrete/vendor/hautelook/phpass/lib',
				'/concrete/vendor/hautelook/phpass/Tests',
				'/concrete/vendor/lusitanian/oauth/examples',
				'/concrete/vendor/lusitanian/oauth/tests',
				'/concrete/vendor/mobiledetect/mobiledetectlib/examples',
				'/concrete/vendor/mobiledetect/mobiledetectlib/tests',
				'/concrete/vendor/monolog/monolog/doc',
				'/concrete/vendor/monolog/monolog/tests',
				'/concrete/vendor/ocramius/proxy-manager/docs',
				'/concrete/vendor/ocramius/proxy-manager/examples',
				'/concrete/vendor/ocramius/proxy-manager/html-docs',
				'/concrete/vendor/ocramius/proxy-manager/tests',
				'/concrete/vendor/oryzone/oauth-user-data/tests',
				'/concrete/vendor/oyejorge/less.php/bin',
				'/concrete/vendor/pagerfanta/pagerfanta/tests',
				'/concrete/vendor/psr/log/Psr/Log/Test',
				'/concrete/vendor/symfony/class-loader/Tests',
				'/concrete/vendor/symfony/console/Tests',
				'/concrete/vendor/symfony/debug/Tests',
				'/concrete/vendor/symfony/event-dispatcher/Tests',
				'/concrete/vendor/symfony/finder/Symfony/Component/Finder/Tests',
				'/concrete/vendor/symfony/http-foundation/Tests',
				'/concrete/vendor/symfony/http-kernel/Tests',
				'/concrete/vendor/symfony/routing/Tests',
				'/concrete/vendor/symfony/serializer/Tests',
				'/concrete/vendor/symfony/translation/Tests',
				'/concrete/vendor/symfony/yaml/Tests',
				'/concrete/vendor/tedivm/jshrink/tests',
				'/concrete/vendor/tedivm/stash/tests',
				'/concrete/vendor/true/punycode/tests',
				'/concrete/vendor/voku/urlify/tests',
				'/concrete/vendor/zendframework/zend-queue/tests',
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