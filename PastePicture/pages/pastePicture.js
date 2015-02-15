$("document").ready(function(){
  var uf = $('#ufile\\[\\][type!="hidden"]');
  uf.parent().prepend("<textarea id='pasteHere'>Paste image here</textarea>");
  $("#pasteHere").pastableTextarea();
  $("#pasteHere").on('pasteImage', function (ev, data){
          var uf = $('#ufile\\[\\][type!="hidden"]');
                    if(uf.size()>0){
                        uf.parent().prepend("<input id='ufile[]' name='ufile[]' type='hidden'><img width=50% id='pasteImg'>");
                        $('#pasteHere').remove();
                        uf.remove();
                        uf = $('#ufile\\[\\]');
                        uf.val(data.dataURL);
                        $('#pasteImg').attr('src',data.dataURL);

                        var sb = $('input[value="Upload File"]');
                        if(sb.size()==1){
                            sb.css('border','5px solid red');
                            var frm = sb.closest('form');
                            frm.attr('action','plugin.php?page=PastePicture/bug_file_add.php');
                        }
                    }
    });
});
