$(function () {
  var showevent_id = parseInt(findGetParameter('id'));

  var districts = [];
  var cities = [];

  $('#modal').on('hidden.bs.modal', function () {
    // Cleanup modal content on close
    $(this).find('.modal-title').text('');
    $('#event-preview').hide();
    $('#event-form').hide();
  })

  $.get("/api/location", function (data) {
    districts = data;
  });

  $("[rel='tooltip']").tooltip();

  $('.time').timepicker({timeFormat: "H:i"});
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
        $('.council').prop('disabled', false).focus();
        $.get("/api/location/" + v, function (data) {
          cities = data;
        });
        $('.council').focus();
      }
    },
    appendTo: "#modal",
  });
  $( ".council" ).autocomplete({
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
    appendTo: "#modal",
  });
  $('.district').on('change', function (e) {
    $('.council').val("");
    var district = $(e.target).val();
    if ( $.inArray(district, districts) !== -1) {
      $('.council').prop('disabled', false).focus();
      $.get("/api/location/" + district, function (data) {
        cities = data;
      });
    } else {
      $('.council').prop('disabled', true);
      $('.council').val("");
      cities = []
    }
  });

  $('.council').on('change', function (e) {
    var city = $(e.target).val();
    if ( $.inArray(city, cities) == -1) {
      $('.council').val("");
    }
  });
  $('form input[name=start-date]').on('change', function (e) {
    $('form input[name=end-date]').val($('form input[name=start-date]').val());
  });

  // Form Validation
  $('#modal form').on('submit', function(e)
  {
    var $form = $(this);
    tinymce.triggerSave();
    e.preventDefault(); //keeps the form from behaving like a normal (non-ajax) html form

    var getError = function (a,b,c,d) {
      let parameters = {};
      if (c) {
        parameters['attribute'] = c;
        if (d) {
          parameters[a] = d;
        }
      }
      return [Lang.get(b, parameters)];
    }

    var url = $form.attr('action');

    let start = moment(
      $form.find('input[name=start-date]').val() + ' ' +
      $form.find('input[name=start-time]').val(),
      'YYYY-MM-DD hh:mm'
    );
    let end = moment(
      $form.find('input[name=end-date]').val() + ' ' +
      $form.find('input[name=end-time]').val(),
      'YYYY-MM-DD hh:mm'
    );


    let errors = {};

    if ( end > start ) {
      $form.find('input[name=start]').val(start.format());
      $form.find('input[name=end]').val(end.format());
    } else {
      errors['date'] = getError(
        'date',
        'validation.before',
        Lang.get('seedbank::messages.startdate'),
        Lang.get('seedbank::messages.enddate')
      );
    }

    if ( ! $form.find('input[name=title]').val() ) {
      errors['title'] = getError(
        'title',
        'validation.required',
        Lang.get('seedbank::messages.title')
      );
    }

    if ( Object.keys(errors).length ) {
      formErrors(errors);
      return false;
    }


    var formData = $form.serialize();
    //submits an array of key-value pairs to the form's action URL
    $.post(url, formData, function(response)
    {
      //handle successful validation
      if (response.id) {
        console.log("saved with with: " + response.id);
        $(".row.validationErrors").empty();
      }
      $('#modal').modal('toggle');
      $('#mycalendar').fullCalendar('refetchEvents');
      return false;

    }).fail(function(response)
    {
        //handle failed validation
        formErrors(response.responseJSON, $form);
        $('html,body').animate({
          scrollTop: $(".row.validationErrors").offset().top
        }, 'slow');
    });

    return false;
  });

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

  var eventPreview = function(event, element) {
    if (! element) {
      element = $('#event-preview');
    }
    eventForm(event, element);
    $.each(["title", "start", "end", "location", "address", "postal"], function (i, d) {
      element.find('[data-name="' + d + '"]').text(event[d]);
    });
    element.find('[data-name=description]').html(event['description']);
    if ( event.user_id == user_id) {
      $('#event-preview button').show();
      $('#event-preview button').on('click', function (e) {
        $('#event-form button[type=submit]').text(Lang.get('seedbank::messages.update'));
        $('#event-preview').hide();
        $('#event-form').show();
      });
    } else {
      $('#event-preview button').hide();
      $('#event-preview button').unbind();
    }
  }

  var eventForm = function(event, element) {

    if (! element) {
      element = $('#event-form');
    }

    var submitText = Lang.get('seedbank::messages.send');
    var titleText = Lang.get('seedbank::messages.newevent');

    if ( event.title ) {
      submitText = Lang.get('seedbank::messages.update');
      titleText = '';
      $.each(["title", "location", "address", "postal", "id"], function (i, d) {
          element.find('input[name="' + d + '"]').val(event[d]);
      });
      tinyMCE.activeEditor.setContent(event.description);
    } else {
      $.each(["title", "location", "address", "postal", "id"], function (i, d) {
        element.find('input[name="' + d + '"]').val('');
        tinyMCE.activeEditor.setContent('');
      });
    }

    $.each(["start", "end"], function (i, d) {
      let date = event[d];
      element.find('input[name="' + d + '"]').val(date.format());
      element.find('input[name=' + d + '-date]').val(date.format('YYYY-MM-DD'));
      element.find('input[name=' + d + '-time]').val(date.format('HH:mm'));
    });

    $('form button.btn-danger').on('click', function (e) {
      $('#modal').modal('hide');
    });

    $('#event-form button[type=submit]').text(submitText);
    $('#modal .modal-title').text(titleText);
    $('#event-preview').hide();
    $(".row.validationErrors").empty();
  }

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
        var event = $("#mycalendar").fullCalendar('clientEvents', showevent_id)[0];
        if (event) {
          eventPreview(event);

          $('#event-preview').show();
          $('#event-form').hide();
          $('#modal').modal('show');
        }
        showevent_id = null;
      };
    },
    eventClick: function(calEvent, jsEvent, view) {
      eventPreview(calEvent);

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

      let startend = {
        'start': new moment(date.hours(9)),
        'end': new moment(date.add(4, 'hour'))
      };
      eventForm(startend);


      $.each(['start', 'end'], function (i, d){
        $('#event-form').find('input[name=' + d +']').val(startend[d].format('YYYY-MM-DD HH:mm'));
        $('form input.date[name=' + d +'-date]').val(startend[d].format('YYYY-MM-DD'));
        $('form input.time[name=' + d +'-time]').val(startend[d].format("HH:mm"));
      });

      $('#event-preview').hide();
      $('#event-form').show();
      $('#modal').modal('show');
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
