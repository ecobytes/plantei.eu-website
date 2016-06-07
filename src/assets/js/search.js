$(function () {
  var show_user_seeds = function(container, user_id) {
    $('.results_general').find('tbody tr').removeClass('active');
    $.get("/seedbank/user_seeds/" + user_id, function (data) {
      if (! data.seeds){
        return false;
      }
      $('.results_detail tbody').empty();
      $('.results_detail h3 span').text(data.user.name).data('user_id', data.user.id);
      $.each(data.seeds, function (i, info){
        //console.log(info);
        $('.results_detail tbody').append('<tr>\
          <td><input name="seed_id" value="' + info.id + '" type="checkbox"/></td>\
          <td>' + info.common_name + '</td></tr>');
        if ( info.parent_id) {
          $('.results_detail tbody tr:last input').attr('disabled', '');
          if (( info.accepted == 2) && (info.completed == 0)) {
            $('.results_detail tbody tr:last').append('<td>' + Lang.get('buttons.waitingforconfirmation') + '</td>');
          } else if (( info.accepted == 0) && (info.completed == 0)){
            $('.results_detail tbody tr:last').append('<td>' + Lang.get('buttons.waitingforreply') + '</td>');
          } else if ( info.accepted == 1){
            $('.results_detail tbody tr:last').append('<td>' + Lang.get('buttons.refused') + '</td>');
          } else if ( info.completed == 1) {
            $('.results_detail tbody tr:last').append('<td>' + Lang.get('buttons.canceled') + '</td>');
          }
        } else {
          $('.results_detail tbody tr:last').append('<td></td>');
        }
        $('.results_detail tbody tr:last').append('<td>\
          <button type="button" data-seed_id="' + info.id + '" class="btn btn-primary btn-sm">\
          ' + Lang.get('buttons.moreinfo') + '\
          </button></td>');
      });
    },
    'json');
    $(container).addClass('active');
  };

  $(".container form").on('submit', function(){
    $('#results tbody').empty();
    if ( $('#results').is(':visible')) {
      $('#results').hide();
      $('#results').hide();
      $('.container form').show();
      return false;
    };
    var formdata = $('form').serialize();
    $.post("/seedbank/search", formdata, function (data) {
      //console.log(data);
      if (! data){
        return false;
      }
      $.each( data, function(i,info){
        $('.results_general tbody').append('<tr data-user_id="' + info.user_id + '" data-seed_id="' + info.id + '">\
          <td>' + info.common_name + '</td>\
          <td>' + info.latin_name + '</td</tr>');
        if ( i == 0 ){
          show_user_seeds($('.results_general tbody tr'), info.user_id);
        }
        $('#results').show();
        $('.container form').hide();
      });
    },
    'json');
    return false;
  });

  $('.results_general').on('click', 'tbody tr', function () {
    show_user_seeds(this, $(this).data('user_id'));
  });

  $('.results_detail').on('click', 'tbody tr button', function () {
    var seed_id = $(this).data('seed_id');

    $.get("/seedbank/seedm/" + seed_id, function (data) {
      if (data.length == 0){
        return false;
      }
      $('#message-display .modal-content').empty();
      $('#message-display .modal-content').append(data);

      $('#message-display').modal('show');
      var element = $('.results_detail table input[value=' + seed_id + ']');
      if (!! element.first().attr('disabled')) {
        $('#message-display .modal-footer .footer-left')
        .html('<div class="label label-warning col-md-12">\
              ' + Lang.get('buttons.alreadypending') + '</div>');
      } else {
        $('#message-display .modal-footer .footer-left')
        .html('<button class="btn btn-sm btn-default col-sm-12">\
              ' + Lang.get('buttons.chooseseed') + '</button>');
      }
    });
  });

  $('#message-display').on('click', '.modal-footer .footer-left button', function(){

    var seed_id = $('#message-display .modal-title').data("seed_id");

    $('.results_detail table input[value=' + seed_id + ']').prop("checked", true);
    $('#message-display').modal('hide');


  });
  $('.body-form').on('click', '#cancel', function(){
    $('.body-form').empty().hide();
  });
  $('.results_detail').on('click', '#newsearch', function(){
    $(".container form").trigger('submit');
  });
  $('.results_detail').on('click', '#startexchange', function(){
    var chosen = [];
    var user_id = $('.results_detail h3 span').data('user_id');
    $.each($(this).next('table').find('input:checked'), function(i, choice){
      chosen.push($(choice).val());
    });

    //console.log(chosen);
    //console.log(user_id);
    $.post("/seedbank/startexchange", {"seed_ids": chosen, "user_id":user_id},  function (data) {
      //console.log(data.length);
      if (! data.length){
        //alert('Empty Response');
        return false;
      }
      //console.log(data);
      //alert(data);

      window.open('/seedbank/exchanges', '_self');
    });
  });
});
