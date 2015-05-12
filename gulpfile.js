var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var shell = require('gulp-shell')

var lessFiles = ['/vagrant/src/assets/less/**'];
var htmlFiles = ['/vagrant/src/client/**/**', '/vagrant/src/resources/views/**/**.php'];
var jsFiles = '/vagrant/src/client/js/**/**.js';
var appFiles = ['/vagrant/src/server/app/**', '/vagrant/server/modules']

gulp.task('default', ['bowercopy', 'browser-sync','less', 'js'], function() {


  gulp.watch(lessFiles, { interval: 1000 }, ['less']);
  gulp.watch(htmlFiles, { interval: 1000 }).on('change', reload);
  gulp.watch(jsFiles, { interval: 1000 }, ['js']);
  gulp.watch(appFiles, { interval: 1000 }).on('change', reload);

});

gulp.task('less', function() {
  gulp.src('/vagrant/src/server/assets/less/admin.less')
    .pipe(less())
    .pipe(gulp.dest('/vagrant/src/css/'))
    .pipe(reload({stream:true})); //Browser Sync
});

gulp.task('bowercopy', function(){
  gulp.src([
    '/vagrant/src/bower_components/angular/angular.js',
    '/vagrant/src/bower_components/angular-bootstrap/ui-bootstrap.js',
    '/vagrant/src/bower_components/angular-loading-bar/build/loading-bar.js',
    '/vagrant/src/bower_components/lodash/dist/lodash.js',
    '/vagrant/src/bower_components/restangular/dist/restangular.js',
    '/vagrant/src/bower_components/angular-route/angular-route.js'
    ])
  .pipe(gulp.dest('/vagrant/src/client/js/libs'))

  gulp.src('/vagrant/src/bower_components/angular-loading-bar/src/loading-bar.css')
  .pipe(gulp.dest('/vagrant/src/client/css/'))

});

gulp.task('js', function(){
  gulp.src(jsFiles)
  .pipe(reload({stream:true})); //Browser Sync
});


gulp.task('browser-sync', function() {
    browserSync({
        proxy: "127.0.0.1"
    });
});