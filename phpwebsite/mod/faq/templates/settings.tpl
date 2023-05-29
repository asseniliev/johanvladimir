<!-- BEGIN TITLE -->
<i>{TITLE}</i><br /><br />
<!-- END TITLE -->

<!-- BEGIN MESSAGE -->
<b>>>{MESSAGE}</b><br /><br />
<!-- END MESSAGE -->

<!-- BEGIN OPTIONS_HEADER -->
<b>{OPTIONS_HEADER_LABEL}</b>
<hr align="left" width="25%" />
<br />
<!-- END OPTIONS_HEADER -->

<table border="0">
<!-- BEGIN ALLOW_RATING_OPTION -->
<tr>
<td>{ALLOW_ANON_CHECKBOX}</td>
<td>{ALLOW_ANON_LABEL}</td>
</tr>
<!-- END ALLOW_RATING_OPTION -->

<!-- BEGIN ALLOW_COMMENTS -->
<tr>
<td>{ALLOW_COMMENTS_CHECKBOX}</td>
<td>{ALLOW_COMMENTS_LABEL}</td>
</tr>
<!-- END ALLOW_COMMENTS -->

<!-- BEGIN ALLOW_SUGGESTIONS_OPTION -->
<tr>
<td>{ALLOW_SUGGESTIONS_CHECKBOX}</td>
<td>{ALLOW_SUGGESTIONS_LABEL}</td>
</tr>
<!-- END ALLOW_SUGGESTIONS_OPTION -->
</table>

<!-- BEGIN ADD_TO_MENU -->
<table><tr>
<td colspan="2"><br />{ADD_TO_MENU_LABEL}<br /></td>
</tr></table>
<!-- END ADD_TO_MENU -->


<p align="right">
{SUBMIT_BUTTON}
</p>

<!-- BEGIN LAYOUT_OPTIONS -->
<b>{LAYOUT_VIEW_HEADER_LABEL}</b>
<hr align="left" width="25%" />

<table border="0">
<tr>
<td colspan="3">&#160;</td>
</tr>

<!-- BEGIN BASIC_QA_HEADER -->
<tr>
<td>{BASIC_QA_LAYOUT_RADIO}</td>
<td colspan="2">{BASIC_QA_LAYOUT_TITLE}</td>
</tr>
<!-- END BASIC_QA_HEADER -->

<!-- BEGIN BASIC_QA_LAYOUT_PAGING_FIELD -->
<tr>
 <td>&#160;</td>
 <td valign="top">&#160;&#160;{BASIC_QA_LAYOUT_PAGINGLIMIT}</td>
 <td>&#160;</td>
</tr>
<!-- END BASIC_QA_LAYOUT_PAGING_FIELD -->

<!-- BEGIN BASIC_QA_USEBOOKMARKS -->
<tr>
 <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{BASIC_QA_USEBOOKMARKS_RADIO}
                 {BASIC_QA_USEBOOKMARKS_TITLE}&nbsp;&nbsp;{BASIC_QA_USEBOOKMARKS_HELP}</td>
</tr>
<!-- END BASIC_QA_USEBOOKMARKS -->

<!-- BEGIN BASIC_QA_NOBOOKMARKS -->
<tr>
 <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{BASIC_QA_NOBOOKMARKS_RADIO}
 	         {BASIC_QA_NOBOOKMARKS_TITLE}&nbsp;&nbsp;{BASIC_QA_NOBOOKMARKS_HELP}</td>
</tr>
<!-- END BASIC_QA_NOBOOKMARKS -->

<tr>
<td colspan="3">&#160;</td>
</tr>


<!-- BEGIN LISTING_LAYOUT -->
<tr>
 <td>{LISTING_LAYOUT_RADIO}</td>
 <td colspan="2">{LISTING_LAYOUT_TITLE}&nbsp;&nbsp;{LISTING_LAYOUT_HELP}</td>
</tr>
<!-- END LISTING_LAYOUT -->

<!-- BEGIN CATEGORY_LAYOUT -->
<tr>
<td colspan="3">&#160;</td>
</tr>
<tr>
<td valign="top">{CATEGORY_LAYOUT_RADIO}</td>
<td valign="top" colspan="2">{CATEGORY_LAYOUT_TITLE}&nbsp;&nbsp;{CATEGORY_LAYOUT_HELP}</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>

<tr><td>&nbsp;</td><td colspan="2">
	           {CATEGORY_TOP_TEXT}&nbsp;&nbsp;
	           {CATEGORY_TOP_FIELD}</td></tr>
<!-- BEGIN CATEGORY_TOP_IMAGE -->
<tr><td>&nbsp;</td><td colspan="2">
	{CATEGORY_TOP_IMAGE}&nbsp;&nbsp;{CATEGORY_TOP_REMOVE}
	<br /><br />
</td></tr>
<!-- END CATEGORY_TOP_IMAGE -->

<tr><td>&nbsp;</td><td colspan="2">
	           {CATEGORY_SUBL_TEXT}&nbsp;&nbsp;
	           {CATEOGRY_SUBL_FIELD}</td></tr>
<!-- BEGIN CATEGORY_SUB_IMAGE -->
<tr><td>&nbsp;</td><td colspan="2">
	{CATEGORY_SUB_IMAGE}&nbsp;&nbsp;{CATEGORY_SUB_REMOVE}
	<br /><br />
</td></tr>
<!-- END CATEGORY_SUB_IMAGE -->

<tr><td>&nbsp;</td><td colspan="2"><br />{CATEGORY_DEFAULT_CB}
	           {HIGHLIGHT_START}{CATEGORY_DEFAULT_TEXT}{HIGHLIGHT_END}</td></tr>

<tr><td>&nbsp;</td><td colspan="2">{CB_SHOW_UPDATED}{SHOW_UPDATED_TEXT}</td></tr>
<tr><td>&nbsp;</td><td colspan="2">{CB_SHOW_NUM_FAQS}{SHOW_NUM_FAQS_TEXT}</td></tr>


<!-- END CATEGORY_LAYOUT -->

</table>
<!-- END LAYOUT_OPTIONS -->

<p align="right">
{SUBMIT_BUTTON}
</p>

<!-- BEGIN SORTING_HEADER -->
<table>
<tr><td><br /><b>{SORTING_TITLE}</b><br /></td></tr>
</table>
<br /><hr align="left" width="25%" />

<p><span class="smalltext">{SORTING_NOTICE}</span><br /></p>
<!-- END SORTING_HEADER -->

<!-- BEGIN SORTING_OPTIONS -->
<table>
<tr><td>{RD_SORTING_COMPSCORE}</td><td>{TX_SORTING_COMPSCORE}</td></tr>
<tr><td>{RD_SORTING_UPDATED}</td><td>{TX_SORTING_UPDATED}</td></tr>
<tr><td>{RD_SORTING_QUESTION}</td><td>{TX_SORTING_QUESTION}</td></tr>
</table>
<!-- END SORTING_OPTIONS -->

<!-- BEGIN LEGEND_HEADER -->
<br /><br />
<table>
<tr>
<td><br /><b>{LEGEND_TITLE}</b><br /></td>
</tr>
</table>
<hr align="left" width="25%" /><br />
<!-- END LEGEND_HEADER -->

<!-- BEGIN LEGEND_FORM -->
<table>
{SCORE_LABEL_LIST}
</table>
<!-- END LEGEND_FORM -->


