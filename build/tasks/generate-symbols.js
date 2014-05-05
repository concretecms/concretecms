module.exports = function(grunt, config, parameters, done) {
	shell = require('shelljs').exec([config.DIR_CLI, '/generate-symbols.php'].join(''));
}
