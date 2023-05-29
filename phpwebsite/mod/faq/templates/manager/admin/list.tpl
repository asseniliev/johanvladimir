<table border="0" width="100%" cellspacing="1" cellpadding="4">
<tr><td class="smalltext" colspan="5"><b>{TITLE}</b>&#160;&nbsp;{NAV_INFO}</td></tr>
<tr class="bg_medium">
<!-- BEGIN SELECT -->
<td width="2%">{SELECT_LABEL}</td>
<!-- END SELECT -->
<td width="7%" class="smalltext" align="left"><b>{ID_LABEL}&#160;{ID_ORDER_LINK}</b></td>
<td width="67%" class="smalltext" align="left"><b>{LABEL_LABEL}&#160;{LABEL_ORDER_LINK}</b></td>
<td width="12%" class="smalltext" align="left"><b>{APPROVED_LABEL}</b></td>
<td width="12%" class="smalltext" align="left"><b>{HIDDEN_LABEL}</b></td>
</tr>
{LIST_ITEMS}

<!-- BEGIN ACTION_STUFF -->
<tr class="bg_medium">
<td colspan="5" align="left">
{ACTION_SELECT} {ACTION_BUTTON}
&#160;&#160;
{NAV_LIMITS}&#160;&#160;&#160;&#160;{NAV_BACKWARD}&#160;{NAV_SECTIONS}&#160;{NAV_FORWARD}
</td>
</tr>
<!-- END ACTION_STUFF -->

</table>
