'use strict';

var async = require('async');
var equal = require('deep-equal');
var path = require('path');

var couch = require('../couchdb');

var databaseNames = ['mailinglist'];
var views = {};

databaseNames.forEach(function(database) {
  views[database] = require(path.join(__dirname, database));
});

exports.populate = function(cb) {
  async.each(databaseNames, populateDB, cb);
};


function populateDB(dbName, cb) {
  var db = couch.use(dbName);
  var dbViews = views[dbName];

  async.eachSeries(Object.keys(dbViews), ensureView, cb);

  function ensureView(viewName, cbi) {
      var view = dbViews[viewName];
      var ddocName = '_design/' + viewName;

      db.get(ddocName, function(err, ddoc) {
        if (err && err.statusCode === 404) insertDDoc(null, cbi);
        else if (err) cbi(err);
        else if (equal(ddoc.views[viewName], view)) cbi();
        else insertDDoc(ddoc, cbi);
      });

      function insertDDoc(ddoc, cbii) {
        if (!ddoc) ddoc = {
          language: 'javascript',
          views: {}
        };

        ddoc.views[viewName] = view;

        db.insert(ddoc, ddocName, function(err) {
          // 409: Conflict error
          if (err && err.statusCode === 409) ensureView(viewName, cbii);
          else cbii(err);
        });
      }

    } // ensureView

}

