/*
 * from here https://gist.github.com/endel/dfe6bb2fbe679781948c
 */

function getMoonPhase(year, month, day)
{
    var c = e = jd = b = 0;
    if (month < 3) {
        year--;
        month += 12;
    }
    c = 365.25 * year;
    e = 30.6 * month;
    jd = c + e + day - 694039.09; //jd is total days elapsed
    jd /= 29.5305882; //divide by the moon cycle
    b = parseInt(jd); //int(jd) -> b, take integer part of jd
    jd -= b; //subtract integer part to leave fractional part of original jd
    b = Math.round(jd * 8); //scale fraction from 0-8 and round
    if (b >= 8 ) {
        b = 0; //0 and 8 are the same so turn 8 into 0
    }
    // 0 => New Moon
    // 1 => Waxing Crescent Moon
    // 2 => Quarter Moon
    // 3 => Waxing Gibbous Moon
    // 4 => Full Moon
    // 5 => Waning Gibbous Moon
    // 6 => Last Quarter Moon
    // 7 => Waning Crescent Moon
    b = b + 1; // for images names
    return "/images/moons/1f31" + b + ".svg"
}


/*
 * load seed preview data
 */

 let getFieldValue = function(p, data) {
   // {"name": "species", "value": "species.name"},
   var fields = p.value.split('.');

   if ( fields.length === 1 ) {
     if (! data[fields[0]]) {
       return "";
     }
     return data[fields[0]];
   };

   if ( fields.length > 1 ) {
     if ( fields[0].indexOf('[]') !== -1 ) {
       fields[0] = fields[0].replace('[]', '');
       if (! data[fields[0]]) {
         return "";
       }
       var arrayValues = [];
       data[fields[0]].forEach(function (e) {
         arrayValues.push(e[fields[1]]);
       });
       return arrayValues.sort();
     };
     if ( ! data[fields[0]] ) {
       return "";
     }
   }
   return data[fields[0]][fields[1]];
 };


var previewseed = function (parameters, data){

  let pdiv = $('#seed-preview');

  let setupMonths = function (){
    let monthsTr = pdiv.find('tbody td');

    monthsTr.each(function(i, el){
      $(el).removeClass('active');
    });

    if (data.months.length) {
      pdiv.find('[data-name="months"]').parent('div').show();
      data.months.forEach(function (month) {
        $(monthsTr[parseInt(month.month) - 1]).addClass('active');
      });
    } else {
      pdiv.find('[data-name="months"]').parent('div').hide();
    }

  };

  let setupCover = function (v) {
    if ( v.length == 0 ){
      v = [{id: null, url: '/images/planteieulogocinza.png', label: ''}];
    }
    console.log(v);
    img = v[0];
    let image = '<img class="img-responsive img-rounded cell" data-file-id="' +
      img.id + '" src="' + img.url + '" alt="' + img.label + '" />';
    /*let pictureDiv = '<div class="col-md-4 cover">' +
      image + '</div>';*/
    $('.cover').append(image);
  };

  let setupField = function (v) {
    if ( (v.type != 'textarea') && ( v.type) ) {
      if (v.type == 'file' ) {
        $('.pictures').empty().hide();
        $('.cover').empty();
        if (! data[v.value.replace('[]', '')]) {
          return false;
        }
        setupCover(data[v.value.replace('[]', '')]);
        data[v.value.replace('[]','')].forEach(
          function(img){
            var image = '<img class="img-responsive img-rounded cell" data-file-id="'
            + img.id + '" src="' + img.url + '" alt="' + img.label + '" />';
            var pictureDiv = '<div class="col-md-4">' +
              image + '</div>';
            $('.pictures').append(pictureDiv).show();
        });
      }
      //console.log(v.name);
      if ( v.name == 'months[]' && data.months ) {
        setupMonths();
      }
      if (! data.months) {
        pdiv.find('[data-name="months"]').parent('div').hide();
      }
      return;
    }
    if (v.name == 'title') {
      $("#modal").find('.modal-title').text(getFieldValue(v, data));
      return;
    }


    let tag = pdiv.find('[data-name="' + v.name + '"]');
    let fieldValue = getFieldValue(v, data);
    if ( ! fieldValue) {
      tag.parent(tag.data('parent')).hide();
    } else {
      tag.parent(tag.data('parent')).show();
    }

    if ( /<[a-z][\s\S]*>/i.test(fieldValue) ) {
      tag.html(fieldValue);
    } else {
      tag.text(fieldValue);
    }
  }

  $.each(parameters, function (i, v) {
    setupField(v);
  });

}

/*
 * work with forms
 */

var clearform = function() {
  //console.log('clearform');
 //$("#modal").find('.modal-header').show().find('.modal-title').text(Lang.get("seedbank::messages.add_new_seed"));
 $('form .form-group.has-error').removeClass('has-error').find('.help-block').empty()
 if (! $('#identification').hasClass('in')){
   $('#identification').collapse('show');
 };

 $('form').find('input').each(function (i, el) {
   if ( el.name == '_token') {
     return;
   }
   if ( (el.type == 'checkbox') ||
        (el.type == 'radio') ) {
     let $parent = $(el).closest('label');
     //console.log(el.type);
     $(el).prop('checked', false);
     $parent.removeClass('active');
     return;
   }
   if (el.type == 'file') {
     $('#fileupload').off();
     $('#files').empty();
     return;
   }
   $(el).val("");
 });
 $('form').find('textarea').each(function (i) {
   tinymce.activeEditor.setContent("");
   $(this).val("");
 });
};

