const fsUtil = require('./fs')
const fs = require('fs')
const path = require('path')
const child_process = require('child_process')

const DIR_GITROOT = path.join(fsUtil.WEBROOT, 'concrete')

if (process.argv.indexOf('-h') > 1 || process.argv.indexOf('--help') > 1 || process.argv.indexOf('/?') > 1) {
    exitWithSyntax(0)
}

setSkipWorktree(getSkippablePaths(), getSkipOption())

function exitWithSyntax(returnCode)
{
    console.log(`Syntax: ${fsUtil.escapeShellArg(process.argv[0])} ${fsUtil.escapeShellArg(process.argv[1])} <0|no|off|false|1|yes|on|true>`)
    process.exit(returnCode)
}

function getSkipOption()
{
    if (process.argv.length !== 3) {
        exitWithSyntax(1)
    }
    switch (process.argv[2].toLowerCase()) {
        case '0':
        case 'no':
        case 'off':
        case 'false':
            return false
        case '1':
        case 'yes':
        case 'on':
        case 'true':
            return true
    }
    exitWithSyntax(1)
}

function getSkippablePaths()
{
    return [
        'blocks/gallery/auto.js',
        'css/ckeditor/concrete.css',
        'css/cms.css',
        'css/features/basics/frontend.css',
        'css/features/boards/frontend.css',
        'css/features/calendar/frontend.css',
        'css/features/conversations/frontend.css',
        'css/features/documents/frontend.css',
        'css/features/express/frontend.css',
        'css/features/faq/frontend.css',
        'css/features/imagery/frontend.css',
        'css/features/maps/frontend.css',
        'css/features/multilingual/frontend.css',
        'css/features/navigation/frontend.css',
        'css/features/polls/frontend.css',
        'css/features/search/frontend.css',
        'css/features/social/frontend.css',
        'css/features/taxonomy/frontend.css',
        'css/features/testimonials/frontend.css',
        'css/features/video/frontend.css',
        'css/fontawesome/all.css',
        'css/fullcalendar.css',
        'images/icons/bedrock/sprites.svg',
        'js/ckeditor/concrete.js',
        'js/cms.js',
        'js/features/boards/frontend.js',
        'js/features/calendar/frontend.js',
        'js/features/conversations/frontend.js',
        'js/features/documents/frontend.js',
        'js/features/express/frontend.js',
        'js/features/imagery/frontend.js',
        'js/features/maps/frontend.js',
        'js/features/multilingual/frontend.js',
        'js/features/navigation/frontend.js',
        'js/fullcalendar.js',
        'js/jquery.js',
        'js/vue.js',
        'themes/concrete/main.css',
        'themes/concrete/main.js',
        'themes/dashboard/main.css',
        'themes/dashboard/main.js',
        'themes/elemental/main.js',
        'themes/elemental/main.css',
        'mix-manifest.json',
    ].concat(
        listCopiedFiles(path.join(fsUtil.WEBROOT, 'build/node_modules/@fortawesome/fontawesome-free/webfonts'), 'css/webfonts')
    ).concat(
        listCopiedFiles(path.join(fsUtil.WEBROOT, 'build/node_modules/ace-builds/src-min'), 'js/ace')
    ).concat(
        listCopiedFiles(path.join(fsUtil.WEBROOT, 'build/node_modules/ckeditor4'), 'js/ckeditor')
    )
}

function listCopiedFiles(sourceDir, relDestDir)
{
    const gitResult = child_process.spawnSync(
        'git',
        ['ls-files'],
        {
            cwd: path.join(DIR_GITROOT, relDestDir),
            stdio: 'pipe',
            shell: true,
            encoding: 'utf-8',
        }
    )
    if (gitResult.status !== 0) {
        if (gitResult.error) {
            throw gitResult.error
        }
        process.exit(1)
    }
    const gitFiles = gitResult.stdout.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n').filter(line => line.length > 0)
    const fsFiles = listAllFiles(path.join(DIR_GITROOT, relDestDir))
    const commonFiles = gitFiles.filter(file => fsFiles.indexOf(file) >= 0)
    return commonFiles.map(file => relDestDir + '/' + file)
}

function listAllFiles(path, callback)
{
    const result = []
    const walker = function(path, prefix, callback)
    {
        fs.readdirSync(
            path,
            {
                withFileTypes: true
            }
        ).forEach(entry => {
            if(entry.isDirectory()) {
                walker(path + '/' + entry.name, prefix + entry.name + '/', callback)
            } else {
                result.push(prefix + entry.name)
            }
        })
    }
    walker(path, '', callback)
    return result
}

function setSkipWorktree(paths, skip)
{
    const CHUNK_SIZE = 150
    for (let index = 0; index < paths.length; index += CHUNK_SIZE) {
        const args = [
            'update-index',
            '-q',
            skip ? '--skip-worktree' : '--no-skip-worktree',
        ]
        paths.slice(index, index + CHUNK_SIZE).forEach(path => {
            args.push(fsUtil.escapeShellArg(path))
        })
        const gitResult = child_process.spawnSync(
            'git',
            args,
            {
                cwd: DIR_GITROOT,
                stdio: 'inherit',
                shell: true,
            }
        )
        if (gitResult.status !== 0) {
            if (gitResult.error) {
                throw gitResult.error
            }
            process.exit(1)
        }
    }
}
