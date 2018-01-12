
$( function () {


  let parameters = [
    {"name": "seed_id", "value": "id"},
    {"name": "_id", "value": "id"},
    {"name": "common_name", "value": "common_name"},
    {"name": "local", "value": "local"},
    {"name": "year", "value": "year"},
    {"name": "public", "value": "public", "type": "checked"},
    {"name": "description", "value": "description", "type": "textarea"},
    {"name": "pictures[]", "value": "pictures[]", "type": "file" },
    {"name": "latin_name", "value": "latin_name"},
    {"name": "species", "value": "species.name"},
    {"name": "species_id", "value": "species.id"},
    {"name": "family", "value": "family.name"},
    {"name": "family_id", "value": "family.id"},
    {"name": "variety", "value": "variety.name"},
    {"name": "variety_id", "value": "variety.id"},
    {"name": "months[]", "value": "months[].month", "type": "checked"},
    {"name": "available", "value": "available", "type": "checked"},
    {"name": "polinization", "value": "polinization", "type": "checked"},
    {"name": "direct", "value": "direct", "type": "checked"}
  ];

  $('tbody tr').on('click', function () {
    var seed_id = $(this).data('seed_id');
    $.get("/seedbank/seedm/" + seed_id, function (data) {
      if (data.length == 0){
        return false;
      }
      clearform();
      populateform(parameters, data);
      initRegisterSeed();
      previewseed(parameters, data);
      $('#seed-preview').show();
      $('form').hide();

      $('#modal').modal('show');
    });
  }).mouseover(function () {
    $(this).addClass('active');
  }).mouseout(function () {
    $(this).removeClass('active');
  });

  $('#newseed').on('click', function () {
    clearform();
    $('#seed-preview').hide();
    $('form').show();
    initRegisterSeed();
    $('#modal').modal('show');
  });

  $('#modal').on('click', '#seed-preview button', function(){
    $('#seed-preview').hide();
    initRegisterSeed();
    $('form').show();
  });

  $('#cancel_seed').on('click', function () {
    $('#modal').modal('hide');
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
  var initRegisterSeed = function () {
    //console.log("registerseed");
    /*$('#cancel_seed').on('click', function () {
      //window.open('/seedbank/myseeds', '_self');
      $('#modal').modal('hide');
      return false;
    });*/
    $('#fileupload').off();
    try {
      // FIXME: Have to destroy fileupload and recreate
      // for duplicate eventlisteners ? (is there other way?)
      $('#fileupload').fileupload('destroy');
    }
    catch (error) {
      //console.log('catched error');
    };
$('form').on('submit', function (){
      element = $('input[name="common_name"]');
      if (! element.val()) {
        $('#identification').collapse('show');
        element.closest('.form-group').addClass('has-error');
        element.next('.help-block').text('This field is the only field required');
        element.focus();
        return false;
      }
    });
    //Picture uploader
    //var confirmed = false;
    var deletebuttontext = Lang.get('seedbank::messages.delete');
    $("form").on('submit', function () {
      // if (confirmed) { return true };
      tinymce.triggerSave();
      var formdata = $(this).serializeArray();
      $.post('/seedbank/register', formdata, function(data) {

        /*$("#seed-preview").html(data).show();
        $.each($('#files').children('.col-md-4').not('.processing'), function (index, elem){
          if ($(elem).find('img').length) {
            $("#seed-preview .pictures").append($(elem).clone()
                .removeClass('col-md-4').addClass('col-md-4'))
              .find('button').remove();
          }
        });*/
        previewseed(parameters, data);

        $("form").hide();
        $("#seed-preview").show();

        $("#seed-preview").on('click', 'button.btn-danger', function(e) {
          $("form").show();
          $("#seed-preview").unbind().empty().hide();
        /*}).on('click', 'button.btn-primary', function(e) {
          confirmed = true;
          $("form").append('<input type="hidden" name="confirmed" value="1"/>').submit();
        */
        });
      });
      return false;
    });
/*    $('#files').on('click', 'button.delete', function(){
      console.log('global delete button');
      var file = $(this).data(),
      that = this;
      console.log(file);
      $.getJSON(file.deleteUrl, function (data){
        if (data.files[0][file.md5sum]){
          that.closest('.col-md-4').remove();
          upload_counter = upload_counter - 1;
        }
      });
      $.each($('#files').children('.col-md-4').not('.processing'), function (index, elem){
        if (! $(elem).find('img').length) {
          $(elem).closest('.col-md-4').remove();
        }
      });
    }); */
    // Change this to the location of your server-side upload handler:
    var url = '/seedbank/add-pictures',
    deleteButton = $('<button/>')
      .addClass('btn btn-danger delete').prop('type', 'button')
      .text(deletebuttontext)
      .on('click', function () {
        /*console.log('this');
        console.log(this);
        console.log($(this));
        console.log($(this).data());*/

        var $this = $(this),
        file = $this.data().result.files[0];
        $.getJSON(file.deleteUrl, function (data){
          if (data.files[0][file.md5sum]){
            $this.closest('.col-md-4').remove();
            upload_counter = upload_counter - 1;
          }
        });
        $.each($('#files').children('.col-md-4').not('.processing'), function (index, elem){
          if (! $(elem).find('img').length) {
            $(elem).remove();
          }
        });
        $this
          .off('click')
          .text('Abort')
          .on('click', function () {
            //$this.remove();
            data.abort();
          });
      });
    $('#fileupload').fileupload({
      url: url,
      dataType: 'json',
      autoUpload: true,
      acceptFileTypes: /(\.|\/)(gif|jpe?g|png|tif?f)$/i,
      maxFileSize: 999000,
      maxNumberOfFiles: 5,
      getNumberOfFiles: function () {
        return $('#files').find('button.delete').length;
      },
      // Enable image resizing, except for Android and Opera,
      // which actually support image resizing, but fail to
      // send Blob objects via XHR requests:
      disableImageResize: /Android(?!.*Chrome)|Opera/
        .test(window.navigator.userAgent),
      previewMaxWidth: 150,
      previewMaxHeight: 150,
      previewCrop: true,
      //multipart: false
    }).on('fileuploadsubmit', function (e, data){
      /*console.log('submit');
      console.log(data);*/

      var seed_id = $("form input[name='seed_id']").val();
      if (seed_id) {
        data.formData = {"seed_id": seed_id};
      } else {
        data.formData = {};
      }
    }).on('fileuploadadd', function (e, data) {
      /*console.log('uploadadd; counter: ' + upload_counter);
      console.log(data);*/
      data.context = $('<div/>').addClass('col-md-4').css("padding-bottom", "24px").appendTo('#files');
      /*$.each(data.files, function (index, file) {
        var node = $('<p/>');
        /*if (!index) {
          node
          .append('<br>')
          .append(uploadButton.clone(true).data(data));
          }*
        node.appendTo(data.context);
      });*/
    }).on('fileuploadprocessalways', function (e, data) {
      /*console.log('processalways');*/
      var index = data.index,
      file = data.files[index],
      node = $(data.context.children()[index]);
      if (file.preview) {
        node
          .prepend('<br>')
          .prepend(file.preview);
      }
      if (file.error) {
        node
          .append('<br>')
          .append($('<span class="text-danger"/>').text(file.error));
      }
      if (index + 1 === data.files.length) {
        data.context.find('button')
          .text('Upload')
          .prop('disabled', !!data.files.error);
      }
    }).on('fileuploadprogressall', function (e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $('#progress .progress-bar').css(
          'width',
          progress + '%'
          );
    }).on('fileuploaddone', function (e, data) {
      /*console.log('done');
      console.log(data);
      console.log(upload_counter);*/
      $.each(data.result.files, function (index, file) {
        /*console.log(file);*/
        if (file.url) {
          upload_counter += 1;
          $.each($('#files').children('.col-md-4'), function (index, elem) {
            var elem_img = $(elem).find('img');
            if (elem_img.length) {
              if ($(elem_img[0]).data('file-id') == file.id) {
                //console.log('file_id exists; counter: ' + upload_counter);
                $(elem_img[0]).closest('.col-md-4').remove();

                upload_counter = upload_counter - 1;
                //console.log('file_id exists; counter: ' + upload_counter);
                return false;

              }
            }
          });
          if (upload_counter > $("#fileupload").fileupload('option', 'maxNumberOfFiles')) {
            $(data.context.children()[index]).closest('.col-md-4').remove();
            $.getJSON(file.deleteUrl, function (){
              upload_counter = upload_counter - 1;
            });
            return false;
          }
          //console.log('create hidden input image');
          //console.log(data.context);
          var hidden_input = '<input type="hidden" name="pictures_id[]" value="' + file.id + '">';
          var image = $('<img class="img-responsive img-rounded cell" data-file-id="'
            + file.id + '" src="' + file.url + '" alt="' + file.label + '" />');
          /*var link = $('<a>')
            .attr('target', '_blank')
            .prop('href', file.url);*/
          data.context.empty().prepend(hidden_input).prepend(image).append(deleteButton.clone(true)
              .data(data));
          $('#files').append(data.context);
          //.wrap(link);
        } else if (file.error) {
          var error = $('<span class="text-danger"/>').text(file.error);
          $(data.context.children()[index])
            .append('<br>')
            .append(error);
        }
        //console.log('done_end; counter:' + upload_counter);
      });
    }).on('fileuploadfail', function (e, data) {
      $.each(data.files, function (index) {
        var error = $('<span class="text-danger"/>').text('File upload failed.');
        $(data.context.children()[index])
          .append('<br>')
          .append(error);
      });
    }).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled');
    var upload_counter = $('#files .col-md-4').length;
  }

});
