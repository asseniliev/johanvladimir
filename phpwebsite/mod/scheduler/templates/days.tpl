<!-- BEGIN FORM -->
{START_FORM}
<table width="100%" border="0" class="scheduler">
<tr>
<td width="50%" align="left" valign="top">
<h2>Today is:</h2>
<h2>{TODAY}</h2>
Select the schedule to view:&#160;{SCHEDULE}&#160;{GO}<br /><br />
{SEARCHSCHEDULE}&#160;&#160;{FINDOPENINGS}&#160;&#160;
<input type="button" value="Print"
onClick="loc='./index.php?module=scheduler&amp;op=print'; window.open(loc);"
/>
<!-- BEGIN CONFLICTS -->
<br /><br />
{CONFLICTS}
<!-- END CONFLICTS -->
</td>
<td width="20%" valign="middle">
<table border="0" cellpadding="1" cellspacing="2">
<tr><td width="5%" class="today-border">&#160;&#160;&#160;</td><td>Today</td></tr>
<tr><td class="selected-border">&#160;&#160;&#160;</td><td>Selected</td></tr>
</table>
</td>
<td width="30%" align="right" valign="top">
{SMALL_MONTH}
</td>
</tr>
</table>
{END_FORM}
<hr />
<!-- END FORM -->

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="scheduler">
<tr>
<td width="50%" align="center">
<a href="./index.php?module=scheduler&amp;op=days&amp;ts={PREV_DAY}" title="View previous day"
onmouseover="window.status='View previous day'; return true;" onmouseout="window.status='';">&lt;&lt;</a>
&#160;{DATE1}
</td>
<td width="50%" align="center">
{DATE2}&#160;
<a href="./index.php?module=scheduler&amp;op=days&amp;ts={NEXT_DAY}" title="View next day"
onmouseover="window.status='View next day'; return true;" onmouseout="window.status='';">&gt;&gt;</a>
</td>
</tr>
<tr><td colspan="2">&#160;</td></tr>
<tr>
<td valign="top">
<table width="100%" border="0"><tr><td class="shade">

<table width="100%" border="0" cellpadding="5" cellspacing="1">
{DAY1}
</table>

</td></tr></table>
</td>
<td valign="top">
<table width="100%" border="0"><tr><td class="shade">

<table width="100%" border="0" cellpadding="5" cellspacing="1">
{DAY2}
</table>

</td></tr></table>
</td>
</tr>
</table>
