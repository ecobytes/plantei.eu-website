/*eslint no-shadow:0 */

'use strict';

var Code = require('code'); // assertion library
var Lab = require('lab');
var lab = exports.lab = Lab.script();

var server = require('../server.js');
var mailinglist = require('../lib/db/mailinglist');

var expect = Code.expect;

var payload = {
  name: 'Api Test User',
  email: 'api-test-mailinglist-entry@test.plantei.eu'
};


lab.experiment('Mailing List', function() {

  lab.before(function(done) {
    mailinglistDelete(done);
  });

  lab.after(function(done) {
    mailinglistDelete(done);
  });

  lab.test('Create mailing list entry', function(done) {
    var options = {
      method: 'POST',
      url: '/api/v1.0/mailing-list',
      payload: payload
    };

    server.inject(options, function(res) {
      var header = res.headers['set-cookie'];
      expect(header).to.not.exist(); // crumb disabled!

      payload.name = payload.name + ' Changed!';
      options = {
        method: 'PUT',
        url: '/api/v1.0/mailing-list',
        payload: payload
      };

      server.inject(options, function(res) {
        expect(res.statusCode).to.equal(200);

        mailinglist.get({
            _id: 'email::' + payload.email
          },
          function(err, entry) {
            expect(err).to.not.exist();
            expect(entry._rev).to.startWith('2-');
            expect(entry.email).to.be.equal(payload.email);
            expect(entry.name).to.be.equal(payload.name);
            expect(entry.updatedAt).to.be.above(entry.createdAt);
            done();
          });
      });
    });
  });

  function mailinglistDelete(done) {
    mailinglist.delete({
      email: payload.email
    }, function() {
      done();
    });
  }

});

