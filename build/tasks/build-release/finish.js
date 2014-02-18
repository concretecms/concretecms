module.exports = function(grunt, config, parameters, done) {

	function endForError(e) {
		process.stderr.write(e.message || e);
	}

	try {
		var fs = require('fs'),
			rimraf = require('rimraf');

		var data = fs.readFileSync('./release/concrete5-master/web/concrete/config/version.php', 'utf8');
		var version = data.match(/\$APP_VERSION = '(.*)'/);
		if (version[1]) {
			var directory = 'concrete' + version[1];
			var execSync = require('exec-sync');
			fs.renameSync('./release/concrete5-master/web', './release/' + directory);
			execSync('pushd release/; zip -r ' + directory + '.zip ' + directory + '; popd');
			rimraf.sync('./release/' + directory);
			rimraf.sync('./release/concrete5-master');
			done();
		}
	}
	catch(e) {
		endForError(e);
		return;
	}
};