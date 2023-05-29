<h2>Schedule Search</h2>
<!-- BEGIN ERROR -->
<div class="error">{ERROR}</div><br />
<!-- END ERROR -->
<!-- BEGIN FORM -->
{START_FORM}
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="middle">Search&#160;&#160;</td>
<td align="left" valign="middle">{SCHEDULES}</td> 
<td align="left" valign="middle">&#160;&#160;between:</td>
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
</table><br />
Query:<br />
{QUERY}
<br /><br />
{SEARCH}<br />
<!-- BEGIN RESULTS -->
<br />
{RESULTS}
<!-- END RESULTS -->
{END_FORM}
<!-- END FORM -->
