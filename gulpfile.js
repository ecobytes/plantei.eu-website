var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var shell = require('gulp-shell')
var del = require('del');

var lessFiles = ['/vagrant/src/assets/less/**'];
var htmlFiles = ['/vagrant/src/client/**/**', '/vagrant/src/resources/views/**/**.php'];
var jsFiles = '/vagrant/src/client/js/**/**.js';
var appFiles = ['/vagrant/src/server/app/**/**', '/vagrant/src/server/modules/**/**']

gulp.task('default', ['bowercopy', 'browser-sync','less', 'js'], function() {


  gulp.watch(lessFiles, { interval: 1000 }, ['less']);
  gulp.watch(htmlFiles, { interval: 1000 }).on('change', reload);
  gulp.watch(jsFiles, { interval: 1000 }, ['js']);
  gulp.watch(appFiles, { interval: 1000 }).on('change', reload);

});

gulp.task('less', function() {
  gulp.src('/vagrant/src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest('/vagrant/src/server/public/css/'))
    .pipe(reload({stream:true})); //Browser Sync
});

gulp.task('bowercopy', function(){
 /* gulp.src([
    
    ])
  .pipe(gulp.dest('/vagrant/src/client/js/libs'))

  .pipe(gulp.dest('/vagrant/src/client/css/'))*/

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