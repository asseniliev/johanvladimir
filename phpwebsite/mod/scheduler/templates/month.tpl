<table border="0" width="100%" class="scheduler">
<tr>
<td width="35%">
<h2>Today is:</h2>
<h3>{TODAY}</h3>
{START_FORM}
<span class="scheduler">Select the schedule to view:</span>&#160;{SCHEDULE}&#160;{GO}<br /><br />
{SEARCHSCHEDULE}&#160;&#160;{FINDOPENINGS}
{END_FORM}
</td>
<td width="20%" valign="top">
{PREV_SMALL}
</td>
<td width="5%">
&#160;
</td>
<td width="20%" valign="top">
{NEXT_SMALL}
</td>
</tr>
<!-- BEGIN CONFLICTS -->
<tr>
<td colspan="4">
{CONFLICTS}
</td>
</tr>
<!-- END CONFLICTS -->
</table>
<hr />

<div align="center">
<a href="./index.php?module=scheduler&amp;op=month&amp;ts={PREV_MONTH}" title="View previous month"
onmouseover="window.status='View previous month'; return true;" onmouseout="window.status='';">&lt;&lt;</a>
&#160;{DATE}&#160;
<a href="./index.php?module=scheduler&amp;op=month&amp;ts={NEXT_MONTH}" title="View next month"
onmouseover="window.status='View next month'; return true;" onmouseout="window.status='';">&gt;&gt;</a>
</div><br />
<table width="100%" border="0" class="scheduler"><tr><td bgcolor="#c1c1c1">
<table width="100%" border="0" cellpadding="5" cellspacing="1">
<tr>
<td align="center" bgcolor="#ffffff">Monday</td>
<td align="center" bgcolor="#ffffff">Tuesday</td>
<td align="center" bgcolor="#ffffff">Wednesday</td>
<td align="center" bgcolor="#ffffff">Thursday</td>
<td align="center" bgcolor="#ffffff">Friday</td>
<td align="center" bgcolor="#ffffff">Saturday</td>
<td align="center" bgcolor="#ffffff">Sunday</td>
</tr>
{ROWS}
</table>
</td></tr></table>
