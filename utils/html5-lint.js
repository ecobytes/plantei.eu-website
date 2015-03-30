'use strict';

var html5Lint = require('html5-lint');
var recursive = require('recursive-readdir');
var minimatch = require('minimatch');
var colors = require('colors');
var fs = require('fs');

recursive('assets/', ['assets/vendor/**/*', 'assets/assets/**/*'], function(err, files) {
  files.map(function(file) {
    if (minimatch(file, '**/*.html')) {
      fs.readFile(file, 'utf8', function(err, html) {
        if (err) throw err;

        html5Lint(html, function(erri, results) {
          if (erri) throw erri;

          if (results.messages.length) console.log('File:'.green, file);

          results.messages.forEach(function(msg) {
            var type = msg.type, // error or warning
              message = msg.message;

            if (type === 'error')
              console.log('HTML5 Lint [%s]: %s'.red, type, message);
            else
              console.log('HTML5 Lint [%s]: %s'.blue, type, message);
          });
        });
      });
    }
  });
});
