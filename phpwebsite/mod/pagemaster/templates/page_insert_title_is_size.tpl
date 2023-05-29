<!-- BEGIN PAGE INSERT -->
<!-- BEGIN TABLE -->
<table class="page" width="100%" cellpadding="4" cellspacing="0">
<tr>
    <!-- BEGIN IFRAME-->
    <td valign="top" align="left">
    <iframe src="{TEXT}" width="100%">
        {IFRAME_ERROR_MSG}
    </iframe>
    </td>
    <!-- END IFRAME -->
</tr>
</table>
<!-- END TABLE -->
<script type="text/javascript">
//<![CDATA[

function BrowserDetectLite() {
	var ua = navigator.userAgent.toLowerCase(); 

	// browser name
	this.isGecko     = (ua.indexOf('gecko') != -1);
	this.isMozilla   = (this.isGecko && ua.indexOf("gecko/") + 14 == ua.length);
	this.isNS        = ( (this.isGecko) ? (ua.indexOf('netscape') != -1) : ( (ua.indexOf('mozilla') != -1) && (ua.indexOf('spoofer') == -1) && (ua.indexOf('compatible') == -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1) && (ua.indexOf('hotjava') == -1) ) );
	this.isIE        = ( (ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1) ); 
	this.isOpera     = (ua.indexOf("opera") != -1); 
	this.isKonqueror = (ua.indexOf("konqueror") != -1); 
	this.isIcab      = (ua.indexOf("icab") != -1); 
	this.isAol       = (ua.indexOf("aol") != -1); 
	this.isWebtv     = (ua.indexOf("webtv") != -1); 
	
	// spoofing and compatible browsers
	this.isIECompatible = ( (ua.indexOf("msie") != -1) && !this.isIE);
	this.isNSCompatible = ( (ua.indexOf("mozilla") != -1) && !this.isNS && !this.isMozilla);
	
	// browser version
	this.versionMinor = parseFloat(navigator.appVersion); 
	
	// correct version number for NS6+ 
	if (this.isNS && this.isGecko) {
		this.versionMinor = parseFloat( ua.substring( ua.lastIndexOf('/') + 1 ) );
	}
	
	// correct version number for IE4+ 
	else if (this.isIE && this.versionMinor >= 4) {
		this.versionMinor = parseFloat( ua.substring( ua.indexOf('msie ') + 5 ) );
	}
	
	// correct version number for Opera 
	else if (this.isOpera) {
		if (ua.indexOf('opera/') != -1) {
			this.versionMinor = parseFloat( ua.substring( ua.indexOf('opera/') + 6 ) );
		}
		else {
			this.versionMinor = parseFloat( ua.substring( ua.indexOf('opera ') + 6 ) );
		}
	}
	
	// correct version number for Konqueror
	else if (this.isKonqueror) {
		this.versionMinor = parseFloat( ua.substring( ua.indexOf('konqueror/') + 10 ) );
	}
	
	// correct version number for iCab 
	else if (this.isIcab) {
		if (ua.indexOf('icab/') != -1) {
			this.versionMinor = parseFloat( ua.substring( ua.indexOf('icab/') + 6 ) );
		}
		else {
			this.versionMinor = parseFloat( ua.substring( ua.indexOf('icab ') + 6 ) );
		}
	}
	
	// correct version number for WebTV
	else if (this.isWebtv) {
		this.versionMinor = parseFloat( ua.substring( ua.indexOf('webtv/') + 6 ) );
	}
	
	this.versionMajor = parseInt(this.versionMinor); 
	this.geckoVersion = ( (this.isGecko) ? ua.substring( (ua.lastIndexOf('gecko/') + 6), (ua.lastIndexOf('gecko/') + 14) ) : -1 );
	
	// platform
	this.isWin   = (ua.indexOf('win') != -1);
	this.isWin32 = (this.isWin && ( ua.indexOf('95') != -1 || ua.indexOf('98') != -1 || ua.indexOf('nt') != -1 || ua.indexOf('win32') != -1 || ua.indexOf('32bit') != -1) );
	this.isMac   = (ua.indexOf('mac') != -1);
	this.isUnix  = (ua.indexOf('unix') != -1 || ua.indexOf('linux') != -1 || ua.indexOf('sunos') != -1 || ua.indexOf('bsd') != -1 || ua.indexOf('x11') != -1)
	
	// specific browser shortcuts
	this.isNS4x = (this.isNS && this.versionMajor == 4);
	this.isNS40x = (this.isNS4x && this.versionMinor < 4.5);
	this.isNS47x = (this.isNS4x && this.versionMinor >= 4.7);
	this.isNS4up = (this.isNS && this.versionMinor >= 4);
	this.isNS6x = (this.isNS && this.versionMajor == 6);
	this.isNS6up = (this.isNS && this.versionMajor >= 6);
	
	this.isIE4x = (this.isIE && this.versionMajor == 4);
	this.isIE4up = (this.isIE && this.versionMajor >= 4);
	this.isIE5x = (this.isIE && this.versionMajor == 5);
	this.isIE55 = (this.isIE && this.versionMinor == 5.5);
	this.isIE5up = (this.isIE && this.versionMajor >= 5);
	this.isIE6x = (this.isIE && this.versionMajor == 6);
	this.isIE6up = (this.isIE && this.versionMajor >= 6);
	
	this.isIE4xMac = (this.isIE4x && this.isMac);
}
var browser = new BrowserDetectLite();

function walkUp(node)
{
    var currNode=node;
    var nextNode=node.parentNode;
    var rowHeight=0;
    var tableHeight=node.offsetHeight;
    var freeSpace=0;
    var totalTop=0;
    
    while(currNode != null) {
		if(currNode.nodeType == 1) {
			switch(currNode.tagName) {
				case "TR":
						rowHeight = currNode.offsetHeight; // remember
					break;
				case "TABLE":
					freeSpace += (rowHeight > tableHeight) ? rowHeight - tableHeight : 0; // accumulate
						tableHeight = currNode.offsetHeight; // remember
					totalTop += currNode.offsetTop; // accumulate
					break;
				case "TD":
					totalTop += currNode.offsetTop;
				default:;
			} // end switch
		} // end if
		currNode=currNode.parentNode; // walk up
	} // end while
	
	result = [totalTop,freeSpace];
	return result;
}

function upsize(vsize)
{
	var docIframes=document.getElementsByTagName('IFRAME');
	var theIframe=docIframes[0];

	switch(vsize)
	{
		case 0: // fit in current window
			result=walkUp(theIframe);
			if(browser.isIE6up && (document.childNodes.length > 1))
				theIframe.height = document.documentElement.clientHeight - result[0] - 5;
			else if(browser.isIE5up)
				theIframe.height = document.body.clientHeight - result[0];
			else if(browser.isNS6up || browser.isMozilla)
				theIframe.height = self.innerHeight - theIframe.offsetTop;
			scrollTo(0,0);
			break;
		case 1: // match current document body
			result=walkUp(theIframe);
			theIframe.height = theIframe.offsetHeight + result[1];
			break;
		default:
			theIframe.height = vsize;
	} // end switch(vsize)
	// alert(browser.isMozilla);
} /* end upsize() */

upsize({TITLE});

//]]>
</script>
<!-- END PAGE INSERT -->
