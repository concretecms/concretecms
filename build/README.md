# Building Assets Required for concrete5


## Requirements
In order to build assets for concrete5 you need:
- [Node.js](http://nodejs.org/)
- [npm](http://npmjs.org/) (may be bundled with Node.js)

Once you have node.js and npm, you have to install the [grunt](http://gruntjs.com/) client.
You can install it globally with `npm install -g grunt-cli`. This requires that you may need to use sudo (for OS X, *nix, BSD, …) or run your command shell as Administrator (for Windows).
If you don't have administrator rights, you may need to install the grunt client locally to your project using `npm install grunt-cli`.
Unfortunately, this will not put the grunt executable in your PATH, so you'll need to specify its explicit location when executing it (for OS X, *nix, BSD `node_modules/.bin/grunt`, for Windows `node_modules\.bin\grunt`).

Once you have installed the grunt client, you need to install the project dependencies: simply launch the following command: `npm install` from inside the `build` directory.


## Building .css files

- For debugging: `grunt css:debug`
	- The generated .css files are more readable, but they have a slightly bigger size
- For production: `grunt css:release` (or simply `grunt css`)
	-  The generated .css files are slightly smaller, but they are harder to read from a human point of view


## Building .js files

- For debugging: `grunt js:debug`
	- Building for debug will generate JavaScript SourceMap files, that greatly improve the debugging of scripts with modern browsers.
- For production: `grunt js:release` (or simply `grunt js`)
	- SourceMap files won't be generated.  


## Building everything

- For debugging: `grunt debug`
	- it's the same as calling `grunt css:debug` and `grunt js:debug`
- For production: `grunt release` (or simply `grunt`)
	- it's the same as calling `grunt css:release` and `grunt js:release`


## Debugging JavaScript with source maps

If you have installed concrete5 in a sub-directory and you want to debug JavaScript with SourceMaps, you should update the `Gruntfile.js` file, changing the following line:
`config.DIR_REL = '';`
For instance, if your concrete5 installation is at http://www.domain.com/c5subfolder, you should change it to:
`config.DIR_REL = '/c5subfolder';`
