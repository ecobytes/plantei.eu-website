/*eslint no-shadow:0 */

'use strict';

var Code = require('code'); // assertion library
var Lab = require('lab');
var lab = exports.lab = Lab.script();

var mailinglist = require('../../lib/db/mailinglist');

// var before = lab.before;
// var after = lab.after;
var expect = Code.expect;


lab.experiment('Mailing List DB', function() {

  lab.test('create entry in mailinglist', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      email: 'zedopipo@mail.com'
    };

    /// [C]reate
    mailinglist.create(payload, function(err) {
      expect(err).to.not.exist();

      /// [R]etrieve
      mailinglist.get({
          _id: 'email::' + payload.email
        },
        function(err, entry) {
          expect(err).to.not.exist();
          expect(entry._id).to.be.equal('email::' + payload.email);
          expect(entry.email).to.be.equal(payload.email);
          expect(entry.name).to.be.equal(payload.name);

          var updateData = {
            _id: entry._id,
            name: entry.name + ' Updated!'
          };
          mailinglist.update(updateData, function(err) {
            expect(err).to.not.exist();

            /// [R]etrieve (2)
            mailinglist.get({
                email: payload.email
              },
              function(err, entry2) {
                expect(err).to.not.exist();
                expect(entry2._id).to.be.equal(entry._id);
                expect(entry2._rev).to.be.not.equal(entry._rev);
                expect(entry2.email).to.be.equal(entry.email);
                expect(entry2.name).to.be.equal(updateData.name);
                expect(entry2.updatedAt).to.be.above(entry.createdAt);

                mailinglist.delete({
                  email: payload.email
                }, function(err) {
                  expect(err).to.not.exist();
                  done();
                });
              });
          });
        });
    });
  });

  lab.test('get non-existent entry in mailinglist', function(done) {
    mailinglist.get({
        email: 'non-existent-email-address@test.plantei.eu'
      },
      function(err, entry) {
        expect(err.error).to.be.equal('not_found');
        expect(err.message).to.be.equal('missing');
        expect(err.statusCode).to.be.equal(404);
        expect(entry).to.not.exist();
        done();
      });
  });

});

