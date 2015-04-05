'use strict';

var ctrl = require('./controllers');
var schema = require('../schemas/mailinglist');

exports.register = function(server, options, next) {
  server.route([{
    method: 'POST',
    path: '/api/v1.0/mailing-list',
    config: {
      handler: ctrl.mailinglist.create,
      validate: {
        payload: schema.base
      }
      // ,
      // plugins: {
      //   crumb: { skip: true }
      // }
    }
  }]);

  server.route([{
    method: 'PUT',
    path: '/api/v1.0/mailing-list',
    config: {
      handler: ctrl.mailinglist.update,
      validate: {
        payload: schema.base
      }
    }
  }]);

  next();
};

exports.register.attributes = {
  name: 'mailing-list-routes',
  version: '1.0.0'
};

