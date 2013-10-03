// This is an sample configuration file.
// Set your values and save this file as Gruntfile.c5config.js

module.exports = {
	// Your Transifex login
	txUsername: '',
	// Your Transifex password
	txPassword: '',
	// The resource identifier of the Transifex resource, for instance 'core-5621'
	txResource: '',
	// A list of locales. If empty then all the Transifex locales are retrieved.
	txLocales: [],
	// The minimum translation progress of locales (percentage). If not specified we'll use 95%
	txProgressLimit: 95
};
