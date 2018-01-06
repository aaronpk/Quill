(function(){
  window.open("<?= Config::$base_url ?>code?"+(window.location.hostname=='<?= $this->my_hostname ?>'?"edit="+encodeURIComponent(window.location.href)+"&":"")+"token=<?= $this->token ?>");
})();
