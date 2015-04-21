'use strict';

var Hapi = require('hapi');

var initDB = require('./lib/db/init');

var server = new Hapi.Server();
var port = Number(process.env.PORT || 9091);

var plugins = [{
  register: require('./lib/routes/basic-csrt')
}, {
  register: require('./lib/routes/mailinglist')
}, {
  register: require('crumb'),
  options: {
    restful: true,
    cookieOptions: {
      isSecure: true
    },
    skip: function(request) {
      if (request.path === '/api/v1.0/mailing-list') return true;
      else return false;
    }
  }
}, {
  register: require('good'),
  options: {
    reporters: [{
      reporter: require('good-console'),
      events: [{
        log: '*',
        request: '*'
      }]
    }]
  }
}];

if (process.env.NODE_ENV === 'development') {
  plugins.push({
    register: require('tv'),
    options: {}
  });
}

server.connection({
  host: '127.0.0.1',
  port: port,
  routes: {
    validate: {
      options: {
        abortEarly: false
      }
    }
  }
});

server.register(plugins, function(err) {
  if (err) throw err;

  if (!module.parent)
    server.start(function() {
      server.log('info', 'Server running at: ' + server.info.uri);
    });

  initDB(function(errdb) {
    if (errdb) throw errdb;
    else server.log('info', 'couchdb initialized!');
  });
});

module.exports = server;

