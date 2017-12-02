var base_path = '/vagrant/';
var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var shell = require('gulp-shell')
var exec = require('child_process').exec;
var spawn = require('child_process').spawn;
var del = require('del');
var rename = require("gulp-rename");

var lessFiles = [base_path + 'src/assets/less/**'];
//var htmlFiles = [base_path + 'src/client/**/**', base_path + 'src/resources/views/**/**.php'];
//var jsFiles = base_path + 'src/client/js/**/**.js';
var htmlFiles = [base_path + 'src/server/public/*.php', base_path + 'src/resources/views/**/**.php'];
var jsFiles = base_path + 'src/server/public/js/*.js';
var appFiles = [base_path + 'src/server/app/**/**', base_path + 'src/server/modules/**/**', base_path + 'src/server/resources/views/**/**'];
var imgFiles = [base_path + 'src/assets/images/*'];


gulp.task('default', ['bowercopy', 'browser-sync','less', 'js'], function() {
  gulp.watch(lessFiles, { interval: 1000 }, ['less']);
  gulp.watch(htmlFiles, { interval: 1000 }).on('change', reload);
  gulp.watch(imgFiles, { interval: 1000 }, ['bower_copy']);
  gulp.watch(jsFiles, { interval: 1000 }, ['js']);
  gulp.watch(appFiles, { interval: 1000 }, ['clear_views']);
});

gulp.task('clear_views', function(){
  // will break with gulp 4.0
  //gulp.src('gulpfile.js', {read: false})
  //    .pipe(shell('php artisan view:clear', {cwd: base_path + 'src/server/'}))
  //    .pipe(reload({stream:true}));
  var cmd = spawn('php', ['artisan', 'view:clear'], {cwd: base_path + 'src/server', stdio: 'inherit'});
  cmd.on('close', function(code) {
    console.log('Done clearing view... exit code: ' + code);
    reload();
  //});
  });

});

gulp.task('rebootdb', function(){
  var cmd = spawn('bash', ['rebootdb.sh'], {cwd: '/vagrant/scripts', stdio: 'inherit'});
  cmd.on('close', function(code) {
    console.log('Done rebooting Database... exit code: ' + code);
  });

});


gulp.task('less', function() {
  gulp.src(base_path + 'src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest(base_path + 'src/server/public/css/'))
    .pipe(reload({stream:true})); //Browser Sync
});

gulp.task('bowercopy', function(){
  gulp.src(base_path + 'src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest(base_path + 'src/server/public/css/'))

  gulp.src([
    //base_path + 'src/assets/js/tinymce**/**',
    base_path + 'src/assets/js/tinymce**/**/**/plugin.min.js',
    base_path + 'src/assets/js/*.js',
    base_path + 'src/bower_components/bootstrap/dist/js/bootstrap.min.js',
    base_path + 'src/bower_components/jquery/dist/jquery.min.js',
    base_path + 'src/bower_components/jquery-ui/jquery-ui.min.js',
    base_path + 'src/bower_components/blueimp-file-upload/js/*.js',
    base_path + 'src/bower_components/blueimp-file-upload/js/**/*.js',
    base_path + 'src/bower_components/blueimp-load-image/js/load-image.all.min.js',
    base_path + 'src/bower_components/tinymce**/**',
    base_path + 'src/bower_components/tinymce**/tinymce.min.js',
    base_path + 'src/bower_components/jt.timepicker/*.min.js',
    base_path + 'src/bower_components/bootstrap-tokenfield/dist/bootstrap-tokenfield.min.js'
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/js'));

  gulp.src([
    base_path + 'src/bower_components/fullcalendar/dist/fullcalendar.min.js',
    base_path + 'src/bower_components/fullcalendar/dist/lang-all.js',
    base_path + 'src/bower_components/fullcalendar/dist/lang**/*',
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/js/fullcalendar'));

  gulp.src([
    base_path + 'src/bower_components/leaflet/dist/leaflet.js',
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/js/leaflet'));

  gulp.src([
    base_path + 'src/bower_components/leaflet/dist/leaflet.css',
    base_path + 'src/bower_components/leaflet/dist/images**/*',
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/js/leaflet'));

  gulp.src(base_path + 'src/bower_components/moment/min/moment-with-locales.min.js')
  .pipe(rename('moment.min.js'))
  .pipe(gulp.dest(base_path + 'src/server/public/js/fullcalendar'));

  //gulp.src([
  //  base_path + 'src/bower_components/jquery-ui/themes/ui-lightness/jquery-ui.min.css',
  //  base_path + 'src/bower_components/jquery-ui/themes/ui-lightness/images**/*'
  //  ], {base: base_path + 'src/bower_components/jquery-ui/themes/ui-lightness'})
  //.pipe(gulp.dest(base_path + 'src/server/public/css'));

  gulp.src([
    base_path + 'src/assets/css/jquery-ui.theme.min.css',
    base_path + 'src/assets/css/jquery-ui.min.css',
    base_path + 'src/assets/css/images**/*.png',
    base_path + 'src/bower_components/blueimp-file-upload/css/jquery.fileupload.css',
    base_path + 'src/bower_components/blueimp-file-upload/css/jquery.fileupload-noscript.css',
    base_path + 'src/bower_components/jt.timepicker/*.css',
    base_path + 'src/bower_components/bootstrap-tokenfield/dist/css/bootstrap-tokenfield.min.css'
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/css'));

  gulp.src([
    base_path + 'src/bower_components/fullcalendar/dist/fullcalendar.min.css',
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/css/fullcalendar'));

  gulp.src([
    base_path + 'src/assets/less/Fira/*.{eot,svg,ttf,woff,woff2}',
    base_path + 'src/bower_components/bootstrap/fonts/*.{eot,svg,ttf,woff,woff2}',
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/fonts'));

  //gulp.src([
  //  base_path + 'src/assets/images/logo_plantei.svg',
  //  base_path + 'src/assets/images/trigos.svg',
  //  ])
  //.pipe(gulp.dest(base_path + 'src/server/public/images'));
  gulp.src([
    base_path + 'src/assets/images/*'
    ])
  .pipe(gulp.dest(base_path + 'src/server/public/images'));

  // will break with gulp 4.0
  gulp.src('gulpfile.js', {read: false})
      .pipe(shell('php artisan lang:js -c public/js/messages.js', {cwd: base_path + 'src/server/', quiet: true}));

});

gulp.task('js', function(){
  gulp.src(jsFiles)
  .pipe(reload({stream:true})); //Browser Sync
});


gulp.task('browser-sync', function() {
    browserSync({
        proxy: "127.0.0.1",
        watchOptions: {
          usePolling: true
        }
    });
});
