<?php
  $user_email = "Contact Email"; 
  $user_email_content = "
      Providing your email address is completely optional and will only be used if there is a question about
      the FAQ you suggested.
      ";

  $older_faqs = "Query Older FAQs"; 
  $older_faqs_content = "
      The following query will allow you to search for FAQs that have not been viewed by anyone or changed 
      by an administrator in the number of months specified.
      ";

  $basic_no_bookmarks_view = "Basic Layout - Question and Answer";
  $basic_no_bookmarks_view_content = "
     <table><tr><td>
      This option is recommend if you have a small number of FAQs.<br /><br />
       <i>Example:</i><br />
         &#160;&#160;<span style=\"color: #ff0000;\">Q</span>:&#160;&#160;Question One<br />
         &#160;&#160;<span style=\"color: #ff0000;\">A</span>:&#160;&#160;Answer One<br /><br />
         &#160;&#160;<span style=\"color: #ff0000;\">Q</span>:&#160;&#160;Question Two<br />
         &#160;&#160;<span style=\"color: #ff0000;\">A</span>:&#160;&#160;Answer One<br />
     </td></tr></table>
  ";

  $basic_bookmarks_view = "Basic Layout - Bookmarked Questions";
  $basic_bookmarks_view_content = "
     <table><tr><td>
      The questions are grouped together at the top of the page and when a question is clicked the page will jump down 
      to the appropriate question and answer.<br /><br />
      This option is recommend if you have a small number of FAQs.<br /><br />
       <i>Example:</i><br />
         &#160;&#160;<a href=\"#\">Question One</a><br />
         &#160;&#160;<a href=\"#\">Question Two</a><br /><br /><br />
         &#160;&#160;<b>Question One</b><br />
         &#160;&#160;Answer One<br /><br />
         &#160;&#160;<b>Question Two</b><br />
         &#160;&#160;Answer Two<br /><br />
     </td></tr></table>
  ";

  $category_view = "Category Layout";
  $category_view_content = "
     <table><tr><td>
       Show FAQs according to categories specified in the fatcat module.<br /><br />
       <i>Example:</i><br />
         &#160;&#160;General Category<br />
         &#160;&#160;&#160;<a href=\"#\">General Question One</a><br />
         &#160;&#160;&#160;<a href=\"#\">General Question Two</a><br />
         &#160;&#160;&#160;<a href=\"#\">General Question Three</a><br /><br />
         &#160;&#160;Specific Category<br />
         &#160;&#160;&#160;<a href=\"#\">Specific Question One</a><br />
         &#160;&#160;&#160;<a href=\"#\">Specific Question Two</a><br />
         &#160;&#160;&#160;<a href=\"#\">Specific Question Three</a><br /><br /><br />

         To have different bullet images than the ones provided by default use the built in interface to upload bullets for both the top and sub level bullets.  
         The terms top level and sub level refer to parent and child categories.
  
         To upload a new image, click Browse, point to the file, and then click 'Save Changes'.  
         Once uploaded, a preview will appear on the faq settings page with the option to delete the image.
     </td></tr></table>
  ";

  $nocat_clickable_view = "No Categories - Clickable Listing"; 
  $nocat_clickable_view_content = "
     <table><tr><td>
       FAQs can be viewed by clicking on the questions.<br /><br />
       <i>Example:</i><br />
         &#160;&#160;<a href=\"#\">Question One</a><br />
         &#160;&#160;<a href=\"#\">Question Two</a><br />
         &#160;&#160;<a href=\"#\">Question Three</a><br />
         &#160;&#160;<a href=\"#\">Question Four</a><br />
     </td></tr></table>
      ";
?>