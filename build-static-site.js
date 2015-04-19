'use strict';

var Metalsmith = require('metalsmith');
var markdown = require('metalsmith-markdown');
var templates = require('metalsmith-templates');
var collections = require('metalsmith-collections');
var permalinks = require('metalsmith-permalinks');
var less = require('metalsmith-less');
var debug = require('debug')('plantei:build');

//Loading partials
var fs = require('fs');
var path = require('path');
var handlebars = require('handlebars');

var metalsmith = new Metalsmith(__dirname);


// Partials
var partialsDir = './templates/partials/';
var partialFiles = fs.readdirSync(partialsDir);
partialFiles.forEach(function(partialFile) {
  var partialName = path.basename(partialFile, '.hbs');
  var content = fs.readFileSync(path.join(partialsDir, partialFile), 'utf8');
  handlebars.registerPartial(partialName, content);
});


metalsmith
  .use(markdown())
  .use(collections({
    articles: {
      pattern: 'articles/**/*.md',
      sortBy: 'date',
      reverse: true,
      limit: 10
    }
  }))
  .use(permalinks({
    pattern: ':title'
  }))
  .use(templates({
    engine: 'handlebars',
    directory: 'templates'
  }))
  .use(less({
    pattern: 'css/**.less'
  }))
  .use(deleteDrafts())
  .destination('./build')
  .build(function(err) {
    // var m;
    if (err) debug('Error!', err);

    // m = metalsmith.metadata();
    // debug('What? ', m);
  });


// we can create any metadata we need! (draft is just one example)
function deleteDrafts() {
  return function drafts(files, ms, done) {
    Object.keys(files).forEach(function(file) {
      debug('delete draft %s', file);
      if (files[file].draft) delete files[file];
    });
    done();
  };
}
