<table border="0" width="100%" cellspacing="1" cellpadding="4">
<tr><td colspan="5"><b>{TITLE}</b>&#160;{NAV_INFO}</td></tr>
<tr class="bg_medium">
<!-- BEGIN SELECT -->
<td width="3%">{SELECT_LABEL}</td>
<!-- END SELECT -->
<td width="5%" class="smalltext" align="left"><b>{ID_LABEL}&#160;{ID_ORDER_LINK}</b></td>
<td width="50%" class="smalltext" align="left"><b>{LABEL_LABEL}&#160;{LABEL_ORDER_LINK}</b></td>
<!-- BEGIN DATE_QUERY -->
<td width="7%" class="smalltext" align="left"><b>{UPDATED_LABEL}&#160;{UPDATED_ORDER_LINK}</b></td>
<!-- END DATE_QUERY -->
<td width="9.2%" class="smalltext" align="left"><b>{APPROVED_LABEL}&#160;{APPROVED_ORDER_LINK}</b></td>
<td width="8.2%" class="smalltext" align="left"><b>{HIDDEN_LABEL}&#160;{HIDDEN_ORDER_LINK}</b></td>
<td width="6.2%" class="smalltext" align="left"><b>{HITS_LABEL}&#160;{HITS_ORDER_LINK}</b></td>
<td width="10.2%" class="smalltext" align="left"><b>{AVGSCORE_LABEL}&#160;{AVGSCORE_ORDER_LINK}</b></td>
<td width="11.2%" class="smalltext" align="left"><b>{COMPSCORE_LABEL}&#160;{COMPSCORE_ORDER_LINK}</b></td>
</tr>
{LIST_ITEMS}

<!-- BEGIN ACTION_STUFF -->
<tr class="bg_medium">
<td colspan="8" align="left">
{ACTION_SELECT} {ACTION_BUTTON}
&#160;&#160;
{NAV_LIMITS}&#160;&#160;&#160;&#160;{NAV_BACKWARD}&#160;{NAV_SECTIONS}&#160;{NAV_FORWARD}
</td>
</tr>

<!-- END ACTION_STUFF -->

</table>
