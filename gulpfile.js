var base_path = '/vagrant/';
var gulp = require('gulp');
var less = require('gulp-less');
//var path = require('path');
var browserSync = require('browser-sync');
var reload = browserSync.reload;
//var shell = require('gulp-shell');
var spawn = require('child_process').spawn;
//var gspawn = require('gulp-spawn');
//var del = require('del');
var rename = require("gulp-rename");

const publicDir = base_path + 'src/server/public/';
var paths = {
  lessFiles: [base_path + 'src/assets/less/**'],
  jsFiles: [base_path + 'src/assets/js/**'],
  tmplFiles: [
    base_path + 'src/server/resources/views/**',
    base_path + 'src/server/modules/*/Resources/views/**'
  ],
  imgFiles: [
    base_path + 'src/assets/images/**',
    base_path + 'src/assets/images/button**/**',
    base_path + 'src/assets/images/moons/**.svg',
  ],
  langFiles: [
    base_path + 'src/server/resources/lang/**/*.php'
  ],
  moduleLangFiles : [
    base_path + 'src/server/modules/*/Resources/lang/**/*.php'
  ],
  vendorFiles: [
    {
      dir: '/leaflet',
      src: "/dist/leaflet.js",
      dst: publicDir + 'js/leaflet'
    },
    {
      dir: '/leaflet',
      src: [
        "/dist/leaflet.css",
        "/dist/images**/*"
      ],
      dst: publicDir + 'css/leaflet'
    },
    {dir: '/jquery', src: "/jquery.min.js", dst: "js/jquery" },
    {
      dir: '/jquery-ui',
      src: [
        "**/*.min.js",
        "**/themes**/**",
        "**/ui**/**"
      ],
      dst: publicDir + "js" },
    {
      dir: '/fullcalendar',
      src: "/dist/fullcalendar.min.css",
      dst: publicDir + 'css/fullcalendar'
    },
    {
      dir: '/fullcalendar',
      src: [
        "/dist/fullcalendar.min.js", "/dist/lang-all.js", "/dist/lang**/*"
      ],
      dst: publicDir + 'js/fullcalendar'
    },
    {
      dir: '/moment',
      src: "/min/moment-with-locales.min.js",
      dst: publicDir + 'js/fullcalendar',
      rename: 'moment.min.js'
    },
    {
      dir: '/Merriweather-Fontface',
      src: "/fonts/*",
      dst: publicDir + 'fonts'
    },
    {
      dir: '/bootstrap',
      src: "/fonts/*",
      dst: publicDir + 'fonts'
    },
    {
      dir: '/tinymce',
      src: [
        "**/skins/**",
        "**/plugins/**",
        "**/themes/**",
        "**/tinymce.min.js"
      ],
      dst: publicDir + 'js'
    },
  ]
};
gulp.task('vendorAssetCopy', function(){
  for ( let v of paths.vendorFiles ) {
    let dir = base_path + 'src/bower_components' + v.dir;
    if ( v.src instanceof Array) {
      for (let i in v.src) {
        v.src[i] = dir + v.src[i];
      }
    } else {
      v.src = dir + v.src;
    }
    if (v.rename) {
      gulp.src(v.src)
        .pipe(rename(v.rename))
        .pipe(gulp.dest(v.dst));
    } else {
      gulp.src(v.src).pipe(gulp.dest(v.dst));
    }
  }
});

gulp.task('clear_views', function(done){
  var cmd = spawn(
    'php',
    ['artisan', 'view:clear'],
    {cwd: base_path + 'src/server', stdio: 'inherit'}
  );
  cmd.on('close', function(code) {
    console.log('Done clearing view... exit code: ' + code);
    reload();
    done();
  })
});

gulp.task('rebootdb', function(){
  //var cmd = spawn('bash', ['rebootdb.sh'], {cwd: base_path + 'scripts', stdio: 'inherit'});
  //cmd.on('close', function(code) {
  //  console.log('Done rebooting Database... exit code: ' + code);
  //});
  //reload();
});

gulp.task('langs', function(done){
  var cmd = spawn(
    'php',
    ['artisan', 'lang:js', '-c', 'public/js/messages.js'],
    {cwd: base_path + 'src/server', stdio: 'inherit'}
  );
  cmd.on('close', function(code) {
    console.log('Done publishing js languages... exit code: ' + code);
    reload();
    done();
  });
  
});

