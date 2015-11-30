$(document).ready(function(){
  $('#filterEmail').keydown(function(e){
    if(e.keyCode==13){
      document.cookie = 'email=' + $('#filterEmail').val().trim();
      location.reload();
    }
  });
});