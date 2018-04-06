var showevent_id = parseInt(findGetParameter('id'));
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
  $('#modal form').on('submit', function(e)
  {
    var $form = $(this);
    tinymce.triggerSave();
    e.preventDefault(); //keeps the form from behaving like a normal (non-ajax) html form
    var url = $form.attr('action');
    var formData = $form.serialize();
    //submits an array of key-value pairs to the form's action URL
    $.post(url, formData, function(response)
    {
      //handle successful validation
      $('#modal').modal('toggle');
      $('#mycalendar').fullCalendar('refetchEvents');
      return false;

    }).fail(function(response)
    {
        //handle failed validation
        formErrors(response.responseJSON, $form);
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
  }

  tinymce.init({
    selector: 'textarea',
    inline: false,
    menubar: 'tools',
    width: '100%',
    plugins: [ "placeholder" ],
    setup : function(ed)
    {
      ed.on('init', function()
        {
          this.getDoc().body.style.fontSize = '18px';
          $('.mce-toolbar-grp').hide();
          $('.mce-statusbar').hide();
          $(this.getBody()).on('blur', function() {
              $('.mce-toolbar-grp').hide();
              $('.mce-statusbar').hide();
          });
          $(this.getBody()).on('focus', function() {
              $('.mce-toolbar-grp').show();
              $('.mce-statusbar').show();
          });
      });
    }
  });

  $('#mycalendar').fullCalendar({
    // put your options and callbacks here
    //defaultView: 'agendaWeek',
    defaultView: 'month',
    header: { left: 'prev title next today', right: false, center: false},
    themeSystem: 'bootstrap3',
    //contentHeight: 500,
    fixedWeekCount: false,
    lang: lang,
    //aspectRatio: 1.3,
    eventLimit: 3,
    timeFormat: 'HH:mm',
    viewRender: function (view, el) {
      $.each($("#mycalendar .fc-content-skeleton thead td"),
        function (index, el) {
          if ( ! $(el).hasClass('fc-other-month') ) {
            let dd = $(el).data('date').split('-');
            let image = getMoonPhase(parseInt(dd[0]), parseInt(dd[1]), parseInt(dd[2]));
            el.innerHTML = '<img src="' + image + '" width=15px>' + el.innerHTML;
          }
        }
      );
    },
    eventRender: function(event, element) {
      $(element).tooltip({title: event.title});
    },
    eventAfterAllRender: function( view ) {
      // show event if available
      if (showevent_id) {
        //showevent_id = null;
        var event = $("#mycalendar").fullCalendar('clientEvents', showevent_id)[0];
        if (event) {
          $.each(["title", "description", "start", "end", "location", "address", "postal"], function (i, d) {
            $('#event-preview').find('[data-name="' + d + '"]').text(event[d]);
          });
          $('#event-preview').show();
          $('#event-form').hide();
          $('#modal').modal('show');
        }
      };
    },
    eventClick: function(calEvent, jsEvent, view) {
      $.each(["title", "description", "start", "end", "location", "address", "postal"], function (i, d) {
        $('#event-preview').find('[data-name="' + d + '"]').text(calEvent[d]);
      });
      $('#event-preview').show();
      $('#event-form').hide();
      $('#modal').modal('show');
    },
    dayClick: function (date, ev, view){
      if ( ( ! ( $(ev.target).hasClass('fc-today') ||
          $(ev.target).hasClass('fc-future') ) )
          || ( $(ev.target).hasClass('fc-other-month')) )  {
        return;
      }

      $('#event-form').find('input[name=start]').val(date.format('YYYY-MM-d hh:mm'));
      $('#event-form').find('input[name=end]').val(date.add(4, 'hour').format('YYYY-MM-d hh:mm'));


      $('#event-preview').hide();
      $('#event-form').show();
      $('#modal').modal('show');
      return;
    },
    eventSources: [
    {
      url: '/api/calendar',
      type: 'POST',
      //color: 'yellow',
      textColor: 'black'
    }
    ],
  });
});