var moduleLangCopy = function (e) {
  let l = e.path.replace(base_path + 'src/server/', '').split('/');
  let dest = base_path + 'src/server/resources/lang/vendor/' + l[1].toLowerCase();
  if (e.contents) {
    // called from gulp.dest
    return dest;
  }
  return gulp.src(e.path).pipe(gulp.dest(dest + '/' + l[4]));
};

gulp.task('less', function() {
  return gulp.src(base_path + 'src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest(base_path + 'src/server/public/css/'));
});

gulp.task('less-watch', ['less'], function(done){
  reload();
  done();
});

gulp.task('js', function(){
  gulp.src(paths.jsFiles)
  .pipe(gulp.dest(publicDir + 'js'));
});

gulp.task('js-watch', ['js'], function(done){
  reload();
  done();
});

gulp.task('images', function(){
  gulp.src(paths.imgFiles)
  .pipe(gulp.dest(publicDir + 'images'))
  .pipe(reload({stream:true})); //Browser Sync
});

gulp.task('images-watch', ['images'], function(done){
  reload();
  done();
});

gulp.task('browser-sync', function() {
    browserSync.init({
        proxy: "127.0.0.1",
        watchOptions: {
          usePolling: true
        }
    });
});

gulp.task(
  'default',
  [
    'vendorAssetCopy',
    'browser-sync',
    'less',
    'js',
    'images',
    'clear_views',
    'langs'
  ],
  function() {
    gulp.watch(paths.lessFiles, { interval: 1000 }, ['less-watch']);
    gulp.watch(paths.jsFiles, { interval: 1000 }, ['js-watch']);
    gulp.watch(paths.tmplFiles, { interval: 1000 }, ['clear_views']);
    gulp.watch(paths.moduleLangFiles, { interval: 1000 }, moduleLangCopy);
    gulp.watch(paths.langFiles, { interval: 1000 }, ['langs']);
    gulp.watch(
      base_path + 'src/bower_components/**',
      { interval: 1000 },
      ['vendorAssetCopy']
    );
    gulp.watch(paths.imgFiles, { interval: 1000 }, ['images-watch']);
  }
);

gulp.task('bowercopy', function(){
  gulp.src(base_path + 'src/assets/less/style.less')
    .pipe(less())
    .pipe(gulp.dest(base_path + 'src/server/public/css/'));

  gulp.src([
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
    base_path + 'src/bower_components/leaflet/dist/leaflet.css',
    base_path + 'src/bower_components/leaflet/dist/images**/*'
    ])
    .pipe(gulp.dest(base_path + 'src/server/public/js/leaflet'));

  gulp.src(base_path + 'src/bower_components/moment/min/moment-with-locales.min.js')
  .pipe(rename('moment.min.js'))
  .pipe(gulp.dest(base_path + 'src/server/public/js/fullcalendar'));

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

  gulp.src(paths.imgFiles).pipe(gulp.dest(base_path + 'src/server/public/images'));

  for (let x of ['SeedBank', 'ProjectPresentation', 'Authentication', 'Newsletter']){
    gulp.src(base_path + 'src/server/modules/' + x + '/Resources/lang/**/*.php')
      .pipe(gulp.dest(moduleLangCopy));
            //(e)base_path + 'src/server/resources/lang/vendor/' + x.toLowerCase()));
    //moduleLangCopy({'path':  base_path + 'src/server/modules/' + x + 'Resources/lang/*/*.php'
  };

  var cmd = spawn(
    'php',
    ['artisan', 'lang:js', '-c', 'public/js/messages.js'],
    {cwd: base_path + 'src/server', stdio: 'inherit'}
  );
  cmd.on('close', function(code) {
    console.log('Done rebooting Database... exit code: ' + code);
  });
  var cmd2 = spawn(
    'php',
    ['artisan', 'view:clear'],
    {cwd: base_path + 'src/server', stdio: 'inherit'}
  );
  cmd2.on('close', function(code) {
    console.log('Done clearing view... exit code: ' + code);
    reload();
  });
});
