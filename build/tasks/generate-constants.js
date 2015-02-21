module.exports = function(grunt, config, parameters, done) {
	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}
	try {
		var fs = require('fs'), c5fs = require('../libraries/fs'), exec = require('child_process').exec;
		var webRoot = fs.realpathSync(config.DIR_BASE);
		exec(
			'php -d short_open_tag=On ' + c5fs.escapeShellArg(__dirname + '/../libraries/generate-constants.php') + ' ' + c5fs.escapeShellArg(webRoot),
			{},
			function(error, stdout, stderr) {
				if(error) {
					var errorMessage = error.message || error;
					if(stderr) {
						errorMessage += "\n" + stderr;
					}
					else {
						errorMessage += "\n" + stdout;
					}
					endForError(errorMessage);
				}
				else {
					process.stdout.write('done.\n');
					done(true);
				}
			}
		);
	}
	catch(e) {
		endForError(e);
	}
}
