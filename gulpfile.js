var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var shell = require('gulp-shell')
var del = require('del');
var rename = require("gulp-rename");

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
  gulp.src([
    '/vagrant/src/bower_components/bootstrap/dist/js/bootstrap.min.js',
    '/vagrant/src/bower_components/jquery/dist/jquery.min.js',
    '/vagrant/src/bower_components/jquery-ui/jquery-ui.min.js'
    ])
  .pipe(gulp.dest('/vagrant/src/server/public/js/'));
  gulp.src([
    '/vagrant/src/bower_components/jquery-ui/themes/ui-lightness/jquery-ui.min.css',
    '/vagrant/src/bower_components/jquery-ui/themes/ui-lightness/images**/*'
    ], {base: '/vagrant/src/bower_components/jquery-ui/themes/ui-lightness'})
  .pipe(gulp.dest('/vagrant/src/server/public/css'));

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

gulp.task('local', function() {
  gulp.src('src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest('src/server/public/css/'))
  gulp.src([
    'src/assets/js/tinymce**/**/**',
    'src/assets/js/event_scripts.js',
    'src/bower_components/bootstrap/dist/js/bootstrap.min.js',
    'src/bower_components/jquery/dist/jquery.min.js',
    'src/bower_components/jquery-ui/jquery-ui.min.js',
    'src/bower_components/blueimp-file-upload/js/*.js',
    'src/bower_components/blueimp-file-upload/js/**/*.js',
    'src/bower_components/blueimp-load-image/js/load-image.all.min.js',
    'src/bower_components/tinymce**/**',
    'src/bower_components/tinymce**/tinymce.min.js',
    'src/bower_components/jt.timepicker/*.min.js',
    'src/bower_components/jt.timepicker/*.min.js',
    'src/bower_components/bootstrap-tokenfield/dist/bootstrap-tokenfield.min.js'
    ])
  .pipe(gulp.dest('src/server/public/js'));
  gulp.src([
    'src/bower_components/fullcalendar/dist/fullcalendar.min.js',
    'src/bower_components/fullcalendar/dist/lang-all.js',
    'src/bower_components/fullcalendar/dist/lang**/*',
    ])
  .pipe(gulp.dest('src/server/public/js/fullcalendar'));

  gulp.src('src/bower_components/moment/min/moment-with-locales.min.js')
  .pipe(rename('moment.min.js'))
  .pipe(gulp.dest('src/server/public/js/fullcalendar'));

  gulp.src([
    'src/bower_components/jquery-ui/themes/ui-lightness/jquery-ui.min.css',
    'src/bower_components/jquery-ui/themes/ui-lightness/images**/*'
    ], {base: 'src/bower_components/jquery-ui/themes/ui-lightness'})
  .pipe(gulp.dest('src/server/public/css'));

  gulp.src([
    'src/assets/js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css',
    'src/assets/js/jquery-ui-1.11.4.custom/images**/*.png',
    'src/bower_components/blueimp-file-upload/css/jquery.fileupload.css',
    'src/bower_components/blueimp-file-upload/css/jquery.fileupload-noscript.css',
    'src/bower_components/jt.timepicker/*.css',
    'src/bower_components/bootstrap-tokenfield/dist/css/bootstrap-tokenfield.min.css'
    ])
  .pipe(gulp.dest('src/server/public/css'));

  gulp.src([
    'src/bower_components/fullcalendar/dist/fullcalendar.min.css',
    ])
  .pipe(gulp.dest('src/server/public/css/fullcalendar'));

  gulp.src([
    'src/assets/less/Fira/*.{eot,svg,ttf,woff,woff2}',
    'src/bower_components/bootstrap/fonts/*.{eot,svg,ttf,woff,woff2}',
    ])
  .pipe(gulp.dest('src/server/public/fonts'));
});
