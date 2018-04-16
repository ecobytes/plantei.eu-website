$(function (){
  $('.table').on('click', 'tbody tr button', function () {
    var action = $(this).data('button'),
    exchange_id = $(this).parents('tr').data('exchange');

    $.get("/seedbank/exchange/" + action + "/" + exchange_id, function (data) {
      if (data == "ok") { window.open('/seedbank/exchanges', '_self'); }
    });
  }).on('mouseover', 'tbody tr', function () {
    $(this).addClass('active');
  }).on('mouseout', 'tbody tr', function () {
    $(this).removeClass('active');
  });
});
