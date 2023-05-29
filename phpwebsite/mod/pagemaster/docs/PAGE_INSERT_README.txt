README FOR PAGE_INSERT

George Brackett (gbrackett@NOSPAM.luceatlux.com)
5/27/03

--------------------------------
OVERVIEW
--------------------------------
Page_insert is a set of templates for use with the PageMaster module of 
phpWebSite v 0.9+.  These templates allow the insertion of any existing
web page within the framework of a phpWS site.  They are based on the 
showinmain module written for phpWS 0.8+ by Jim Flowers (jflowers@ezo.net).
The page insert capability should work for later versions of Internet 
Explorer, Netscape Navigator, and Mozilla.

--------------------------------
INSTALLATION
--------------------------------
These templates should be installed within the theme directory for the theme(s)
you are using.  That is, for every theme you are offering to users, you
should copy the three template files into the directory

	themes/your_theme/templates/pagemaster/
	
This will make the page_insert templates available to the PageMaster module
when you are displaying the phpWS site using your_theme.  Be sure to copy
the files to all the themes you are making available, or errors will result.

The three templates are as follows:

page_insert_fit_page.tpl - This template inserts the full page into the 
	pagemaster block.  If the page being inserted is too large to fit 
	on-screen, the phpWS window will acquire a scrollbar at right.

page_insert_fit_block.tpl - This template inserts the page into the largest
	possible scrolling area that will fit within the current-sized pagemaster 
	block.

page_insert_title_is_size - This template inserts the page into a scrolling
	area of a fixed, specified height within the pagemaster block.

--------------------------------
USE
--------------------------------
After installing the templates in the theme(s) you wish to have the
page insertion capability, insert a page this way:

1.	Log in as a user with PageMaster access.  Select PageMaster.
2.	Create a new page.  Give it a title.
3.	Edit the page.
	a.	In the first section, select one of the three page_insert templates
		from the drop-down template menu.
	b.	If you chose page_insert_title_is_size, enter the height you want
		for the scrolling page display in the Section Subtitle blank.  For
		example, to display the page in a scrolling area 500 pixels high,
		enter 500 in the Subtitle blank.
	c.	Enter the URL of the page you want inserted as the text of the
		section.  The URL can be a full URL (i.e., http://mysite.com) or
		a relative link to a file on your site (e.g., downloads/cool.html).
4.	Save the section and the page.

That's it!

--------------------------------
HELP
--------------------------------
I have tested page_insert on IE 5 Mac and Mozilla 1.3 Mac.  PLEASE email me at
the address above (without the NOSPAM, of course) if you find it DOES NOT WORK 
with your browser.  That way I can warn users of incompatibilities, or 
find a fix.
