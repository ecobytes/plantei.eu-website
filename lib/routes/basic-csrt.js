'use strict';

exports.register = function(server, options, next) {
  server.route([
    // a "crumb" cookie gets set with any request when not using views
    {
      method: 'GET',
      path: '/generate',
      handler: function(request, reply) {
        // return crumb if desired
        return reply('{ "crumb": ' + request.plugins.crumb + ' }');
      }
    },
    // request header "X-CSRF-Token" with crumb value must be set in request for this route
    {
      method: 'PUT',
      path: '/crumbed',
      handler: function(request, reply) {
        return reply('Crumb route');
      }
    }
  ]);

  next();
};

exports.register.attributes = {
  name: 'base-csrf-routes',
  version: '1.0.0'
};

