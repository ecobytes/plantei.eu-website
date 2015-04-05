'use strict';

var http = require('http');
var nano = require('nano');

// override the default node 5 maximum number of simultaneous connections!
http.globalAgent.maxSockets = Number(process.env.HTTP_MAX_SOCKETS) || 1024;

module.exports = nano(process.env.COUCHDB_URL || 'http://127.0.0.1:5984');

