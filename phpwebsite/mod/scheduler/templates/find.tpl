<h2>Find Schedule Openings</h2>
<!-- BEGIN ERROR -->
<div class="error">{ERROR}</div><br />
<!-- END ERROR -->
{START_FORM}
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="middle">Search&#160;&#160;</td>
<td align="left" valign="middle">{SCHEDULES}</td> 
<td align="left" valign="middle">&#160;&#160;for openings between:</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="1">
<tr><td colspan="2">&#160;</td></tr>
<tr>
<td width="25%">Start date:<br />
{STARTMONTH}&#160;/&#160;{STARTDAY}&#160;/&#160;{STARTYEAR}</td>
</tr>
<tr><td>&#160;</td></tr>
<tr>
<td>End date:<br />
{ENDMONTH}&#160;/&#160;{ENDDAY}&#160;/&#160;{ENDYEAR}</td>
</tr>
<tr><td>&#160;</td></tr>
<tr><td>For a time block of: {HOURS} hour(s)  {MINUTES} minutes</td></tr>
</table>
<br />
{FIND}<br /><br />
<!-- BEGIN RESULTS -->
{RESULTS}

<!-- BEGIN ADD_ENTRY -->
&#160;{ADD_ENTRY}
<!-- END ADD_ENTRY -->

<!-- BEGIN FIND_NEXT -->
&#160;&#160;{FIND_NEXT}
<!-- END FIND_NEXT -->

<!-- END RESULTS -->
{END_FORM}
