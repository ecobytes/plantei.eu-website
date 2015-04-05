'use strict';

var Joi = require('joi');
var Boom = require('boom');
var path = require('path');

var schemaNames = ['mailinglist'];
var schemas = {};

schemaNames.forEach(function(schemaName) {
  schemas[schemaName] = require(path.join(__dirname, schemaName));
});

module.exports = {
  validate: validate,
  validating: validating
};


function validate(doc, schema, op, cb) {
  if (typeof schema === 'string') schema = schemas[schema];

  if (!schema) cb(new Error('Unkown schema!'));
  else {
    schema = schema[op];

    if (!schema) cb(new Error('Undefined operation! ' + op));
    else
      Joi.validate(doc, schema, { abortEarly: false }, function(err) {
        if (err) {
          Boom.wrap(err, 400);
          cb(err);
        } else cb(null, doc);
      });
  }
}

function validating(schemaName, op, fn) {
  var schema = schemas[schemaName];

  if (!schema) throw new Error('Unknown schema: ' + schemaName);

  return function(doc, cb) {
    validate(doc, schema, op, function(err, doci) {
      if (err) cb(err);
      else fn.call(null, doci, cb);
    });
  };
}

