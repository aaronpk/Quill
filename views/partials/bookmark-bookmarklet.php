(function(){
  var t;try{t=((window.getSelection&&window.getSelection())||(document.getSelection&&document.getSelection())||(document.selection&&document.selection.createRange&&document.selection.createRange().text));}catch(e){t="";};
  window.location="<?= Config::$base_url ?>bookmark?url="+encodeURIComponent(window.location.href)+"&content="+encodeURIComponent((t == '' ? '' : '"'+t+'"'))+"&name="+encodeURIComponent(document.title)+"&token=<?= $this->token ?>";
})();
