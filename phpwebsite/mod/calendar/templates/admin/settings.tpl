{START_FORM}
<table>
  <tr>
    <td valign="top">
      <table cellpadding="5"  border="0">
        <tr class="bg_light">
          <td><b>{VIEWS}</b></td>
        </tr>
        <tr>
	  <td>{MINIMONTH}{MINIMONTH_LABEL}<br />{TODAY}{TODAY_LABEL}<br />{DAYSAHEAD}{DAYSAHEAD_LABEL}</td>
        </tr>
      </table>
    </td>
    <td valign="top">
      <table cellpadding="5"  border="0">
        <tr class="bg_light">
          <td><b>{OTHER_SETTINGS}</b></td>
        </tr>
        <tr>
          <td>{CACHEVIEW}{CACHEVIEW_LABEL}<br />{USERSUBMIT}{USERSUBMIT_LABEL}
	  <br />{REINDEXFATCAT}{REINDEX_LABEL}
	  <br />{SEARCH_PAST}{SEARCH_PAST_LABEL}
	  <br />{SESSIONVIEW}{SESSIONVIEW_LABEL}
	  <br />{RESTRICT_VIEW}{RESTRICT_VIEW_LABEL}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{DEFAULT_SUBMIT}
<br /><br />
<table cellpadding="5"  border="0">
  <tr class="bg_light">
   <td><b>{PURGE_SETTINGS}</b></td>
  </tr>
  <tr>
   <td>
       {PURGE_FATCAT_MONTH}{PURGE_FATCAT_DAY}{PURGE_FATCAT_YEAR}{PURGE_FATCAT_CERTAIN}
       <br />{PURGE_FATCAT_BUTTON}
   </td>
  </tr>
</table>
{END_FORM}
