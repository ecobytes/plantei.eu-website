'use strict';

var Boom = require('boom');

exports.wrapNano = wrapNanoError;


function wrapNanoError(cb) {
  return function(err) {
    if (err) Boom.wrap(err, err.statusCode || 500);

    cb.apply(null, arguments);
  };
}
