module.exports = function(grunt, config, parameters, done) {

	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}

	try {
		var fs = require('fs');
			path = "./release/concrete5-master/web",
			cleanFiles = [".DS_Store", ".git", ".gitignore"];

		walk = function(directory, done) {
			fs.readdir(directory, function(err, list) {
				list.forEach(function(file) {
					var item = directory + '/' + file;
					fs.stat(item, function(err, stat) {
						if (stat && stat.isDirectory()) {
							walk(item);
						} else {
							if (cleanFiles.indexOf(file) > -1) {
								process.stdout.write('Deleting File: ' + item + "\n");
								fs.unlink(item);
							}
						}
					});
				});
			});
		}

		walk(path);
	}
	catch(e) {
		endForError(e);
		return;
	}
};