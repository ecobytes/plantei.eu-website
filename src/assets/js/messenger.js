$( function () {
  $('#newMessage').on('click', function (e) {
    //alert('button');
    $('.modal-content').html('<img src="/js/tinymce/skins/lightgray/img/loader.gif"/>');
    $('.modal').modal('show');
    //alert();
    $.get('/messages/create', function (data) {
      $('.modal-content').html(data);
    });
  });

  $('#newContact').on('click', function (e) {
    $('.addContact').html('<input class="input" name="newContact" autocomplete="off" style="color:black;"/>\
        <button type="button" class="btn btn-primary">Add</button>').show();
    $('.addContact button').on('click', function(e) {
      var newContact = $('.addContact input').val();
      $('.addContact').empty().hide();
      if (! newContact ) { return false; }
      var fd = { 'newContact': newContact};
      $.post('/api/contacts/add', fd, function (data) {
        if ( data.id ) {
          $('#contactList').prepend('<li>'+data.name+'</li>');
        }

      });
    });

  });

  $('.table').on('click', 'tbody tr', function () {
    $('.modal-content').html('<img src="/js/tinymce/skins/lightgray/img/loader.gif"/>');
    $('.modal').modal('show');
    var threadId = $(this).data('thread_id');
    console.log($(this).data());
    $.get('/messages/' + threadId, function (data) {
      $('.modal-content').html(data);
    });
  }).on('mouseover', 'tbody tr', function () {
    $(this).addClass('active');
  }).on('mouseout', 'tbody tr', function () {
    $(this).removeClass('active');
  });
});
