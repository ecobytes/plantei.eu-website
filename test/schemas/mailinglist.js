/*eslint no-shadow:0 */

'use strict';

var Code = require('code'); // assertion library
var Lab = require('lab');
var lab = exports.lab = Lab.script();

var schemas = require('../../lib/schemas');

// var before = lab.before;
// var after = lab.after;
var expect = Code.expect;


lab.experiment('Mailing List Schema', function() {

  lab.test('create with required fields', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      email: 'zedopipo@mail.com',
      createdAt: Date.now(),
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'create', op);

    testOp(payload, function(err) {
      expect(err).to.be.undefined();
      done();
    });

    function op(entry, cb) {
      expect(entry).to.deep.equal(payload);
      cb();
    }
  });

  lab.test('create with invalide email', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      email: 'zedopipomail.com',
      createdAt: Date.now(),
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'create', op);

    testOp(payload, function(err) {
      expect(err.message).to.be.equal('child "email" fails because ["email" must be a valid email]');
      done();
    });

    function op(entry, cb) {
      expect(entry).to.deep.equal(payload);
      cb();
    }
  });

  lab.test('create with invalide name (long)', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      email: 'zedopipo@mail.com',
      createdAt: Date.now(),
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'create', op);
    var i = 0;

    for (; i < 12; i++) {
      payload.name += 'Ze do Pipo';
    }

    testOp(payload, function(err) {
      expect(err.message).to.startWith('child "name" fails because ["name" length must be less than or equal to');
      done();
    });

    function op(entry, cb) {
      expect(entry).to.deep.equal(payload);
      cb();
    }
  });

  lab.test('create without required fields (name)', function(done) {
    var payload = {
      email: 'zedopipo@mail.com',
      createdAt: Date.now(),
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'create', op);

    testOp(payload, function(err) {
      expect(err.message).to.be.equal('child "name" fails because ["name" is required]');
      done();
    });

    function op(entry, cb) {
      // this should not be called in case of error
      expect(false).to.be.true();
      cb();
    }
  });

  lab.test('create without required fields (email)', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      createdAt: Date.now(),
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'create', op);

    testOp(payload, function(err) {
      expect(err.message).to.be.equal('child "email" fails because ["email" is required]');
      done();
    });

    function op(entry, cb) {
      // this should not be called in case of error
      expect(false).to.be.true();
      cb();
    }
  });

  lab.test('update with required fields', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'update', op);

    testOp(payload, function(err) {
      expect(err).to.be.undefined();
      done();
    });

    function op(entry, cb) {
      expect(entry).to.deep.equal(payload);
      cb();
    }
  });

  lab.test('update with field not allowed (email)', function(done) {
    var payload = {
      name: 'Ze do Pipo',
      email: 'zedopipo@mail.com',
      updatedAt: Date.now()
    };
    var testOp = schemas.validating('mailinglist', 'update', op);

    testOp(payload, function(err) {
      expect(err.message).to.be.equal('"email" is not allowed');
      done();
    });

    function op(entry, cb) {
      // this should not be called in case of error
      expect(false).to.be.true();
      cb();
    }
  });

});

