// This is an sample configuration file.
// Set your values and save this file as Gruntfile.parameters.js
module.exports = {

	// If concrete5 is installed in a subfolder and if you want to use javascript sourcemap you should change this variable.
	// For instance if your concrete5 installation is at http://www.domain.com/c5subfolder, you should change it to:
	// config.DIR_REL = '/c5subfolder';
	DIR_REL: '',
	// The path to the web folder, relative to this Gruntfile
	DIR_BASE: '../',
	// The path to the cli folder, relative to this Gruntfile
	DIR_CLI: '../concrete/vendor/bin',

	// Your Transifex login.
	// It's needed only if you have to run tasks that interact with Transifex.
	txUsername: '',
	// Your Transifex password.
	// It's needed only if you have to run tasks that interact with Transifex.
	txPassword: '',
	// The identifier of the Transifex resource, for instance 'core-5621'.
	// It's needed only if you have to run tasks that interact with Transifex.
	txResource: '',
	// A list of locales. If empty then all the Transifex locales are retrieved.
	txLocales: [],
	// The minimum translation progress of locales (percentage).
	// Default value: 90%
	txProgressLimit: 90

};
