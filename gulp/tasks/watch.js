/* global require */
var gulp = require('gulp');
var gutil = require('gulp-util');
var sourcemaps = require('gulp-sourcemaps');
var browserify = require('../browserify.js');
var watchify = require('watchify');
var vendorlist = require('../vendorlist.js');
var plumber = require('gulp-plumber');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var crypto = require('crypto');
var rename = require('gulp-rename');
var notifier = require('node-notifier');

// Rerun the task when a file changes
gulp.task('watch', function() {
    gulp.watch([
        'scss/**/*.scss'
        ], ['scss']);
//    gulp.watch([
//        'js/**/*.js'
//        ], ['js']);
    gulp.watch([
        'public/_js/index.js'
        ], ['buildinfo']);
    //browserify.plugin(watchify);
    //var watcher = watchify(browserify);
    var streams = ['./js/index.js'].map(function(filename) {
        var bundlerInstance = browserify({
            entries: filename,
            debug: true
        })
            .external(vendorlist)
            .plugin(watchify);
        var createJs = function () {
            gutil.log("[browserify] Updating JS");
            bundlerInstance
                .bundle()
                .on('error', function (message) {
                    gutil.log('[browserify] ' +  gutil.colors.red(message));
                    notifier.notify({
                        "title": "zmsbot-Build-Error",
                        "message" : "Error: " + message
                    });
                })
                .pipe(source(filename))
                .pipe(buffer())
                .pipe(plumber())
                .pipe(sourcemaps.init({
                    'loadMaps': true,
                    //'identityMap': true,
                    'largeFile': true
                }))
                .pipe(plumber())
                .pipe(sourcemaps.write('./', {
                    sourceMappingURL: function (file) {
                        gutil.log('[sourcemaps] Rewrite path ' +  gutil.colors.green(file.relative));
                        // Avoid caching of source
                        //return file.relative + '.map?build=' + crypto.createHash('sha1').update(file.contents).digest('hex');
                        return file.relative + '.map?build=' + crypto.randomBytes(20).toString('hex');
                    }
                }))
                .on('end', function() {gutil.log(gutil.colors.magenta("ATTENTION: Fast build, remember to do a full build before commit!"))})
                .pipe(rename({dirname:''}))
                .pipe(gulp.dest('./public/_js/'));

        }
        bundlerInstance.on('update', createJs);
        createJs();
    })
    return streams;
});

