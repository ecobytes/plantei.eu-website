/*global $,jQuery */
/*eslint-env browser */

$ = jQuery = require('jquery');
var bootstrap = require('bootstrap');

$(function() {
  'use strict';
  $('a[href*=#]:not([href=#])').click(function() {
    if (window.location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && window.location.hostname === this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});

