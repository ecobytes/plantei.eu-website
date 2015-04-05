'use strict';

var mailinglist = require('../../db/mailinglist');

module.exports = {
  create: createEntry,
  update: updateEntry
};


function createEntry(request, reply) {
  mailinglist.create(request.payload, function(err) {
    if (err) reply(err).code(400);
    else mailinglist.get({
      _id: 'email::' + request.payload.email
    }, function(errg, entry) {
      if (errg) reply(errg).code(400);
      reply(entry).code(201);
    });
  });
}

function updateEntry(request, reply) {
  var payload = {
    _id: 'email::' + request.payload.email,
    name: request.payload.name
  };

  mailinglist.update(payload, function(err) {
    if (err) reply(err).code(400);
    else mailinglist.get({
      _id: 'email::' + request.payload.email
    }, function(errg, entry) {
      if (errg) reply(errg).code(400);
      reply(entry).code(200);
    });
  });
}

