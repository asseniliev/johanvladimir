<h2>{TITLE}</h2>
{START_FORM}
<!-- BEGIN HIDDENS -->
{HIDDENS}
<!-- END HIDDENS -->
<div style="color:#ff0000; font-style: italic;">{ERROR}</div>
Details:<br />
{LABEL}<br /><br />
<!-- BEGIN ADMINISTRATIVE -->
Administrative:
{ADMINISTRATIVE}
<!-- END ADMINISTRATIVE -->
<table width="100%" border="0" cellpadding="4" cellspacing="1">
<tr>
<td width="25%" nowrap="nowrap">Start on:<br />
{STARTMONTH}&#160;/&#160;{STARTDAY}&#160;/&#160;{STARTYEAR}</td>
<td nowrap="nowrap">End on:<br />
{ENDMONTH}&#160;/&#160;{ENDDAY}&#160;/&#160;{ENDYEAR}</td>
</tr>
<tr><td colspan="2">&#160;</td></tr>
<tr>
<td width="25%" nowrap="nowrap">At:<br />
{STARTHOUR}&#160;:&#160;{STARTMINUTE}&#160;{STARTAMPM}</td>
<td nowrap="nowrap">At:<br />
{ENDHOUR}&#160;:&#160;{ENDMINUTE}&#160;{ENDAMPM}</td>
</tr>
<tr><td colspan="2">&#160;</td></tr>
</table>

<!-- BEGIN REPEAT -->
{SAVE}<br /><br />
<hr />
{REPEAT} Repeat until 
{REPEATMONTH}&#160;/&#160;{REPEATDAY}&#160;/&#160;{REPEATYEAR}<br /><br />
<table border="0" width="100%" cellpadding="4" cellspacing="1">
<tr class="bg_medium"><th width="15%">Mode</th><th>Properties</th></tr>
<tr><td width="10%">{MODE_1}&#160;Daily</td><td>&#160;</td></tr>
<tr class="row-bg"><td width="10%">{MODE_2}&#160;Weekly</td>
<td>
{PROPERTIES_SUN}&#160;Sun.&#160;
{PROPERTIES_MON}&#160;Mon.&#160;
{PROPERTIES_TUE}&#160;Tue.&#160;
{PROPERTIES_WED}&#160;Wed.&#160;
{PROPERTIES_THU}&#160;Thu.&#160;
{PROPERTIES_FRI}&#160;Fri.&#160;
{PROPERTIES_SAT}&#160;Sat.&#160;
</td>
</tr>
<tr><td width="10%">{MODE_3}&#160;Monthly</td><td>{PROPERTIES_MONTHLY}</td></tr>
<tr class="row-bg"><td width="10%">{MODE_4}&#160;Yearly</td><td>&#160;</td></tr>
<tr><td width="10%">{MODE_5}&#160;Every</td><td>{PROPERTIES_EVERY}&#160;{PROPERTIES_DAY}&#160;{PROPERTIES_MONTH}</td></tr>
</table><br />
<!-- END REPEAT -->

{SAVE}
{END_FORM}
