<script language="JavaScript">
<!--
var flag=0;
function SetFlag() {
  flag=1;
}
//-->
</script>
<table border="0" cellspacing="0" cellpadding="2" align="center" width="$VARS[width]">
  <tr>
   <td width="45%" valign="bottom" class="font2">$LANG[FormMess5]</td>
   <td width="55%" align="right" valign="bottom" class="font2"> <b><img src="$GB_PG[base_url]/img/return.gif" width="10" height="10">
     <a href="$GB_PG[index]">$LANG[BookMess4]</a></b></td>
  </tr>
</table>
<form method="post" action="$GB_PG[addentry]" onsubmit="return SetFlag()">

$GB_PREVIEW

</form>
