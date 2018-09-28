// This is an sample configuration file.
// Set your values and save this file as Gruntfile.parameters.js
module.exports = {

	// If concrete5 is installed in a subfolder and if you want to use javascript sourcemap you should change this variable.
	// For instance if your concrete5 installation is at http://www.domain.com/c5subfolder, you should change it to:
	// config.DIR_REL = '/c5subfolder';
	DIR_REL: '',
	// The path to the web folder, relative to this Gruntfile
	DIR_BASE: '..',
	// The path to the cli folder, relative to this Gruntfile
	DIR_CLI: '../cli',

	// The entry point URI of the API of the Community Translation server containing the translations.
	// It's needed only if you have to run tasks that interact with a Community Translation server.
	ctEntryPoint: 'https://translate.concrete5.org/api',
	// The API Token to be used to authenticate yourself on a Community Translation server (may not be required).
	// It's needed only if you have to run tasks that interact with a Community Translation server.
	ctAPIToken: '',
	// The handle of the package on the Community Translation server.
	// It's needed only if you have to run tasks that interact with Transifex.
	ctPackage: 'concrete5',
	// The version of the package on the Community Translation server
    // It's needed only if you have to run tasks that interact with Transifex.
	ctPackageVersion: 'dev-8',
	// A list of locales. If empty then all the Community Translation locales are retrieved.
	ctLocales: [],
	// The minimum translation progress of locales (percentage).
	// Default value: 90%
	ctProgressLimit: 90,

	// Keep the short echo tags? Otherwise they'll be expanded [default: true]
	keepShortEcho: true

};
