$(function () {
  $('#seeds tr').on('click', function () {
    var seed_id = $(this).data('seed_id');
    window.open('/seedbank/allseeds?seed_id=' + seed_id, '_self')
  }).mouseover(function () {
    $(this).addClass('active');
  }).mouseout(function () {
    $(this).removeClass('active');
  });

  $('#mySeeds tr').on('click', function () {
    var seed_id = $(this).data('seed_id');
    window.open('/seedbank/myseeds?seed_id=' + seed_id, '_self')
  }).mouseover(function () {
    $(this).addClass('active');
  }).mouseout(function () {
    $(this).removeClass('active');
  });

  $('.listEvents').on('click', 'li', function (e) {
    $.get('/events/get/' + $(this).data('id'), function (data) {
      if (! data ) { return false;}
      $(".modal-content").html(data);
      $('#info_modal').modal('show');
    });
  });

  $('#messages tr').on('click', function () {
      var messageId = $(this).data('message_id') || '';
      window.open('/messages/?message_id=' + messageId, '_self');
    }).mouseover(function () {
      $(this).addClass('active');
    }).mouseout( function () {
      $(this).removeClass('active');
    });

    console.log(lang);
  $('#calendar-horta').fullCalendar({
    // put your options and callbacks here
    defaultView: 'month',
    header: { left: false, right: false, center: "prev title next"},
    themeSystem: 'bootstrap3',
    contentHeight: 'auto',
    fixedWeekCount: false,
    locale: lang,
    timeFormat: 'HH:mm',
    viewRender: function (view, el) {
      $.each($("#calendar-horta .fc-content-skeleton thead td"),
        function (index, el) {
          //if ( ! $(el).hasClass('fc-other-month') ) {
            let dd = $(el).data('date').split('-');
            let image = getMoonPhase(parseInt(dd[0]), parseInt(dd[1]), parseInt(dd[2]));
            el.innerHTML += '<br/><img src="' + image + '" width=25px>';
          //}
        }
      );
    },

    eventClick: function(calEvent, jsEvent, view) {
      console.log(calEvent);
      $.get("/events/get/" + calEvent.id, function (data) {
        if (data.length == 0){
          return false;
        }
        $('#event_info .modal-content').empty();
        $('#event_info .modal-content').append(data);
        $('#event_info').modal('show');
      });
    },
    eventSources: [
      {
        url: '/api/calendar',
        type: 'POST',
        color: 'yellow',
        textColor: 'black'
      }
    ],
  });
});
