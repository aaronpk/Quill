
console.log("Favoriting URL: <?= $this->url ?>");

var css = document.createElement('link');
css.rel="stylesheet";
css.type="text/css";
css.href="<?= Config::$base_url ?>css/favorite.css";
document.body.appendChild(css);

function show_star() {
  var star = document.createElement('img');
  star.id="quill-star";
  star.src="<?= Config::$base_url ?>images/<?= $this->like_url ? 'star' : 'red-x' ?>.svg";
  star.onload=function() {
    setTimeout(function(){
      
      document.getElementById('quill-star').classList.add('hidden');
      var el = document.getElementById('quill-star');
      el.parentNode.removeChild(el);
      if(typeof favorite_finished == "function") {
        favorite_finished();
      } else {
        // For now, redirect the user to the URL of their favorite so they can see it posted.
        // Might want to change this later.
        window.location = "<?= $this->like_url ?>";
      }

    }, 1200);  
  }
  document.body.appendChild(star);
}

show_star();
