$( function () {
  $('#mySeeds tbody').on('click', 'tr', function () {
    window.open('/seedbank/register/' + $(this).data('seed_id'), '_self');
    return false;
  }).on('mouseover', 'tr', function () {
    $(this).addClass('active');
  }).on('mouseout', 'tr', function () {
    $(this).removeClass('active');
  });
  $('#new_seed').on('click', function () {
    window.open('/seedbank/register', '_self');
    return false;
  });
  $('.pageWrap nav').on('click', 'a', function () {
    var url = $(this).data('url');
    $.get(url, function(data){
      $('tbody').empty();
      $.each(data.data, function(i, item){
        var public = ''; var available = '';
        if (item.public) { public = 'X' };
        if (item.available) { available = 'X' };
        $('tbody').append('<tr data-seed_id="' + item.id +'">\
            <td>' + item.common_name + '</td>\
            <td>' + item.latin_name + '</td>\
            <td style="text-align: center;">' + available + '</td>\
            <td style="text-align: center;">' + public + '</td>\
            </tr>');
      });
      var buttons = '';
      if (data.prev_page_url) {
        buttons += '<li class="previous"><a style="color: black !important;" data-url="' + data.prev_page_url + '" class="btn btn-primary btn-md">\
        ' + Lang.get('paginate.previous') + '\
        </a></li>';
      }
      if (data.next_page_url) {
        buttons += '<li class="next"><a style="color: black !important;" data-url="' + data.next_page_url + '" class="btn btn-primary btn-md">\
        ' + Lang.get('paginate.next') + '\
        </a></li>';
      }
      if (buttons != '') {
        $('.pageWrap nav ul').html(buttons);
      }
    });
  });
});
