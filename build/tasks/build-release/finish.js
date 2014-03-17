module.exports = function(grunt, config, parameters, done) {

	function endForError(e) {
		process.stderr.write(e.message || e);
	}

	try {
		var fs = require('fs'),
			shell = require('shelljs');
		process.stdout.write('Determining concrete5 version... ');
		var data = fs.readFileSync('./release/concrete5-master/web/concrete/config/version.php', 'utf8'),
			version = data.match(/\$APP_VERSION = '(.*)'/)
		;
		if (!version) {
			endForError('$APP_VERSION not found');
			return;
		}
		version = version[1];
		process.stdout.write(version + '\n');
		var directory = 'concrete' + version;
		fs.renameSync('./release/concrete5-master/web', './release/' + directory);
		shell.pushd('release');
		process.stdout.write('Creating zip file... ');
		shell.exec('zip -r ' + directory + '.zip ' + directory, {silent: true, async: true}, function(code, output) {
			shell.popd();
			if(code !== 0) {
				endForError(output);
				return;
			}
			process.stdout.write('done.\n');
			shell.rm('-rf', './release/' + directory);
			shell.rm('-rf', './release/concrete5-master');
			done();
		});
	}
	catch(e) {
		endForError(e);
		return;
	}
};