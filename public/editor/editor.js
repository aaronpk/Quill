var editor = new MediumEditor('.editable', {
  buttons: ['bold', 'italic', 'header1', 'header2', 'quote', 'unorderedlist', 'pre'],
  paste: {
    // This example includes the default options for paste, if nothing is passed this is what it used
    forcePlainText: false,
    cleanPastedHTML: true,
    cleanReplacements: [],
    cleanAttrs: ['class', 'style', 'dir'],
    cleanTags: ['meta']
  }
});

$(function () {
  $('.editable').mediumInsert({
    editor: editor,
    beginning: true,
    addons: {
      images: {
        deleteScript: '/editor/delete-file',
        fileUploadOptions: {
          url: '/editor/upload'
        }
      },
      embeds: {
        oembedProxy: '/editor/oembed'
      }
    }
  });
});
