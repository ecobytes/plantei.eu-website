'use strict';

var path = require('path');
var http = require('http');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var watchify = require('watchify');
var exorcist = require('exorcist');
var del = require('del');
var ecstatic = require('ecstatic');
var gulp = require('gulp');
var livereload = require('gulp-livereload');
var gulpif = require('gulp-if');
var notify = require('gulp-notify');

var watch;
var debug = true;


gulp.task('browserify-nowatch', function() {
  watch = false;
  browserifyBootstrapBundle();
});

gulp.task('browserify-watch', function() {
  watch = true;
  browserifyBootstrapBundle();
});

gulp.task('watch', ['browserify-watch'], function() {
  http.createServer(ecstatic({
    root: path.join(__dirname, 'public')
  })).listen(process.env.DEV_PORT || 8181);

  livereload.listen({
    basePath: 'public'
  });
});

gulp.task('clean', function(cb) {
  del(['public/js/**/*.js', 'public/js/**/*.js.map'], cb);
});

// default tasks
gulp.task('default', ['clean'], function() {
  gulp.start('watch');
});


//
// aux functions
//
function browserifyBootstrapBundle() {
  var b = browserify({
    // cache: {},
    // packageCache: {},
    // fullPaths: true,
    debug: debug
  });

  if (watch) {
    b = watchify(b);
    b.on('update', function() {
      bundleBootstrapBundle(b);
    });
  }

  b.add('./client-js/bootstrap-bundle.js');
  bundleBootstrapBundle(b);
}

function bundleBootstrapBundle(b) {
  b.bundle()
    .on('error', notify.onError('Error: <%= error.message %>'))
    .pipe(gulpif(debug, exorcist(path.join(__dirname, 'public/js/bootstrap-bundle.js.map'))))
    .pipe(source('js/bootstrap-bundle.js'))
    .pipe(gulp.dest('./public'))
    .pipe(gulpif(watch, livereload()));
}

