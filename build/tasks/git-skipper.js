/* jshint unused:vars, undef:true, node:true, esversion: 6 */

const path = require('path');
const exec = require('child_process').exec;


var fixPathSeparator;
if (path.sep === '/') {
    fixPathSeparator = function(x) { return x; };
} else {
    fixPathSeparator = function(x) { return x.replace(/\//g, path.sep); };
}

function runGitCommand(config, args, cb) {
    exec(
        'git ' + args,
        {
            cwd: config.DIR_BASE
        },
        function(error, stdout, stderr) {
            if(error) {
                throw new Error(stderr || error);
            }
            cb(stdout);
        }
    );
}

function listGeneratedAssetFiles(kind, config) {
    var files = [], key, key2, value;
    
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
    return files;
}
function filterTrackedFiles(config, files, cb) {
    if (files.length === 0) {
        cb([]);
    }
    runGitCommand(
        config,
        'status --untracked=all --ignored --porcelain ' + files.join(' '),
        function (status) {
            status.replace(/\r\n/g, '\n').split('\n').forEach(function(line) {
                line = line.replace(/\s+$/, '');
                var match = /^\?\?\s+(.*)/.exec(line);
                if (match !== null) {
                    let fileToRemove = match[1].replace(/\//g, path.sep);
                    files.splice(files.indexOf(fileToRemove), 1);
                }
            });
            cb(files);
        }
    );
}


module.exports = function(grunt, config, parameters, kind, setSkipped, done) {
    filterTrackedFiles(
        config,
        listGeneratedAssetFiles(kind, config),
        function (files) {
            if (files.length === 0) {
                process.stderr.write('No files found\n');
                done(false);
                return;
            }
            process.stdout.write(setSkipped ? ('Telling git to NOT CONSIDER built assets (' + kind + ')... ') : ('Telling git to CONSIDER built assets (' + kind + ')... '));
            runGitCommand(
                config,
                'update-index ' + (setSkipped ? '--assume-unchanged' : '--no-assume-unchanged') + ' ' + files.join(' '),
                function() {
                    process.stdout.write('\n');
                    done(true);
                }
            );
        }
    );
};
