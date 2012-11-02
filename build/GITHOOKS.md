

# Concrete5 Build
If you're new here and looking to build concrete5, you're probably looking in the wrong place. This directory is only used when developing the concrete5 core.

## Dependancies:
In order to build concrete5's css and js libraries, you'll need a couple binaries:

1. **Lessc**: Lessc is the original OEM less compiling binary, you can grab it at [lesscss.org](http://lesscss.org) or install it in some fancy way with npm or potentially your OS's repos. The npm install is pretty simple; just run `npm install -g less`.
2. **UglifyJS**: UglifyJS is a JavaScript obfuscater / compressor / beautifier (what?) We use it to compress and obfuscate our JavaScript so to prime it for sending over `http`. Installing uglifyjs is much the same as the lessc installation, simply substitute "uglify-js" where you would put lessc (probably): `npm install uglify-js`

## Git Hooks
In the concrete5 workflow, we compile when you run `git commit`, we also run some fancy logic that makes sure that the files have been altered before compiling so to keep it speedyish.

In order to get this working, you'll need to do something like this:

    ln -s /build/hook .git/hooks/pre-commit

Now, everytime you commit that script will run. This will compile the css and js that require compiling prior to saving your commit (that way, it will be added to your commit you're committing (Fancy right?)).

## Manual Compiling
If that's too fancy for you, we still suppport the previous method of compiling, just run `./build/js` and `./build/css` to compile your changes.
If you'd like to ignore the git status and just compile the hell out of everything, just add a parameter; doesn't matter what ... just add one:

    ./build/js DONTWORRYABOUTGIT

or

    ./build/css duuurrr

etc.

If that's still way too fancy, the original build scripts are intact and working, just run `./build/js.sh` or `./build/css.sh`