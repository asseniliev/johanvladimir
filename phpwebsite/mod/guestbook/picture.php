<?php
$include_path = dirname(__FILE__);
$border = 24;

include_once $include_path."/admin/config.inc.php";

if (isset($HTTP_GET_VARS['img'])) {
    if (file_exists("$GB_TMP/$HTTP_GET_VARS[img]")) {
        $size = GetImageSize("$GB_TMP/$HTTP_GET_VARS[img]");
        $picture = "$GB_PG[base_url]/$GB_TMP/$HTTP_GET_VARS[img]";
    }
    if (file_exists("$GB_UPLOAD/$HTTP_GET_VARS[img]")) {
        if (eregi("(^t_)(img-[0-9]+.[a-z]{3})",$HTTP_GET_VARS['img'],$regs)) {
            $size = GetImageSize("$GB_UPLOAD/$regs[2]");
            $picture = "$GB_PG[base_url]/$GB_UPLOAD/$regs[2]";
        } else {
            $size = GetImageSize("$GB_UPLOAD/$HTTP_GET_VARS[img]");
            $picture = "$GB_PG[base_url]/$GB_UPLOAD/$HTTP_GET_VARS[img]";
        }
    }
}
$tbl_height = (isset($size[1]) && $size[1]>100) ? $size[1]+$border : 100;
?>
<html>
<head>
<title>Guestbook</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#CCCCCC" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="<?php echo $tbl_height; ?>">
  <tr>
    <td align="center" valign="middle">
    <?php
        if (isset($HTTP_GET_VARS['img']) && is_array($size)) {
            echo "<a href=\"javascript:window.close()\"><img src=\"$picture\" width=\"$size[0]\" height=\"$size[1]\" border=\"0\"></a>\n";
        }
    ?>
    </td>
  </tr>
</table>
</body>
</html>
