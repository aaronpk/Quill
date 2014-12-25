<?= $this->facebook_id ? partial('partials/fb-script') : '' ?>

console.log("Favoriting URL: <?= $this->url ?>");

var star = document.createElement('img');
star.id="quill-star";
star.src="http://quill.dev/images/<?= $this->like_url ? 'star' : 'red-x' ?>.svg";
document.body.appendChild(star);

var css = document.createElement('link');
css.rel="stylesheet";
css.type="text/css";
css.href="http://quill.dev/css/favorite.css";
document.body.appendChild(css);

setTimeout(function(){
  
  document.getElementById('quill-star').classList.add('hidden');
  var el = document.getElementById('quill-star');
  el.parentNode.removeChild(el);

}, 1200);
