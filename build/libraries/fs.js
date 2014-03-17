var fs = require('fs');
var path = require('path');

/** Escape a parameter so that it's safe to use it with terminal scripts.
* @function
* @param {string} arg The argument to be escaped.
* @return {string}
*/
var escapeShellArg;
if (process.platform === 'win32') {
	escapeShellArg = function(arg) {
		// If arg contains spaces or some special character...
		if (/["<>\s\|]/.test(arg)) {
			// Enclose arg in double quotes and escape double quotes.
			return '"' + arg.replace(/(")/g, '"$1') + '"';
		}
		return arg;
	};
}
else {
	escapeShellArg = function(arg) {
		// If arg contains spaces or some special character...
		if (/[!"#$&'()*<>\s\\\|`{}]/.test(arg)) {
			// Enclose arg in single quotes and escape single quotes.
			return "'" + arg.replace(/(')/g, '\\$1') + "'";
		}
		return arg;
	};
}

/** Checks whether a directory exists
* @param {string} path
* @return boolean
*/ 
function isDirectory(path) {
	try {
		return fs.lstatSync(path).isDirectory();
	}
	catch(e) {
		if(e.code == 'ENOENT') {
			return false;
		}
		throw e;
	}
}

/** Checks whether a file exists
* @param {string} path
* @return boolean
*/ 
function isFile(path) {
	try {
		return fs.lstatSync(path).isFile();
	}
	catch(e) {
		if(e.code == 'ENOENT') {
			return false;
		}
		throw e;
	}
}

/** Checks whether a file or directory exists
* @param {string} path
* @return boolean
*/ 
function fileExists(path) {
	try {
		fs.lstatSync(path);
		return true;
	}
	catch(e) {
		if(e.code == 'ENOENT') {
			return false;
		}
		throw e;
	}
}

/** Helper function that effectively parses the directory tree (used by directoryParser).
* @param {directoryParser} main The directoryParser instance.
* @param {string} dirAbs The absolute path of the directory to parse.
* @param {string} dirRel The relative path of the directory to parse.
* @param {function} callback The function to call when dirAbs (and optionally its sub-directories) have been parsed.
*/
function parseDirectory(main, dirAbs, dirRel, callback) {
	fs.readdir(dirAbs, function(error, subNames) {
		var subFiles = [], subDirs = [];
		if(!error) {
			try {
				subNames.forEach(function(subName) {
					var subAbs = path.join(dirAbs, subName);
					var subRel = dirRel + '/' + subName;
					var stats = fs.lstatSync(subAbs);
					if(!stats.isSymbolicLink()) {
						if(stats.isDirectory()) {
							if(
								main.excludeDirectoriesByPath.indexOf(subRel) < 0
								&&
								main.excludeDirectoriesByName.indexOf(subName) < 0
								&&
								(!main.onlyDirectoriesWithPath.length || main.onlyDirectoriesWithPath.indexOf(subRel) >= 0)
							) {
								subDirs.push({abs: subAbs, rel: subRel, name: subName});
							}
						}
						else if(stats.isFile()) {
							var ext = path.extname(subName).toLowerCase();
							if(
								main.excludeFilesByExtension.indexOf(ext) < 0
								&&
								main.excludeFilesByName.indexOf(subName) < 0
								&&
								(!main.onlyFilesWithExtension.length || main.onlyFilesWithExtension.indexOf(ext) >= 0)
							) {
								subFiles.push({abs: subAbs, rel: subRel, name: subName});
							}
						}
					}
				});
			}
			catch(e) {
				error = e;
			}
		}
		if(error) {
			if(main.onDone) {
				main.onDone(error);
			}
			return;
		}
		function workOnNextFile(index) {
			if(index >= subFiles.length) {
				if(main.onDirectory) {
					workOnNextDir(0);
				}
				else {
					allWorkedOn();
				}
			}
			else {
				main.onFile(
					function() {
						workOnNextFile(index + 1);
					},
					subFiles[index].abs,
					subFiles[index].rel,
					subFiles[index].name
				);
			}
		}
		function workOnNextDir(index) {
			if(index >= subDirs.length) {
				allWorkedOn();
			}
			else {
				main.onDirectory(
					function() {
						workOnNextDir(index + 1);
					},
					subDirs[index].abs,
					subDirs[index].rel,
					subDirs[index].name
				);
			}
		}
		function allWorkedOn() {
			if(!main.recursive) {
				callback();
				return;
			}
			function parseNextDir(index) {
				if(index == subDirs.length) {
					callback();
					return;
				}
				parseDirectory(
					main,
					subDirs[index].abs,
					subDirs[index].rel,
					function() {
						parseNextDir(index + 1);
					}
				);
			}
			parseNextDir(0);
		}
		if(main.onFile) {
			workOnNextFile(0);
		}
		else if(main.onDirectory) {
			workOnNextDir(0);
		}
		else {
			allWorkedOn();
		}
	});
}

/** Create an instance of directoryParser.
* @param {string} dir The directory to parse.
* @constructor
* @this {directoryParser}
* @property {boolean} recursive Set to true to parse sub-directories [default: true].
* @property {function} onDirectory A function to be called when sub-directories are found.
*	It receives the following parameters:
*		callback: the function to be called to going on with the directory parsing.
*		abs: the absolute path of the found directory (for instance: '/etc/apache2/sites-available/default/concrete')
*		rel: the relative path of the found directory (for instance: '/concrete')
*		name: the directory name (for instance: 'concrete')
* @property {function} onFile A function to be called when files are found.
*	It receives the following parameters:
*		callback: the function to be called to going on with the directory parsing.
*		abs: the absolute path of the found file (for instance: '/etc/apache2/sites-available/default/concrete/dispatcher.php')
*		rel: the relative path of the found file (for instance: '/concrete/dispatcher.php')
*		name: the directory name (for instance: 'dispatcher.php')
* @property {Array} excludeDirectoriesByPath A list of directories to exclude.
*	They must be specified as relative to the main directory (for instance: '/files').
* @property {Array} excludeDirectoriesByName A list of directories to exclude.
*	They must be specified as names (default value: '__MACOSX').
* @property {Array} onlyDirectoriesWithPath A list of directories to limit the processing on.
*	They must be specified as relative to the main directory (for instance: '/files').
* @property {Array} excludeFilesByName A list of file to be excluded, in every folder.
*	They can be specified as names (default: ['.DS_Store', 'thumbs.db']).
* @property {Array} excludeFilesByExtension A list of file extensions to exclude.
*	They must be lower case and start with dot (for instance: '.php').
* @property {Array} onlyFilesWithExtension A list of file extensions to limit the processing on.
*	They must be lower case and start with dot (for instance: '.php').
*/
function directoryParser(dir) {
	this.dir = path.resolve(dir);
	this.recursive = true;
	this.onDirectory = null;
	this.onFile = null;
	this.excludeDirectoriesByPath = [];
	this.excludeDirectoriesByName = ['__MACOSX'];
	this.onlyDirectoriesWithPath = [];
	this.excludeFilesByName = ['.DS_Store', 'thumbs.db'];
	this.excludeFilesByExtension = [];
	this.onlyFilesWithExtension = [];
}
/** Start the processing of the directory.
* @param {function} onDone A function to be called when the execution ends. In case of errors it receives one parameter ('error') describing the exception.
* @this {directoryParser}
*/
directoryParser.prototype.start = function(onDone) {
	this.onDone = onDone || null;
	parseDirectory(
		this,
		this.dir,
		'',
		this.onDone || function() {}
	);
};

/** Synchronously create a directory and its ancestors.
* @param {string} dir The directory to create
* @param {number} mode Defaults to 0777
* @throws Throws an exception in case of errors
*/
function mkdirRecursiveSync(dir, mode) {
	try {
		fs.mkdirSync(dir, mode);
	}
	catch(e) {
		if(e.errno !== 34) {
			throw e;
		}
		mkdirRecursiveSync(path.dirname(dir), mode);
		mkdirRecursiveSync(dir, mode);
	}
}

/** Synchronously delete a directory and all its content.
* @param {string} dir The directory to delete
* @throws Throws an exception in case of errors
*/
function rmdirRecursiveSync(dir) {
	fs.readdirSync(dir).forEach(function(item) {
		var full = dir + path.sep + item;
		if(fs.lstatSync(full).isDirectory()) {
			rmdirRecursiveSync(full);
		}
		else {
			fs.unlinkSync(full);
		}
	});
	fs.rmdirSync(dir);
}

/** Copy a file asynchronously.
* @param {string} from The source file
* @param {string} to The destination file (must not exist)
* @param {function} callback A function to call when operation completes. In case of errors it receives the error as first parameter.
*/
function copyFile(from, to, callback) {
	fs.stat(to, function(err) {
		if(!err) {
			if(callback) {
				callback(new Error('File ' + to + ' already exists.'));
			}
			return;
		}
		fs.stat(from, function(err, stat) {
			if(err) {
				if(callback) {
					callback(err);
				}
				return;
			}
			var fromStream = fs.createReadStream(from);
			var toStream = fs.createWriteStream(to);
			fromStream.pipe(toStream);
			toStream.on('close', function(err) {
				if(err) {
					try {
						fs.unlinkSync(to);
					}
					catch(foo) {
					}
					if(callback) {
						callback(err);
					}
					return;
				}
				fs.utimes(to, stat.atime, stat.mtime, function(err) {
					if(callback) {
						if(err) {
							callback(err);
						}
						else {
							callback();
						}
					}
				});
			});
		});
	});
}

exports.mkdirRecursiveSync = mkdirRecursiveSync;
exports.rmdirRecursiveSync = rmdirRecursiveSync;
exports.escapeShellArg = escapeShellArg;
exports.directoryParser = directoryParser;
exports.copyFile = copyFile;
