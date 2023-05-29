<!--html>
<head>
<title>$LANG[FormSelect]</title>
$LANG[metatag]
<meta name="keywords" content="guestbook, php, script, mySQL, free, advance">
<meta http-equiv="Content-Type" content="text/html;charset=TIS-620" />
</head>
<body bgcolor="$VARS[pbgcolor]" link="$VARS[link_color]" vlink="$VARS[link_color]"-->

<style type="text/css">
<!--
.font1 {  font-family: $VARS[font_face]; font-size: $VARS[tb_font_1]; color: $VARS[text_color] }
.font2 {  font-family: $VARS[font_face]; font-size: $VARS[tb_font_2]; color: $VARS[text_color] }
.font3 { font-family: Arial, Helvetica, sans-serif; font-size: 7.5pt; color: $VARS[text_color]; font-weight: bold}
.select {  font-family: $VARS[font_face]; font-size: 9pt}
.input {  font-family: $VARS[font_face]; font-size: 9pt}
-->
</style>
<script language="JavaScript">
<!--
function gb_picture(Image,imgWidth,imgHeight) {
    var border = 24;
    var img = Image;
    var features;
    var w;
    var h;
    winWidth = (imgWidth<100) ? 100 : imgWidth+border;
    winHeight = (imgHeight<100) ? 100 : imgHeight+border;
    if (imgWidth+border > screen.width) {
        winWidth = screen.width-10;
        w = (screen.width - winWidth)/2;
        features = "scrollbars=yes";      
    } else {
        w = (screen.width - (imgWidth+border))/2;
    }
    if (imgHeight+border > screen.height) {
        winHeight = screen.height-60;
        h = 0;
        features = "scrollbars=yes";      
    } else {
        h = (screen.height - (imgHeight+border))/2 - 20;
    }
    winName = (img.indexOf("t_") == -1) ? img.substr(4,(img.length-8)) : img.substr(6,(img.length-10));
    features = features+',toolbar=no,width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
    theURL = '$GB_PG[base_url]/picture.php?img='+Image;
    popup = window.open(theURL,winName,features);
    popup.focus();  
}
//-->
</script>
