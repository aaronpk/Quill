<div class="narrow">
  <?= partial('partials/header') ?>

    <div style="clear: both;">
      <div class="alert alert-success hidden" id="test_success"><strong>Success! </strong><a href="" id="post_href">View your post</a></div>
      <div class="alert alert-danger hidden" id="test_error"><strong>Something went wrong!</strong><br>Your Micropub endpoint indicated that something went wrong creating the post.</div>
    </div>

    <form role="form" style="margin-top: 20px;" id="note_form">

      <h2>Product</h2>

      <div class="row">
        <div class="col-xs-6">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" id="item_name" placeholder="" value="">
          </div>
        </div>
        <div class="col-xs-6">
          <div class="form-group">
            <label>URL</label>
            <input type="url" class="form-control" id="item_url" placeholder="" value="">
          </div>
        </div>
      </div>

      <h2>Review</h2>

      <div class="rating-stars">
        <a href="" data-rating="1"></a><a href="" data-rating="2"></a><a href="" data-rating="3"></a><a href="" data-rating="4"></a><a href="" data-rating="5"></a>
        <span class="description">It's okay</span>
      </div>

      <div class="row review-content hidden">
        <div class="col-xs-12">
          <div class="form-group">
            <textarea id="review_content" value="" class="form-control" style="height: 4em;" placeholder="Write your review here"></textarea>
            <div id="review-html-note">
              <input type="checkbox" id="review_is_html" value="1"> Post as HTML
            </div>
          </div>
        </div>
      </div>

      <div class="row review-summary hidden">
        <div class="col-xs-12">
          <div class="form-group">
            <input id="review_summary" value="" class="form-control" placeholder="Review summary">
          </div>
        </div>
      </div>

      <div class="row review-save hidden">
        <div class="col-xs-12">
          <div style="float: right; margin-top: 6px;">
            <button class="btn btn-success" id="btn_post">Post Review</button>
          </div>
        </div>
      </div>

    </form>

</div>
<style type="text/css">
.alert {
  margin-top: 1em;
}
.rating-stars {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.rating-stars .description {
  display: none;
  font-weight: bold;
  margin-left: 20px;
}
.rating-stars .description.visible {
  display: inline-block;
}
.rating-stars a {
  display: inline-block;
  width: 64px;
  height: 64px;
  background-repeat: no-repeat;
  background-image: url("data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4IgogICB3aWR0aD0iNjRweCIgaGVpZ2h0PSI2NHB4IiB2aWV3Qm94PSIwIDAgNjQgNjQiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDY0IDY0IiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSJub25lIiBzdHJva2U9IiNBN0E5QUMiIHN0cm9rZS13aWR0aD0iNCIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9IjMxLjg2Niw2LjYxOCA0MC4wOSwyMy4yODEgNTguNDc5LDI1Ljk1MyA0NS4xNzIsMzguOTIzIDQ4LjMxMyw1Ny4yMzkgMzEuODY2LDQ4LjU5MiAxNS40MTgsNTcuMjM5IDE4LjU2LDM4LjkyMyA1LjI1MywyNS45NTMgMjMuNjQyLDIzLjI4MSAiLz48L3N2Zz4=");

}
.rating-stars a.hover {
  background-image: url("data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMHB4IiB5PSIwcHgiCiAgIHdpZHRoPSI2NHB4IiBoZWlnaHQ9IjY0cHgiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNjQgNjQiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxwb2x5Z29uIGZpbGw9IiM1MUFFQ0QiIHN0cm9rZT0iIzUxQUVDRCIgc3Ryb2tlLXdpZHRoPSI0IiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIHBvaW50cz0iMzEuODY2LDYuNjE4IDQwLjA5LDIzLjI4MSA1OC40NzksMjUuOTUzIDQ1LjE3MiwzOC45MjMgNDguMzEzLDU3LjIzOSAzMS44NjYsNDguNTkyIDE1LjQxOCw1Ny4yMzkgMTguNTYsMzguOTIzIDUuMjUzLDI1Ljk1MyAyMy42NDIsMjMuMjgxICIvPjwvc3ZnPgo=");
}
.rating-stars a.selected {
  background-image: url("data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4IgogICB3aWR0aD0iNjRweCIgaGVpZ2h0PSI2NHB4IiB2aWV3Qm94PSIwIDAgNjQgNjQiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDY0IDY0IiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjRkVDMjBGIiBzdHJva2U9IiNGRUMyMEYiIHN0cm9rZS13aWR0aD0iNCIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9IjMxLjg2Niw2LjYxOCA0MC4wOSwyMy4yODEgNTguNDc5LDI1Ljk1MyA0NS4xNzIsMzguOTIzIDQ4LjMxMyw1Ny4yMzkgMzEuODY2LDQ4LjU5MiAxNS40MTgsNTcuMjM5IDE4LjU2LDM4LjkyMyA1LjI1MywyNS45NTMgMjMuNjQyLDIzLjI4MSAiLz48L3N2Zz4=");
}

.review-content {
  margin-top: 1em;
}
#review-html-note {
  font-size: 12px;
  text-align: right;
}

