module.exports = function(grunt, config, parameters, done) {

	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}

	try {
		var shell = require('shelljs'),
			fs = require('fs'),
			download = require('download'),
			path = "./release";

		shell.rm('-rf', path);
		fs.mkdir(path);

		// Download archive from git
		process.stdout.write("Downloading Archive...\n");
		var stream = download('https://github.com/concrete5/concrete5/archive/master.zip', path, {
			extract: true
		});
		stream.on('close', function() {
			done();
		});

	}
	catch(e) {
		endForError(e);
		return;
	}
};