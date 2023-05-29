<script type="text/javascript">
//<![CDATA[

function suggest(form, value) {
   if(form != null && value != "" && value != "empty") {
     {JS_VARS}

     form.name_field.value = eval(value+"Title");
     form.tb_link.value = eval(value+"Link");
     form.tableField.value = value;
   }
}

//]]>
</script>

<div style="text-align:right;">{BACK_LINK}</div>
{START_FORM}
<table cellspacing="10">
<tr><td align="top"><b>{TABLE_LABEL}</b></td><td>{TABLEFIELD}</td></tr>
<tr><td align="top"><b>{NAME_LABEL}</b></td><td>{NAME_FIELD}</td></tr>
</table>

<br />
<table>
<tr><td>{CB_LINK}&nbsp;<b>{LINK_LABEL}</b></td><td>&nbsp;&nbsp;&nbsp;&nbsp;{TB_LINK_LABEL}&nbsp;&nbsp;{TB_LINK}</td></tr>
</table>
<br /><br />
{SAVE_COUNTER}
{END_FORM}