</style>
<script>
var selectedRating = 0;
var userSelectedHTML = null;

function isHTML(str) {
  var doc = new DOMParser().parseFromString(str, "text/html");
  return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}

function isTouchDevice() {
  return 'ontouchstart' in document.documentElement;
}

function setSaveButtonState() {
  if(selectedRating > 0 && $("#item_name").val() != "" && $("#item_url").val() != "") {
    $(".review-save").removeClass("hidden");
  } else {
    $(".review-save").addClass("hidden");
  }
}

$(function(){

  $(".rating-stars a").on("mouseover",function(){
    // Disable hover effects on touch devices
    if(isTouchDevice()) { return; }

    $(this).addClass("hover");
    var to = intval($(this).data("rating"));
    $(".rating-stars a").removeClass("selected");
    for(var i=1; i<=to; i++) {
      $(".rating-stars a[data-rating="+i+"]").addClass("hover").removeClass("selected");
    }
    var description;
    switch(to) {
      case 1:
        description = "I hate it"; break;
      case 2:
        description = "I don't like it"; break;
      case 3:
        description = "It's okay"; break;
      case 4:
        description = "I like it"; break;
      case 5:
        description = "I love it!"; break;
    }
    $(".rating-stars .description").text(description);
    $(".rating-stars span").addClass("visible");
  });
  $(".rating-stars a").on("mouseout",function(){
    $(this).removeClass("hover");
  });
  $(".rating-stars").on("mouseout",function(){
    $(".rating-stars span").removeClass("visible");
    $(".rating-stars a").removeClass("hover");
    if(selectedRating) {
      for(var i=1; i<=selectedRating; i++) {
        $(".rating-stars a[data-rating="+i+"]").addClass("selected")
      }
    }
  });
  $(".rating-stars a").on("click",function(){
    selectedRating = intval($(this).data("rating"));
    $(".rating-stars a").removeClass("hover").removeClass("selected");
    for(var i=1; i<=selectedRating; i++) {
      $(".rating-stars a[data-rating="+i+"]").addClass("selected")
    }
    $(".review-content").removeClass("hidden");
    setSaveButtonState();
    return false;
  });

  $("#review_is_html").on("click", function(){
    if($(this).attr("checked") == "checked") {
      userSelectedHTML = 1;
    } else {
      userSelectedHTML = -1;
    }
  });
  $("#review_content").on("keyup", function(){
    if(userSelectedHTML == null) {
      if(isHTML($(this).val())) {
        $("#review_is_html").attr("checked", "checked");
      } else {
        $("#review_is_html").removeAttr("checked");
      }
    }
    if($(this).val() != "") {
      $(".review-summary").removeClass("hidden");
    } else {
      $(".review-summary").addClass("hidden");
    }

    var scrollHeight = document.getElementById("review_content").scrollHeight;
    var currentHeight = parseInt($("#review_content").css("height"));
    if(Math.abs(scrollHeight - currentHeight) > 20) {
      $("#review_content").css("height", (scrollHeight+30)+"px");
    }
  });

  $("#item_name").on("keyup", setSaveButtonState);
  $("#item_url").on("keyup", setSaveButtonState);

  $("#btn_post").click(function(){
    $("#btn_post").addClass("loading disabled").text("Working...");

    var review = {
      item: [{
              type: ["h-product"],
              properties: {
                name: [$("#item_name").val()],
                url: [$("#item_url").val()]
              }
            }],
      rating: [selectedRating],
    };
    if($("#review_content").val() != "") {
      if($("#review_is_html").attr("checked") == "checked") {
        review["content"] = [{html: $("#review_content").val()}];
      } else {
        review["content"] = [$("#review_content").val()];
      }
    }
    if($("#review_summary").val() != "") {
      review["summary"] = [$("#review_summary").val()];
    }

    $.post("/micropub/postjson", {
      data: JSON.stringify({
        "type": ["h-review"],
        "properties": review
      })
    }, function(response){
      $("#btn_post").removeClass("loading disabled").text("Post Review");

      if(response.location != false) {
        $("#post_success").removeClass('hidden');
        $("#post_error").addClass('hidden');
        $("#post_href").attr("href", response.location);
        $("#note_form").addClass("hidden");
      } else {
        $("#post_success").addClass('hidden');
        $("#post_error").removeClass('hidden');
      }

    });
    return false;

  });

});
</script>
