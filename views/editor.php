<!doctype html>
<html lang="en" manifest_="appcache.manifest">
  <head>
    <title>Quill Editor</title>
    <meta charset="utf-8">
    <link rel="pingback" href="http://webmention.io/aaronpk/xmlrpc" />
    <link rel="webmention" href="http://webmention.io/aaronpk/webmention" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- standard viewport tag to set the viewport to the device's width
      , Android 2.3 devices need this so 100% width works properly and
      doesn't allow children to blow up the viewport width-->
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" />
    <!-- width=device-width causes the iPhone 5 to letterbox the app, so
      we want to exclude it for iPhone 5 to allow full screen apps -->
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />

    <link rel="stylesheet" href="/editor/style.css">
    <link rel="stylesheet" href="/editor/medium-editor/css/medium-editor.min.css">
    <link rel="stylesheet" href="/editor/medium-editor/css/themes/default.min.css">
    <link rel="stylesheet" href="/editor/medium-editor/css/medium-editor-insert-plugin.min.css">
    <link rel="stylesheet" href="/editor/medium-editor/css/medium-editor-insert-plugin-frontend.min.css">
    <link href="/editor/font-awesome/css/font-awesome.css" rel="stylesheet">

    <script src="/editor/jquery-1.11.3.min.js"></script>
    <script src="/editor/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/editor/jquery.fileupload.js"></script>
    <script src="/editor/jquery.iframetransport.js"></script>

    <script src="/editor/handlebars.min.js"></script>
    <script src="/editor/medium-editor/js/medium-editor.min.js"></script>
    <script src="/editor/medium-editor/js/medium-editor-insert-plugin.min.js"></script>
    <script src="/editor/localforage/localforage.js"></script>

    <link rel="apple-touch-icon" sizes="57x57" href="/images/quill-icon-57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/quill-icon-72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/quill-icon-114.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/quill-icon-144.png">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
  </head>
<body>

<div class="toolbar">
  <div class="toolbar-left">
    <span class="item"><a href="/"><img src="/editor/quill-logo-36.png" width="36" height="31" class="logo"></a></span>
    <span class="item text"><span id="draft-status">Draft</span></span>
  </div>
  <div class="toolbar-right">
    <button class="btn" id="publish_btn">Publish</button>
    <button class="btn" id="new_btn">New</button>
  </div>
  <div class="clear"></div>
</div>

<div class="container">
  <input id="post-name" type="text" value="" placeholder="Title">
  <div id="content" class="editable"><p class="placeholder">Write something nice...</p></div>
</div>

<script src="/editor/editor.js"></script>

</body>
</html>