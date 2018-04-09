$(function () {
  $("[rel='tooltip']").tooltip();

  $("#login").on('click', function(e){
    e.preventDefault();
    $("#loginform").show();
    $("#registerform").hide();
    $('#modal').modal('show');
  });

  $("#register").on('click', function(e){
    e.preventDefault();
    $("#registerform").show();
    $("#loginform").hide();
    $('#modal').modal('show');
  });

});
