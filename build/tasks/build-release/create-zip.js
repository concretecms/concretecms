module.exports = function(grunt, config, parameters, done) {

	var workFolder = parameters.releaseWorkFolder || './release/source', revert = null;

	function endForError(e) {
		if(revert) {
			try {
				revert();
			}
			catch(foo) {
			}
		}
		process.stderr.write(e.message || e);
		done(false);
	}

	try {
		var fs = require('fs'),
			path = require('path'),
			shell = require('shelljs'),
			c5fs = require('../../libraries/fs');
		process.stdout.write('Determining concrete5 version... ');
		var str = fs.readFileSync(path.join(workFolder, 'concrete/config/concrete.php'), 'utf8');
		// Remove comments and new lines
		str = str.replace(/\/\/.*?($|\r|\n)/g, '').replace(/[\r\n]/g, ' ').replace(/\/\*.*?\*\//g, '');
		var version = str.match(/["']version["']\s*=>\s*['"](.*?)['"]/);
		if (!version) {
			endForError('$APP_VERSION not found');
			return;
		}
		version = version[1];
		process.stdout.write(version + '\n');
		var dirname = 'concrete5-' + version;
		var workFolderParent = path.join(workFolder, '..');
		var folderToZip = path.join(workFolderParent, dirname);
		fs.renameSync(workFolder, folderToZip);
		revert = function() {
			fs.renameSync(folderToZip, workFolder);
		}
		shell.pushd(workFolderParent);
		process.stdout.write('Creating zip file... ');
		shell.exec(
			'zip -r ' + c5fs.escapeShellArg(dirname + '.zip') + ' ' + c5fs.escapeShellArg(dirname),
			{
				silent: true,
				async: true
			},
			function(code, output) {
				shell.popd();
				if(code !== 0) {
					endForError(output);
					return;
				}
				process.stdout.write('done.\n');
				fs.renameSync(path.join(workFolderParent, dirname + '.zip'), './' + dirname + '.zip');
				revert();
				done();
			}
		);
	}
	catch(e) {
		endForError(e);
		return;
	}
};
