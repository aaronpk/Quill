<!doctype html>
<html lang="en">
  <head>
    <title>Quill Editor</title>
    <meta charset="utf-8">
    <link rel="pingback" href="https://webmention.io/aaronpk/xmlrpc" />
    <link rel="webmention" href="https://webmention.io/aaronpk/webmention" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- standard viewport tag to set the viewport to the device's width
      , Android 2.3 devices need this so 100% width works properly and
      doesn't allow children to blow up the viewport width-->
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" />
    <!-- width=device-width causes the iPhone 5 to letterbox the app, so
      we want to exclude it for iPhone 5 to allow full screen apps -->
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />

    <link rel="stylesheet" href="/editor-files/medium-editor/css/medium-editor.min.css">
    <link rel="stylesheet" href="/editor-files/medium-editor/css/themes/default.min.css">
    <link rel="stylesheet" href="/editor-files/medium-editor/css/medium-editor-insert-plugin.min.css">
    <link rel="stylesheet" href="/editor-files/medium-editor/css/medium-editor-insert-plugin-frontend.min.css">
    <link href="/editor-files/font-awesome/css/font-awesome.css" rel="stylesheet">

    <script src="/editor-files/jquery-1.11.3.min.js"></script>
    <script src="/editor-files/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/editor-files/jquery.fileupload.js"></script>
    <script src="/editor-files/jquery.iframetransport.js"></script>

    <script src="/editor-files/handlebars.min.js"></script>
    <script src="/editor-files/medium-editor/js/medium-editor.min.js"></script>
    <script src="/editor-files/medium-editor/js/medium-editor-insert-plugin.min.js"></script>
    <script src="/libs/localforage.js"></script>

    <script src="/libs/tokenfield/bootstrap-tokenfield.min.js"></script>
    <link rel="stylesheet" href="/libs/tokenfield/bootstrap-tokenfield.min.css">
    <link rel="stylesheet" href="/libs/tokenfield/tokenfield-typeahead.min.css">

    <link rel="stylesheet" href="/editor-files/style.css">

    <link rel="apple-touch-icon" sizes="57x57" href="/images/quill-icon-57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/quill-icon-72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/quill-icon-114.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/quill-icon-144.png">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
  </head>
<body>

<div class="toolbar">
  <div class="toolbar-left">
    <span class="item"><a href="/"><img src="/editor-files/quill-logo-36.png" width="36" height="31" class="logo"></a></span>
    <? if($this->user): ?>
      <span class="item text"><b><?= display_url($this->user->url) ?></b></span>
    <? endif; ?>
    <span class="item text"><span id="draft-status">Draft</span></span>
  </div>
  <div class="toolbar-right">
    <button class="btn" id="publish_btn">Publish <i class="fa fa-caret-down"></i></button>
    <button class="btn" id="new_btn">New</button>
  </div>
  <div class="clear"></div>
</div>

<div class="micropub-html-warning hidden"><div>
  <button class="btn btn-default" id="micropub-html-btn">Upgrade me!</button>
  <b>Upcoming change!</b>
  The Micropub spec now requires HTML content be sent as a nested object, <code>content[html]=&lt;b&gt;example&lt;/b&gt;</code>.
  You can <a href="http://indiewebcamp.com/Micropub-brainstorming#HTML_Escaping">read more about the change here</a>.
  When you are ready to receive the content as an object, click the button to switch.
</div></div>

<div class="publish-dropdown hidden">
  <div class="arrow"></div>
  <div class="dropdown-content action-publish">

    <div style="float:right"><button class="btn btn-medium" id="publish-confirm">Publish Now</button></div>
    <div style="clear:right; margin-bottom: 4px;"></div>

    <table id="publish-fields">
      <tr>
        <td>Tags:</td>
        <td><input type="text" class="form-field-small" placeholder="" id="post-tags"></td>
      </tr>
      <tr>
        <td>Slug:</td>
        <td><input type="text" class="form-field-small" id="post-slug"></td>
      </tr>
      <tr>
        <td>Status:</td>
        <td>
          <select id="post-status" class="form-select-small">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
          </select>
          <a href="/docs/post-status" class="small hidden" target="_blank" id="published-status-warning">read this first!</a>
        </td>
      </tr>
      <tr>
        <td>Publish:</td>
        <td><input type="text" class="form-field-small" id="post-publish-date" value="now" placeholder="YYYY-MM-DD hh:mm:ss"></td>
      </tr>
    </table>


    <div class="helptext hidden" id="publish-in-progress">
      Posting... <!-- TODO replace this with a CSS animated spinner -->
    </div>

    <div class="helptext hidden" id="publish-success">
      <div><a href="" id="publish-success-url">View your post</a></div>
    </div>

    <div class="helptext hidden" id="publish-error">
      <div>Something went wrong! Below is the response from your Micropub endpoint.</div><br>
      <pre id="publish-error-debug"></pre>
    </div>

  </div>
  <div class="dropdown-content action-signin hidden">
    <div class="helptext">You need to sign in before you can publish! Don't worry, your draft will still be here when you finish signing in.</div>
    <input type="url" class="form-field-small" placeholder="yourdomain.com" id="signin-domain">
    <button class="btn btn-small" id="signin-btn">Sign In</button>
    <div class="helptext small"><a href="/docs">How does this work?</a></div>
  </div>
</div>

<div class="container">
  <input id="post-name" type="text" value="" placeholder="Title">
  <div id="content" class="editable"></div>
</div>

<script src="/editor-files/editor.js"></script>

</body>
</html>