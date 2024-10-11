/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */
jQuery(document).ready(function(){

	if (typeof window.ppFBAppID != "undefined" && window.ppFBAppID != "")
	  jQuery('.whpp-sharewrap .facebook').click(function(){

	  	var sF = jQuery(this).parent();
	  	if (jQuery(sF).attr('data-sharable') == "true")
	    wShareJS_ShareFB(jQuery(sF).attr('data-ppurl'),jQuery(sF).attr('data-ppdesc'),'',jQuery(sF).attr('data-ppimg'));

	  });

  jQuery('.whpp-sharewrap .twitter').click(function(){

  	var sF = jQuery(this).parent(); var twitterVia = '';	
  	if (typeof window.ppTwitterHandle != "undefined")  twitterVia = window.ppTwitterHandle;
  	if (jQuery(sF).attr('data-sharable') == "true")
    wShareJS_ShareTW(jQuery(sF).attr('data-ppurl'),jQuery(sF).attr('data-ppdesc'),twitterVia,jQuery(sF).attr('data-ppimg'));

  });

});




function wShareJS_ShareFB(url,title,via, imgurl){

  if (typeof imgurl == "undefined") imgurl = '';

  // via txt
  var viatxt = ''; if (typeof via != "undefined") if (via != '') viatxt = ' via ' + via;

  // Replaced URL @ 18/08
  //wShareJSxout('http://www.facebook.com/sharer.php?s= 100&amp;p[title]=' + title + '&amp;p[url]=' + url + '&amp;p[summary]=' + title + viatxt, 600, 260,'Share On Facebook');
  // As: 
  // http://stackoverflow.com/questions/20956229/has-facebook-sharer-php-changed-to-no-longer-accept-detailed-parameters
  wShareJSxout('https://www.facebook.com/dialog/feed?app_id=' + window.ppFBAppID  + '&display=popup&caption=' + title + viatxt + '&link=' + url + '&redirect_uri=' + url + '&picture' + imgurl, 600, 260,'Share On Facebook');
}

function wShareJS_ShareTW(url,title,via, imgurl){

  if (typeof imgurl == "undefined") imgurl = '';

  // via txt
  var viatxt = ''; if (typeof via != "undefined") if (via != '') viatxt = ' via ' + via + ' ';

  //wShareJSxout('http://twitter.com/home?status=' + title + ' ' + viatxt + url, 600, 260,'Share On Twitter');
  //https://dev.twitter.com/docs/intents
  wShareJSxout('http://twitter.com/intent/tweet/?text=' + title + ' ' + viatxt + url, 600, 260,'Share On Twitter');

}



function wShareJSxout(b, c, a, x) {
    wShareJS_OpenSeasame(b, x, c, a)
}



/* SHARES */
var wShareShareWindow = "";
function wShareJS_OpenSeasame(d, c, f, a) {
    c = c.replace(/\/|\-|\./gi, "");
    var b = new RegExp("\\s", "g");
    c = c.replace(b, "");
    var e = (screen.width) ? (screen.width - f) / 2 : 100;
    var g = (screen.height) ? (screen.height - a) / 2 : 100;
    if (!wShareShareWindow.closed && wShareShareWindow.location) {
        wShareShareWindow.location.href = d
    } else {
        wShareShareWindow = window.open(d, c, "left= " + e + ", top=" + g + ", location=no, scrollbars=yes, resizable=yes, toolbar=no, menubar=no, width=" + f + ", height=" + a);
        if (!wShareShareWindow.opener) {
            wShareShareWindow.opener = self
        }
    } if (window.focus) {
        wShareShareWindow.focus()
    }
}