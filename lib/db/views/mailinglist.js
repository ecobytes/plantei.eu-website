'use strict';

exports.by_email = {
  map: function(doc) {
    if (doc.email) emit(doc.email, { _id: doc._id });
  }
};

