<table border="0" width="100%" cellspacing="1" cellpadding="4">
<tr><td class="smalltext" colspan="7"><b>{TITLE}</b>&#160;{NAV_INFO}</td></tr>
<tr class="bg_medium">
<!-- BEGIN SELECT -->
<td width="5%">{SELECT_LABEL}</td>
<!-- END SELECT -->
<td class="smalltext" align="left" nowrap="nowrap"><b>{LABEL_LABEL}&#160;{LABEL_ORDER_LINK}</b></td>
<td width="30%" class="smalltext" align="left" nowrap="nowrap"><b>{BODY_LABEL}&#160;{BODY_ORDER_LINK}</b></td>
<td class="smalltext" align="center" nowrap="nowrap"><b>{ACTIVE_LABEL}&#160;{ACTIVE_ORDER_LINK}</b></td>
<td class="smalltext" align="center" nowrap="nowrap"><b>{RESTRICTED_LABEL}&#160;{RESTRICTED_ORDER_LINK}</b></td>
<td class="smalltext" align="center" nowrap="nowrap"><b>{ALLOWCOMMENTS_LABEL}&#160;{ALLOWCOMMENTS_ORDER_LINK}</b></td>
<td class="smalltext" align="center" nowrap="nowrap"><b>{CREATED_LABEL}&#160;{CREATED_ORDER_LINK}</b></td>
<td class="smalltext" align="center" width="15%" nowrap="nowrap"><b>{HIDDEN_LABEL}&#160;{HIDDEN_ORDER_LINK}</b></td>
</tr>
{LIST_ITEMS}
</table>

<!-- BEGIN ACTION_STUFF -->
<table border="0" width="100%" cellspacing="1" cellpadding="4">
<tr>
<td width="33%">&nbsp;</td>
<td width="33%" align="center">{NAV_BACKWARD}&#160;{NAV_SECTIONS}&#160;{NAV_FORWARD}<br />{NAV_LIMITS}</td>
<td width="33%" align="right">{ACTION_SELECT} {ACTION_BUTTON}</td>
</tr>
</table>
<!-- END ACTION_STUFF -->
