'use strict';

var extend = require('util')._extend;
var Joi = require('joi');

var updateAttributes = {
  _id: Joi.string(),
  _rev: Joi.string(),
  name: Joi.string().max(120).required(),
  updatedAt: Joi.date()
};

var createAttributes = extend({
  email: Joi.string().email().required(),
  createdAt: Joi.date()
}, updateAttributes);

var getAttributes = {
  email: Joi.string().email(),
  _id: Joi.string()
};

var baseAttributes = {
  email: Joi.string().email(),
  name: Joi.string().max(120).required()
};

module.exports = {
  create: Joi.object().keys(createAttributes),
  update: Joi.object().keys(updateAttributes),
  email: Joi.object().keys(getAttributes).xor('email', '_id'),
  base: Joi.object().keys(baseAttributes)
};

