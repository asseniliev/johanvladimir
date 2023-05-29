<br />
<b>{SEND_OPTION_LABEL}</b><br />
{START_FORM}
<!-- BEGIN TABLE -->
<table border="0">
<!-- BEGIN SEND_ALL_USERS -->
<tr><td>{SEND_OPTION_1}{SEND_OPTION}&nbsp;{ALL_USERS_LABEL}</td></tr>
<!-- END SEND_ALL_USERS -->
<!-- BEGIN SEND_TO_GROUPS -->
<tr><td>{SEND_OPTION_2}&nbsp;{GROUPS_LABEL}&nbsp;{TO_GROUP_FIELD}&nbsp;<span class="smalltext">{VIEW_GROUPS}</span><br /><br /></td></tr>
<!-- END SEND_TO_GROUPS -->
<!-- BEGIN SUBJECT -->
<tr><td><b>{SUBJECT_LABEL}</b><br />{SUBJECT_FIELD}</td></tr>
<!-- END SUBJECT -->
<tr><td>&nbsp;</td></tr>
<!-- BEGIN MESSAGE -->
<tr><td><b>{MESSAGE_LABEL}</b><br />{MESSAGE_FIELD}</td></tr>
<!-- END MESSAGE -->
<!-- BEGIN SUBMIT_BUTTON -->
<tr><td>{SEND_BUTTON}</td></tr>
<!-- END SUBMIT_BUTTON -->
</table>
<!-- END TABLE -->
{END_FORM}
