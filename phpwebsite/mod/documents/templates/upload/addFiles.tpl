{BACK_LINK}<br /><br />
<!-- BEGIN UPLOAD_FORM -->
<form name="JAS_Files_upload" method="post" action="index.php" enctype="multipart/form-data">
<input type="hidden" name="module" value="documents" />
<input type="hidden" name="JAS_Files_op" value="uploadFiles" />

<!-- BEGIN SELECT -->
{FILE_NUM_TEXT}<br />
{FILE_NUM_SELECT}&#160;<input type="submit" name="JAS_Files_update" value="{UPDATE_BUTTON_TEXT}" /><br /><br />
<!-- END SELECT -->

<!-- BEGIN ELEMENTS -->
{FILE_UPLOAD_TEXT}<br />
{FILE_UPLOAD_ELEMENTS}<br /><br />
<!-- END ELEMENTS -->

<input type="submit" value="{UPLOAD_BUTTON_TEXT}" />
</form>
<!-- END UPLOAD_FORM -->
