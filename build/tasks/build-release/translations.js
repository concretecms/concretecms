module.exports = function(grunt, config, parameters, done) {
	var workFolder = parameters.releaseWorkFolder || './release/source';

	function endForError(e) {
		process.stderr.write(e.message || e);
		done(false);
	}
	try {
        var buildParameters = require('util')._extend({}, parameters);
        buildParameters = parameters;
        buildParameters.destination = workFolder;
        if(!parameters.txResource) {
        	parameters.txResource = 'core-dev-8';
        }
        require('../translations.js')(grunt, config, buildParameters, done);
	}
	catch(e) {
		endForError(e);
	}
};
