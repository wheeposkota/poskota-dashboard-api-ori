window._ = require('lodash');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');

Vue.config.errorHandler = function (err, vm) {
  window.console.log(err);
};


$(document).ready(function() {
  $(".delete-content").click(function(event) {
    if(window.confirm('Anda yakin ingin menghapus konten iklan ini ?'))
    {
      $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            data: {_token: window.Laravel.csrfToken, id: $(this).attr('data-id'), _method: 'DELETE'},
          })
      .done(function($response) {
        if($response.code == 200)
          window.location.reload();
      });
    }
  });

  $(".delete-schedule").click(function(event) {
    if(window.confirm('Anda yakin ingin menghapus jadwal tayang iklan ini ?'))
    {
      $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            data: {_token: window.Laravel.csrfToken, id: $(this).attr('data-id'), _method: 'DELETE'},
          })
      .done(function($response) {
        if($response.code == 200)
          window.location.reload();
      });
    }
  });
});

var hasilcek = [];

window.checkContent = function(){

  //cek char
  $(document).on("keyup", ".formkonten", function(e) {
      var konten = $(this).val();
      var indexxx = $('.formkonten').index(this);
      if (e.keyCode == 13 && !e.shiftKey) {
          $('.formkonten').eq(indexxx).val(konten.replace(/[\r\n]+/g, ""));
      } else {
          checkcharkonten(e, konten, indexxx);
      }
  });
  // cek char
  
  $(".formkonten").bind("paste", function(e){
      var konten = $(this).val();
      var indexxx = $('.formkonten').index(this);
      checkcharkonten(e, konten, indexxx);
  });
}

window.checkcharkonten = function(e, konten, indexxx){

  /*var sub_value = $('.formkonten').eq(indexxx).parent().parent().find("[name='ad_taxonomy_id[]'] option:selected").text();
  try {
    if(sub_value != '' && sub_value != $('.formkonten').eq(indexxx).parent().parent().find("[name='ad_taxonomy_id[]'] option").eq(0).text())
    {
     if(!konten.startsWith(sub_value))
     {
        $('.formkonten').eq(indexxx).val(sub_value);
        konten = $('.formkonten').eq(indexxx).val();
     }
    }
  } catch(e) {
  }*/

  /*var sub_loc = $('.formkonten').eq(indexxx).parent().parent().find("[name='mst_city_id[]'] option:selected").text();
  try {
    if(sub_loc != '' && sub_loc != $('.formkonten').eq(indexxx).parent().parent().find("[name='mst_city_id[]'] option").eq(0).text())
    {
     if(!konten.startsWith(sub_loc))
     {
        $('.formkonten').eq(indexxx).val(sub_loc);
        konten = $('.formkonten').eq(indexxx).val();
     }
    }
  } catch(e) {
  }*/

  var countchar = konten.length;
  var maxchar = $('.formkonten').eq(indexxx).attr('data-maxlength');

  var strings = konten;
  var capital = 0;

  strings = strings.replace(/(^\s*)|(\s*$)/gi, ""); //exclude  start and end white-space
  strings = strings.replace(/[ ]{2,}/gi, " "); //2 or more space to 1
  strings = strings.replace(/\n /, "\n"); // exclude newline with a start spacing

  var wordarray = strings.split(' ');
  var previewarray = strings.split(' ');
  var jmlkata = wordarray.length;

  var i = 0;
  
  var longwords = [];
  var longestword = '';
  var longestwordmessage = '';
  var character = '';
  var maxcharperkata = 25;
  var maxcharkapital = getCapitalLenght(indexxx) / 2;
  var maxcharkapitalmessage = '';

  var pricethis = $('.formkonten').eq(indexxx).attr('data-price');
  var charonline = $('.formkonten').eq(indexxx).attr('data-char-on-line');
  var minline = $('.formkonten').eq(indexxx).attr('data-min-line');

  var currentlinetotal = Math.ceil(countchar / parseInt(charonline));

  var currentprice = currentlinetotal * parseInt(pricethis);
  if(currentlinetotal <= parseInt(minline)) {
      var currentprice = parseInt(minline) * parseInt(pricethis);
  }
  

  while (i < jmlkata) {

      var code = e.key;
      if(i < 2){
          wordarray[i] = wordarray[i].toUpperCase();
          //$('.formkonten').eq(indexxx).val(wordarray.join(' ').substring(0, maxchar));

          previewarray[i] = "<b>" + previewarray[i].toUpperCase() + "</b>";
          $('.previewtext').eq(indexxx).html(previewarray.join(' ').substring(0, maxchar + 7));
      }
      
      var j = 0;
      while (j <= wordarray[i].length) {
          character = wordarray[i].charAt(j);
          if(character.charCodeAt() >= 65 && character.charCodeAt() <= 90) {
              console.log(character);
              capital++;
          }
          j++;
      }

      var listemail = ['@gmail', '@yahoo', '@outlook', '@live'];
      var includeemail = false; 
      for(var m = 0; m < listemail.length; m++){
          var email = wordarray[i].toLowerCase();
          includeemail = email.includes(listemail[m]);
          if(includeemail) {
              break;
          }
      }

      
      if(wordarray[i].length >= maxcharperkata && includeemail == false){
          longwords.push(wordarray[i]);
      }

      if (capital >= maxcharkapital || longwords.length > 0) {
          if(capital >= maxcharkapital) {
              maxcharkapitalmessage = 'huruf kapital melebihi ketentuan';
          }
          if (longwords.length > 0) {
              // longestword = wordarray[i];
              longestword = longwords.join(', ');
              longestwordmessage = '"' + longestword + '" lebih dari ' + maxcharperkata + ' karakter';
          }
          //$('.formkonten').eq(indexxx).attr('maxlength', countchar);
          hasilcek[indexxx] = false;
      } else {
          longestword = '';
          longestwordmessage = '';
          maxcharkapitalmessage = '';
          //$('.formkonten').eq(indexxx).attr('maxlength', maxchar);
          hasilcek[indexxx] = true;
      }

      i++;
  }

  if(hasilcek.includes(false)){
      $('#buttonlanjut').attr('disabled',true);
  }else{
      $('#buttonlanjut').attr('disabled',false);
  }


  $('.formkonten').eq(indexxx).parent()
      .find('.countchar')
      .html(countchar + ' dari ' + maxchar + ' karakter | kapital : ' + capital + ' Baris : <b>'+currentlinetotal+'</b> Harga : Rp<b>' + currentprice + '</b> <br> <span style="color:red">' + longestwordmessage + ' - ' + maxcharkapitalmessage + '</span> ');
}

window.getCapitalLenght = function(indexxx){
	return $('.formkonten').eq(indexxx).attr('data-maxlength');
}