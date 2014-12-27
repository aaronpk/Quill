var quill_popup=function(){ 
  window.open(document.getElementById('quill-script').src.replace('favorite.js?','favorite-popup?'),'quill-like', 'status=no,directories=no,location=no,resizable=no,menubar=no,width=300,height=200,toolbar=no'); 
}; 
(function(){ 
  var quill=document.createElement('script'); 
  quill.src='<?= Config::$base_url ?>favorite.js?url='+encodeURIComponent(window.location)+'&token=<?= $this->token ?>'; 
  quill.setAttribute('id','quill-script'); 
  quill.setAttribute('onerror', 'quill_popup()'); 
  document.body.appendChild(quill); 
})();