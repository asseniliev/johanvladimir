// Cookie scripts courtesy of WebCoder.com
// http://www.webcoder.com

// Use this function to retrieve a cookie.
function getCookie(name){
    var cname = name + "=";
    var dc = document.cookie;
    if (dc.length > 0) {
        begin = dc.indexOf(cname);
        if (begin != -1) {
            begin += cname.length;
            end = dc.indexOf(";", begin);
            if (end == -1) end = dc.length;
            return unescape(dc.substring(begin, end));
        }
    }
return null;
}

// Use this function to save a cookie.
function setCookie(name, value, expires) {
    document.cookie = name + "=" + escape(value) + "; path=/" +
    ((expires == null) ? "" : "; expires=" + expires.toGMTString());
}

// Use this function to delete a cookie.
function delCookie(name) {
    document.cookie = name + "=; expires=Thu, 01-Jan-70 00:00:01 GMT" +  "; path=/";
}