var populateform = function(parameters, data) {
  //if (data.common_name){
  //  $("#modal").find('.modal-header').show().find('.modal-title').text(Lang.get("seedbank::messages.change") + " - " + data.common_name);
  //}

  let setCheckbox = function (n, v) {
    //console.log(n);
    //console.log(v);
    let inputG = $('input[name="' + n + '"]')[0];
    if ( ! inputG ) { return; };
    var parent = inputG.closest('[data-toggle="buttons"]');
    if ( parent ) {
      var $input;
      if ( v.constructor === Array){
        var vs = v;
      } else {
        var vs = [v];
      }
      vs.forEach( function(e) {
        //console.log(parent);
        //console.log(e);
        if ( e.constructor === Boolean ) {
          if ( e ) { e = 1; } else { e = 0; }
        }
        $input = $(parent)
          .find('input[value="' + e + '"]');
        //console.log($input);
        if ( ! $input.length ) {
          parent = $(parent.parentElement)
            .next('.form-group')
            .find('[data-toggle="buttons"]')[0]
          if (!parent){ return; }
          $input = $(parent).find('input[value="' + e + '"]');
        }
        $input.prop('checked', true);
        $($input[0].closest('label')).addClass('active');
      });
    }
  };

  let setInputfields = function (p) {
    if ( p.type == 'checked' ) {
      setCheckbox(p.name, getFieldValues(p, data) );
      return;
    }
    if ( p.type == 'textarea' ) {
      $('form')
        .find('textarea[name="' + p.name + '"]')
        .val(getFieldValues(p, data));
        tinymce.activeEditor.setContent(getFieldValues(p, data));
      return;
    }
    if (p.type == "file") {
      // FIXME: Hardcoded data key
      $('#files').empty();
      var deletebuttontext = Lang.get('seedbank::messages.delete');
      var deleteButton = $('<button/>')
        .addClass('btn btn-danger delete').prop('type', 'button')
        .text(deletebuttontext)
        .on('click', function () {
          //console.log("ddsds");
          //console.log($(this).data());
          var $this = $(this),
            file = $this.data().file;

          $.getJSON(file.deleteUrl, function (data){
            //console.log('in utils button');
            //console.log(data);
            if (data.files[0][file.md5sum]){
              $this.closest('.col-md-4').remove();
              //upload_counter = upload_counter - 1;
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
      if (data["pictures"]) {
        $.each(data["pictures"], function (index, file){
          file.deleteUrl = "/seedbank/pictures/delete/" + file.id;
          var hidden_input = '<input type="hidden" name="pictures_id[]" value="' + file.id + '">';
          var image = '<img class="img-responsive img-rounded cell" data-file-id="'
          + file.id + '" src="' + file.url + '" alt="' + file.label + '" />';
          var pictureDiv = '<div class="col-md-4" style="padding-bottom: 24px;"' +
            hidden_input + image + '</div>';
         var $clonedDelete = deleteButton.clone(true)
           .data("file", file);
         var $pictureDiv = $(pictureDiv);
           $pictureDiv.append($clonedDelete);
           //console.log($pictureDiv);
           //console.log($clonedDelete);
         $('#files').append($pictureDiv);
        })
      }
      return;

    }
    //console.log("name: " + p.name + "; value: " + getFieldValues(p, data));
    $('form')
      .find('input[name="' + p.name + '"]')
      .val(getFieldValues(p, data));
  };

  let getFieldValues = function(p, data) {
    // {"name": "species", "value": "species.name"},
    //console.log("value: " + p.value)
    var fields = p.value.split('.');


    if ( fields.length === 1 ) {
      if (! data[fields[0]]) {
        return "";
      }
      //console.log("data: " + data[fields[0]]);
      return data[fields[0]];
    };

    if ( fields.length > 1 ) {
      if ( fields[0].indexOf('[]') !== -1 ) {
        fields[0] = fields[0].replace('[]', '');
        if (! data[fields[0]]) {
          return "";
        }
        var arrayValues = [];
        data[fields[0]].forEach(function (e) {
          arrayValues.push(e[fields[1]]);
        });
        return arrayValues
          .sort(function(a, b){
            return parseInt(a) - parseInt(b);
        });

      };
      if ( ! data[fields[0]] ) {
        return "";
      }
    }
    return data[fields[0]][fields[1]];
  };
  //console.log('populate form');
  $.each(parameters, function (i, v) {
    setInputfields(v);
  });

};

function findGetParameter(parameterName) {
  // https://stackoverflow.com/a/5448595
  var result = null,
    tmp = [];
  var items = location.search.substr(1).split("&");
  for (var index = 0; index < items.length; index++) {
    tmp = items[index].split("=");
    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
  }
  return result;
};
