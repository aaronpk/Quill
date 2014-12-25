console.log("Favoriting with token: " + quill_token);

var http = new XMLHttpRequest();
var params = "like-of=" + encodeURIComponent(window.location) + "&token=" + quill_token;

http.open("POST", "http://quill.dev/favorite", true);
http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

http.onreadystatechange = function() {//Call a function when the state changes.
  console.log(http);
    if(http.readyState == 4 && http.status == 200) {
        alert(http.responseText);
    }
}
http.send(params);

/*

(function(){var el=document.createElement('input'); el.type="hidden"; el.id="quill_token"; el.value="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiMjciLCJtZSI6Imh0dHA6XC9cL2Fhcm9ucGFyZWNraS5jb20iLCJjcmVhdGVkX2F0IjoxNDEwMTE3NTM5fQ.ifp1VIgCTz9NPtMTlTLPBXAGSxHwpGS5tLPhXGxrjNk"; document.body.appendChild(el); document.body.appendChild(document.createElement('script')).src='http://quill.dev/js/fav.js';})();

(function(){document.body.appendChild(document.createElement('script')).src='http://quill.dev/favorite.js?url='+encodeURIComponent(window.location)+'&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiMSIsIm1lIjoiaHR0cDpcL1wvcGsuZGV2XC8iLCJjcmVhdGVkX2F0IjoxNDE5MDM2NzAzfQ.AgJ5xyviiBzWOvQO0je0Bdi3BUpKJ4CLJnx8GIm-0OI';})();

*/