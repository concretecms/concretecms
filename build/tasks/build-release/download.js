module.exports = function(grunt, config, parameters, done) {
	var zipUrl = parameters.releaseSourceZip || 'https://github.com/concrete5/concrete5/archive/develop.zip';
	var workFolder = parameters.releaseWorkFolder || './release';
	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}
	try {
		var fs = require('fs'),
			path = require('path')
			Download = require('download'),
			c5fs = require('../../libraries/fs'),
			shell = require('shelljs');
		if(c5fs.isDirectory(workFolder)) {
			process.stdout.write('Removing working folder... ');
			shell.rm('-rf', workFolder);
			if(c5fs.isDirectory(workFolder)) {
				throw new Error('Unable to remove ' + workFolder);
			}
			process.stdout.write('done.\n')
		}
		fs.mkdir(workFolder);
		process.stdout.write('Downloading & unzipping archive... ');
		var download = new Download({extract: true})
			.get(zipUrl)
			.dest(workFolder);
		download.run(function(err) {
			if (err) {
				endForError(err);
				return;
			}
			process.stdout.write('done.\n');
			var extractedFolder = null;
			fs.readdirSync(workFolder).forEach(function(item) {
				if(item.indexOf('.') !== 0) {
					if(extractedFolder === null) {
						extractedFolder = item;
					}
					else {
						throw new Error('Multiple items in the root of the extract archive!');
					}
				}
			});
			if(extractedFolder === null) {
				throw new Error('No items extracted!');
			}
			fs.renameSync(path.join(workFolder, extractedFolder), path.join(workFolder, 'source'));
			done();
		});
	}
	catch(e) {
		endForError(e);
		return;
	}
};