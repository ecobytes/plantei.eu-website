/*eslint no-shadow:0 */

'use strict';

var Code = require('code'); // assertion library
var Lab = require('lab');
var lab = exports.lab = Lab.script();

var server = require('../server.js');

// var before = lab.before;
// var after = lab.after;
var expect = Code.expect;


lab.experiment('CSRF', function() {

  lab.test('reply with a crumb cookie', function(done) {
    var options = {
      method: 'GET',
      url: '/generate'
    };

    server.inject(options, function(res) {
      var header = res.headers['set-cookie'];
      var cookie;
      var validHeader = {};
      var invalidHeader = {};

      expect(header.length).to.equal(1);
      expect(header[0]).to.contain('Secure');

      cookie = header[0].match(/crumb=([^\x00-\x20\"\,\;\\\x7F]*)/);

      validHeader.cookie = 'crumb=' + cookie[1];
      validHeader['x-csrf-token'] = cookie[1];

      invalidHeader.cookie = 'crumb=' + cookie[1];
      invalidHeader['x-csrf-token'] = 'x' + cookie[1];

      options = {
        method: 'PUT',
        url: '/crumbed',
        headers: validHeader
      };
      server.inject(options, function(res) {
        expect(res.result).to.equal('Crumb route');

        options = {
          method: 'PUT',
          url: '/crumbed',
          headers: invalidHeader
        };
        server.inject(options, function(res) {
          expect(res.statusCode).to.equal(403);

          done();
        });
      });
    });
  });
});

