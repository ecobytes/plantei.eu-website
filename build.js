'use strict';

var Metalsmith = require('metalsmith'),
  markdown = require('metalsmith-markdown'),
  templates = require('metalsmith-templates'),
  collections = require('metalsmith-collections'),
  permalinks = require('metalsmith-permalinks'),
  less = require('metalsmith-less'),
  debug = require('debug');

var log = debug('build');
var metalsmith = new Metalsmith(__dirname);

metalsmith
// .use(setTemplate({
//   pattern: 'projects#<{(||)}>#*.md',
//   template: 'projects.html'
// }))
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
    pattern: 'assets/css/**.less'
    }
  ))

  // .use(deleteDrafts())
  .destination('./build')
  .build(function(err) {
    // var m;
    if (err) log('Error! %s', err);

    // m = metalsmith.metadata();
    // log('What? ', m);
  });


// we can create any metadata we need! (draft is just one example)
function deleteDrafts() {
  return function drafts(files, ms, done) {
    Object.keys(files).forEach(function(file) {
      log('delete draft %s', file);
      if (files[file].draft) delete files[file];
    });
    done();
  };
}
