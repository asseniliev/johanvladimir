{START_FORM}
<div style="margin-left:10px;">
{ENABLE_WEBSTATS_FLD}{ENABLE_WEBSTATS_LBL}
&nbsp;&nbsp;&nbsp;&nbsp;{SAVE_WEBSTATS_SETTINGS}
<br /><br /><br />

<!-- BEGIN VIEW_WEBSTATS2 -->
<div style="width:60%">
<div style="margin-right:15px;float:left;">
<i>{HITS_LBL}</i><br /><br />
<b>{TODAYS_HITS_LBL}</b>: {TODAYS_HITS_FLD}<br />
<b>{CURR_MONTHS_HITS_LBL}</b>: {CURR_MONTHS_HITS_FLD}
</div>
<div style="float:right;">
<i>{MOST_LOGIN_TITLE}</i><br /><br />
<table cellpadding="0">
<tr><th>{USERNAME_TITLE}&nbsp;&nbsp;&nbsp;&nbsp;</th><th>{HITS_TITLE}</th></tr>
{USER_ROWS}
</table>
</div>
</div>
<!-- END VIEW_WEBSTATS2 -->

<br clear="all"/>
<br /><br />
<hr />

<a name="viewgraph" id="viewgraph"></a>

<!-- BEGIN VIEW_WEBSTATS -->
<br />
<div style="text-align:center;">
<i>{VIEW_GRAPH_LBL}</i><br /><br />
<!-- BEGIN SITE_FLD -->
{SITE_LABEL}:&nbsp;&nbsp;{SITE_FLD}<br /><br />
<!-- END SITE_FLD -->
{GRAPH_MONTH_LBL}:&nbsp;&nbsp;{VIEW_GRAPH_FLD}&nbsp;&nbsp;{GRAPH_YEAR_LBL}:&nbsp;&nbsp;{GRAPH_YEAR_FLD_YEAR}
{VIEW_GRAPH_BTN}
<br />
</div>

<br />
<table width="100%" align="center">
<tr>
<td>
<!-- BEGIN VIEW_WEBSTATS -->
<!-- BEGIN BAR_GRAPHS -->
<table align="center" cellspacing="10" width="100%">
<!-- BEGIN CURR_MONTH_BAR_GRAPH -->
<tr><td align="center">
<b>{CURR_MONTH_TITLE}</b><br />
{CURR_MONTH_BAR_GRAPH}
</td></tr>
<!-- END CURR_MONTH_BAR_GRAPH -->
<!-- BEGIN ALL_MONTHS_BAR_GRAPH -->
<tr><td align="center">
<b>{ALL_MONTHS_TITLE}</b><br />
{ALL_MONTHS_BAR_GRAPH}
</td></tr>
<!-- END ALL_MONTHS_BAR_GRAPH -->
</table>
<!-- END BAR_GRAPHS -->

</td>
</tr></table>
<!-- END VIEW_WEBSTATS -->

</div>
{END_FORM}
<!-- BEGIN GD_MSG -->
<br />
<i>{GD_MSG}</i>
<!-- END GD_MSG -->
