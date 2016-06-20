/* jshint unused:vars, undef:true, node:true */

module.exports = function(grunt, config, parameters, kind, setSkipped, done) {
    var path = require('path'), exec = require('child_process').exec;
    
    var fixPathSeparator;
    if (path.sep === '/') {
        fixPathSeparator = function(x) { return x; };
    } else {
        fixPathSeparator = function(x) {
            return x.replace(/\//g, path.sep);
        };
    }
    var files = [], key, value, key2;
    
    if ((kind === 'css' || kind === 'all') && config && config.less && config.less.release && config.less.release.files) {
        for (key in config.less.release.files) {
            if (config.less.release.files.hasOwnProperty(key)) {
                if (typeof key === 'string' && key.indexOf('<%= DIR_BASE %>/') === 0) {
                    files.push(fixPathSeparator(key.substr('<%= DIR_BASE %>/'.length)));
                }
            }
        }
    }
    if ((kind === 'js' || kind === 'all') && config && config.uglify) {
        for (key in config.uglify) {
            if (config.uglify.hasOwnProperty(key) && typeof key === 'string' && key.length > '_release'.length && key.substr(-'_release'.length) === '_release') {
                value = config.uglify[key];
                if (value.files) {
                    for (key2 in value.files) {
                        if (value.files.hasOwnProperty(key2)) {
                            if (typeof key2 === 'string' && key2.indexOf('<%= DIR_BASE %>/') === 0) {
                                files.push(fixPathSeparator(key2.substr('<%= DIR_BASE %>/'.length)));
                            }
                        }
                    }
                }
            }
        }
    }
    if (files.length === 0) {
        process.stderr.write('No files found\n');
        done(false);
        return;
    }
    console.log(path.join(__dirname, '..', '..'));
    process.stdout.write(setSkipped ? ('Telling git to NOT CONSIDER built assets (' + kind + ')... ') : ('Telling git to CONSIDER built assets (' + kind + ')... '));
    var cmd = 'git update-index ' + (setSkipped ? '--assume-unchanged' : '--no-assume-unchanged') + ' ' + files.join(' ');
    exec(
        cmd,
        {
            cwd: path.join(__dirname, '..', '..')
        },
        function(error, stdout, stderr) {
            if(error) {
                process.stderr.write(stderr || error);
            }
            else {
                process.stdout.write('ok');
            }
            process.stdout.write('\n');
            done(!error);
        }
    );
};
