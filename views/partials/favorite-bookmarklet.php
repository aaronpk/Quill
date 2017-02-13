(function(){
  window.open("<?= Config::$base_url ?>favorite?url="+encodeURIComponent(window.location.href)+"&token=<?= $this->token ?>");
})();
