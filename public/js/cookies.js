// cookies
if (document.cookie.indexOf("allowCookies=1") == -1) {

  function getHref() {
      lowerBody = document.body.innerHTML.toLowerCase();
      if(document.body.innerHTML.toLowerCase().indexOf('polityka prywatnoĹÂci') > 0) { 
          elements = document.getElementsByTagName("a");  
          for (var i = elements.length-1; i >= 0; i--) {                
              if(document.getElementsByTagName("a").item(i).innerHTML.toLowerCase() == 'polityka prywatnoĹÂci') return document.getElementsByTagName("a").item(i).href; 
          }
      }
      return 'http://wszystkoociasteczkach.pl/';
  }

  function getDomain() {
    var domainArray = window.location.hostname.split('.'); 
    if(domainArray.length > 3) return domainArray[domainArray.length-3] + '.' + domainArray[domainArray.length-2] + '.' + domainArray[domainArray.length-1];
    else return window.location.hostname;
  }

  function create(htmlStr) {
      var frag = document.createDocumentFragment(),
          temp = document.createElement('div');
      temp.innerHTML = htmlStr;
      while (temp.firstChild) {
          frag.appendChild(temp.firstChild);
      }
      return frag;
  }
  
  function checkCookies() {
      var text = 'Serwis www.e-jeziora24.pl wykorzystuje do prawidłowego działania pliki cookies. <a target="_blank" href="' + getHref() + '" style="color: #FFF;">[Więcej informacji]</a>';
      var fragment = create('<div id="cookieinfo" style="background: #446e9b; color: #111; font: 15px;"><div style="padding: 10px; max-width: 1140px; margin: 0 auto; text-align: justify;"><p style="color: #FFF;">' + text + '<p style="margin-top: 5px; text-align: right;"><a href="#" onclick="acceptCookie();return false;" style="color: #FFF; text-decoration: none;">Rozumiem.</a></p></p></div></div>');
      document.body.insertBefore(fragment, document.body.childNodes[0]); 
  }
  
  function acceptCookie() {
      var exdate = new Date();
      exdate.setDate(exdate.getDate() + 365);
      var value = "1" + "; expires=" + exdate.toUTCString() + ';domain=.' + getDomain() + ';path=/';
      document.cookie = "allowCookies=" + value;
      var element = document.getElementById('cookieinfo');
      element.parentNode.removeChild(element);
  }

  window.onload = checkCookies;   
}