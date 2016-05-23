$(function () {

  var districts = [];
  var cities = [];

  $.get("/api/location", function (data) {
    districts = data;
  });

  $("[rel='tooltip']").tooltip();
  $('.time').timepicker({timeFormat: "G:i"});
  $('.date').datepicker({minDate: -1, dateFormat: "yy-mm-dd"});
  $( ".district" ).autocomplete({
    source: function( request, response ) {
      var r = [];
      var term = request.term;
      $.each(districts,function(index, value){
        if (value.toLowerCase().indexOf(term.toLowerCase()) >= 0) {
          r.push(value);
        }
      });
      response(r)
    },
    response: function(event, ui) {
      if (ui.content.length == 1)
      {
        var v = ui.content[0].value
        $(this).val(v);
        //$(this).autocomplete( "close" );
        $('.city').prop('disabled', false).focus();
        $.get("/api/location/" + v, function (data) {
          cities = data;
        });
        $('.city').focus();
      }
    },
  });
  $( ".city" ).autocomplete({
    source: function( request, response ) {
      var r = [];
      var term = request.term;
      $.each(cities,function(index, value){
        if (value.toLowerCase().indexOf(term.toLowerCase()) >= 0) {
          r.push(value);
        }
      });
      response(r);
    },
    response: function(event, ui) {
      if (ui.content.length == 1)
      {
        $(this).val(ui.content[0].value);
        //$(this).autocomplete( "close" );
        $('textarea').focus();
      }
    },
  });
  $('.district').on('change', function (e) {
    $('.city').val("");
    var district = $(e.target).val();
    if ( $.inArray(district, districts) !== -1) {
      $('.city').prop('disabled', false).focus();
      $.get("/api/location/" + district, function (data) {
        cities = data;
      });
    } else {
      $('.city').prop('disabled', true);
      $('.city').val("");
      cities = []
    }
  });
  $('.city').on('change', function (e) {
    var city = $(e.target).val();
    if ( $.inArray(city, cities) == -1) {
      $('.city').val("");
    }
  });
  // Form Validation
  $('.modal form').on('submit', function(e)
  {
    var $form = $(this);
    e.preventDefault(); //keeps the form from behaving like a normal (non-ajax) html form
    var url = $form.attr('action');
    /*var formData = {};
    //submit a POST request with the form data
    $form.find('input', 'select').each(function()
    {
        formData[ $(this).attr('name') ] = $(this).val();
    });*/
    var formData = $form.serialize();
    //submits an array of key-value pairs to the form's action URL
    $.post(url, formData, function(response)
    {
        //handle successful validation
      $('#event_info').modal('toggle');
      $('#mycalendar').fullCalendar('refetchEvents');
      return false;

    }).fail(function(response)
    {
        //handle failed validation
        associate_errors(response.responseJSON, $form);
    });
      return false;
  });
  function associate_errors(errors, $form)
{
  $('.alert-danger').remove();
  errorsHtml = '<div class="alert alert-danger"><ul>';
  $.each( errors, function( key, value ) {
    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
  });
  errorsHtml += '</ul></div>';
  $('.modal-body').prepend(errorsHtml)
  $('#event_info').animate({ scrollTop: 0});
    /*//remove existing error classes and error messages from form groups
    $form.find('.form-group').removeClass('has-errors').find('.help-text').text('');
    errors.foreach(function(value, index)
    {
       //find each form group, which is given a unique id based on the form field's name
        var $group = $form.find('#' + index + '-group');

        //add the error class and set the error text
        $group.addClass('has-errors').find('.help-text').text(value);
    }*/
    console.log(errors);
}

});

