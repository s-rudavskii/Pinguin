var alertSize = 1;
var add = false;

$(document).ready(function(){
  $('#filterEmail').keydown(function(e){
    if(e.keyCode==13){
      document.cookie = 'email=' + $('#filterEmail').val().trim();
      location.reload();
    }
  });

  $('.form-submit').bind(
    'click',
    function(){

      if (add) {
        return;
      }

      var url = $('#url').val();
      var email = $('#email').val();
      var comment = $('#comment').val();
      var bridge = $('#bridge').is(':checked');
      var pass = $('#password').val();

      if (url.trim() == '') {
        alert('URl is required', 'error');
        return;
      }

      $.post(
        "index.php",
        {
          action: 'add',
          url: url,
          email: email,
          comment: comment,
          bridge: bridge ? 1 : 0,
          pass: pass
        },
        function( data ) {
          data = parse(data);
          if (data){
            if (data['error']) {
              alert(data['data'], 'error');
            } else {
              add = true;
              alert('Site ' + url + ' add', 'ok');
              setTimeout(function(){
                location.reload();
              }, 4000);
            }
          } else {
            alert('Something going wrong', 'error');
          }
        }
      );
    }
  );
});

/**
 * @param id
 * @param passRec
 * @param url
 */
function deleteSite (id, passRec, url){
  var pass = '';
  var rez = false;
  if (confirm('Delete site?\n\n' + url)) {
    if (passRec) {
      pass = prompt('You delete site \n' + url + '\n\nEnter password:').trim();
    }
    $.post(
      "index.php",
      {
        id: id,
        pass: pass,
        action: 'delete'
      },
      function( data ) {
        rez = parse(data);
        if (rez){
          if (rez['error']) {
            alert(rez['data'], 'error');
          } else {
            alert('Site ' + url + ' delete', 'ok');
            $('.id-' + id).hide();
          }
        } else {
          alert('Something going wrong', 'error');
        }
      }
    );

  }
}

/**
 * @param text
 * @param type
 * @returns {number}
 */
function alert(text,type){
  var id = Math.floor((Math.random() * 1000) + 1);
  if (type === 'ok') {
    $("#alertBox").append('<div class="alertBox alertID-'+id+'">'+
      '<div id="alert" class="'+type+'">'+
      '<div class="icon "><span id="alertIcon" class="icon-done"></span></div>'+
      '<div class="text" id="alertText"></div>'+
      '</div></div>');
  } else if (type === 'error') {
    $("#alertBox").append('<div class="alertBox alertID-'+id+'">' +
      '<div id="alert" class="'+type+'">'+
      '<div class="icon "><span id="alertIcon" class="icon-close"></span></div>'+
      '<div class="text" id="alertText"></div>'+
      '</div></div>');
  } else {
    $("#alertBox").append('<div class="alertBox alertID-'+id+'">' +
      '<div id="alert" class="info">'+
      '<div class="icon "><span id="alertIcon" class="icon-sms"></span></div>'+
      '<div class="text" id="alertText"></div>'+
      '</div></div>');
  }
  $(".alertID-"+id+" #alertText").html(text);
  $(".alertID-"+id).animate({opacity: 1, marginTop: (275+(alertSize*60)) + 'px'}, 1000);
  alertSize++;
  setTimeout(function(){alertHide(id);},4000);
  return id;
}

/**
 * @param id
 * @returns {boolean}
 */
function alertHide(id){
  $(".alertID-"+id).animate({opacity: 0, marginTop: 0}, 1000);
  setTimeout(function(){alertDrop(id);},1000);
  alertSize--;
  return true;
}

/**
 * @param id
 * @returns {boolean}
 */
function alertDrop(id){
  $(".alertID-"+id).remove();
  return true;
}

/**
 * @param data
 * @returns {*}
 */
function parse(data){
  return $.parseJSON(data);
}
