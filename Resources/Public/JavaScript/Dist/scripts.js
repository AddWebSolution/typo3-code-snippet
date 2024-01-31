/*!
 * So Typo3 v1.0.0 (https://www.plus-it.de)
 * Copyright 2017-2023 Plus IT
 * Licensed under the GPL-2.0-or-later license
 */
console.log("WE LOVE TYPO3@@");
/*
$(document).ready(function(){
   alert(111);
});*/
function languageChange(val,request=[]) {
   console.log(request);
   console.log(val);
   var base_url = window.location.origin ;
   var query = new URLSearchParams();
   request.tx_short_code['lang'] = val;
   var queryString = Object.keys(request.tx_short_code).map(function (key) {
      return ("tx_short_code[" + key + "]") + '=' + encodeURIComponent( request.tx_short_code[key]);
   }).join('&');
   var url = base_url+"/?" + queryString;
   console.log(url);
   location.replace(url);
}

function searchFilter(request) {
   console.log(request);
   var base_url = window.location.origin ;
   var query = new URLSearchParams();
   var val = document.getElementById("gsearch").value;
   request.tx_short_code['search'] = val;
   request.tx_short_code['page'] = 1;
   console.log(request);
   var queryString = Object.keys(request.tx_short_code).map(function (key) {
      return ("tx_short_code[" + key + "]") + '=' + encodeURIComponent( request.tx_short_code[key]);
   }).join('&');

   var url = base_url+"/?" + queryString;

   console.log(url);
   location.replace(url);
}