'use strict';

var extend = require('util')._extend;

var schemas = require('../schemas');
var errors = require('../errors');
var mailinglist = require('./couchdb').use('mailinglist');

module.exports = {
  create: schemas.validating('mailinglist', 'create', createEntry),
  update: schemas.validating('mailinglist', 'update', updateEntryDiff),
  delete: schemas.validating('mailinglist', 'email', deleteEntry),
  get: schemas.validating('mailinglist', 'email', getEntry)
};


function createEntry(entry, cb) {
  entry._id = 'email::' + entry.email;
  entry.createdAt = Date.now();
  entry.updatedAt = Date.now();
  mailinglist.insert(entry, errors.wrapNano(cb));
}

function updateEntryDiff(entryDiff, cb) {
  schemas.validate(entryDiff, 'mailinglist', 'update', function(err) {
    if (err) cb(err);
    else merge();
  });

  function merge() {
    mailinglist.get(entryDiff._id, errors.wrapNano(function(err, currentEntry) {
      if (err) cb(err);
      else {
        entryDiff.updatedAt = Date.now();
        extend(currentEntry, entryDiff);
        mailinglist.insert(currentEntry, errors.wrapNano(done));
      }
    }));
  }

  function done(err) {
    if (err && err.statusCode === 409 && !entryDiff._rev) merge(); // try again!
    else cb.apply(null, arguments);
  }
}

function deleteEntry(entry, cb) {
  var id = entry._id || 'email::' + entry.email;
  mailinglist.get(id, errors.wrapNano(function(err, currentEntry) {
    if (err) cb(err);
    else mailinglist.destroy(currentEntry._id, currentEntry._rev, errors.wrapNano(cb));
  }));
}

function getEntry(entry, cb) {
  var id = entry._id || 'email::' + entry.email;
  mailinglist.get(id, errors.wrapNano(function(err, currentEntry) {
    if (err) cb(err);
    else cb(null, currentEntry);
  }));
}

