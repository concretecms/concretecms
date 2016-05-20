module.exports = function(grunt, config, parameters, done) {
	var workFolder = parameters.releaseWorkFolder || './release/source';
	function endForError(error) {
		process.stderr.write(error.message || error);
		done(false);
	}
	try {
		var path = require('path'),
			exec = require('child_process').exec,
			fs = require('fs');
		process.stdout.write('Installng node.js modules with npm... ');
		exec(
			'npm install',
			{
				cwd: path.join(workFolder, 'build')
			},
			function(error, stdout, stderr) {
				if(error) {
					endForError(stderr || error);
					return;
				}
				process.stdout.write('done.\n');
				process.stdout.write('Building release components with Grunt... ');
				exec(
					'grunt release',
					{
						cwd: path.join(workFolder, 'build')
					},
					function(error, stdout, stderr) {
						if(error) {
							endForError(stderr || error);
							return;
						}
						process.stdout.write('done.\n');
						process.stdout.write('Installng PHP dependencies with Composer... ');
						exec(
							'composer install --no-dev',
							{
								cwd: path.join(workFolder, 'concrete')
							},
							function(error, stdout, stderr) {
								if(error) {
									endForError(stderr || error);
									return;
								}
								process.stdout.write('done.\n');
								done();
							}
						);
					}
				);
			}
		);
	}
	catch(e) {
		endForError(e);
		return;
	}
};
