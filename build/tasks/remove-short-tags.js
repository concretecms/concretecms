module.exports = function(grunt, config, parameters, done) {
	var deleteFolderOnError = '', listFile = null;

	function endForError(e) {
		if(deleteFolderOnError.length) {
			try {
				shell.rm('-rf', deleteFolderOnError);
			}
			catch(foo) {
			}
			deleteFolderOnError = '';
		}
		if(listFile && listFile.fd) {
			try {
				fs.closeSync(listFile.fd);
				listFile.fd = null;
			}
			catch(foo) {
			}
		}
		process.stderr.write(e.message || e);
		done(false);
	}
	try {
		var c5fs = require('../libraries/fs'), path = require('path'), fs = require('fs'), shell = require('shelljs'), temp = require('temp');
		temp.track();
		var listFile = temp.openSync();
		var pkg = parameters['package'] || '';
		if(pkg === '-') {
			pkg = '';
		}
		// Check if web root is correct
		var webRoot = fs.realpathSync(config.DIR_BASE);
		if(!fs.lstatSync(webRoot).isDirectory()) {
			endForError('"' + config.DIR_BASE + '" is not a directory.');
			return;
		}
		// Determine source folder
		var sourceFolder;
		if(pkg) {
			sourceFolder = path.resolve(webRoot, 'packages', pkg);
			if(!fs.lstatSync(sourceFolder).isDirectory()) {
				endForError('"' + pkg + '" is not a valid package handle.');
				return;
			}
		}
		else {
			sourceFolder = webRoot;
		}

		if (parameters.source) {
			sourceFolder = path.resolve(__dirname, '..', parameters.source);
		}
		// Check destination folder
		var destinationFolder;
		if(parameters.destination) {
			destinationFolder = path.resolve(__dirname, '..', parameters.destination);
			if(destinationFolder == sourceFolder) {
				destinationFolder = '';
			}
			else if(sourceFolder.indexOf(destinationFolder + path.sep) === 0) {
				endForError('The destination folder can\'t contain the source folder.');
				return;
			}
			else {
				var existing;
				try {
					existing = fs.lstatSync(destinationFolder);
				}
				catch(e) {
					if(e.code !== 'ENOENT') {
						endForError(e);
						return;
					}
					existing = false;
				}
				if(existing) {
					endForError('The destination folder "' + destinationFolder + '" already exists.');
					return;
				}
			}
		}
		else {
			destinationFolder = '';
		}
		// Determine the script that will convert from short to long tags for each file.
		var spawn = require('child_process').spawn;
		// Let's start!
		var parser = new c5fs.directoryParser(sourceFolder);
		parser.excludeFilesByName.push('.gitignore');
		parser.excludeFilesByName.push('.git');
		parser.excludeDirectoriesByName.push('.git');
		if(destinationFolder) {
			// Copy all files and expand .php files, so that we'll have a full copy of the original directory
			c5fs.mkdirRecursiveSync(destinationFolder);
			deleteFolderOnError = destinationFolder;
			parser.onDirectory = function(cb, abs, rel) {
				try {
					fs.mkdirSync(path.join(destinationFolder, rel));
				}
				catch(err) {
					endForError(err);
					return;
				}
				cb();
			};
		}
		else {
			parser.onlyFilesWithExtension.push('.php');
		}
		parser.onFile = function(cb, abs, rel, name) {
			if(!/.\.php$/i.test(name)) {
				if(!destinationFolder) {
					cb();
					return;
				}
				process.stdout.write('Copying ' + rel + '... ');
				c5fs.copyFile(abs, path.join(destinationFolder, rel), function(err) {
					if(err) {
						endForError(err);
					}
					else {
						process.stdout.write('done.\n');
						cb();
					}
				});
				return;
			}
			var line = abs;
			if(destinationFolder) {
				line += "\t" + path.join(destinationFolder, rel);
			}
			fs.writeSync(listFile.fd, line + "\n");
			cb();
		};
		if(!destinationFolder) {
			process.stdout.write('Listing files... ');
		}
		parser.start(function(error) {
			if(error) {
				endForError(error);
			}
			else {
				if(!destinationFolder) {
					process.stdout.write('done.\n');
				}
				fs.closeSync(listFile.fd);
				listFile.fd = null;
				var removerError = null;
				try {
					var remover;
					if(parameters.shortTagRemover) {
						remover = spawn(parameters.shortTagRemover, ['--list', listFile.path]);
					}
					else {
						var args = ['-d', 'short_open_tag=On', path.join(__dirname, '../libraries/short-tags-remover.php')];
						if (parameters.keepShortEcho) {
							args.push('--keep-short-echo');
						}
						args = args.concat(['--list', listFile.path]);
						remover = spawn('php', args);
					}
					remover.stdout.on('data', function (data) {
						process.stdout.write(data);
					});
					remover.stderr.on('data', function (data) {
						process.stdout.write(data);
					});
					remover.on('error', function(err) {
						removerError = err;
						endForError(removerError);
					});
					remover.on('exit', function(code) {
						if(removerError !== null) {
							return;
						}
						if(code !== 0) {
							endForError('child process exited with code ' + code);							
							return;
						}
						done(true);
					});
				}
				catch(e) {
					endForError(e);
				}
			}
		});
	}
	catch(e) {
		endForError(e);
		return;
	}
};