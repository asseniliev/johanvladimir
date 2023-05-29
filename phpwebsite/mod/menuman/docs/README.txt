-----------------------------------------------------------------------------------
phpWebSite Menu Manager README
-----------------------------------------------------------------------------------
Author: Steven Levin <steven@NOSPAM.tux.appstate.com>
Version: 1.0b 10/2/2002

-------------------------------
REQUIREMENTS:
-------------------------------
phpWebSite v0.9.0

-------------------------------
INSTALL:
-------------------------------
The menuman module install is powered by the boost module installer.

1. Put menuman into the mods directory of the phpWebSite base.
2. Set you images directory in the conf/config.php file.
3. Point your browser at your site and go to the boost module.
4. Find menuman in the list and click install.
5. Enjoy!

-------------------------------
UPGRADE:
-------------------------------
The menu module upgrade is powered by the boost module.
(Not Implemented Yet)

1. Point your browser at your site and go to the boost module.
2. Find menuman in the list and click check for upgrade.
3. Boost should do the rest.
4. Enjoy!

-------------------------------
TEMPLATES
-------------------------------
See the TEMPLATE.txt file for more information on templating.


-------------------------------
DELEVOPER API
-------------------------------
If you want your module to be able to add a link to the menu all you need is one function call.

sdd_module_item($module, $op_string, $call_back, $item_active);

Here is an example from the pagemaster module.

if($GLOBALS['core']->moduleExists("menuman")) {
    $_SESSION['OBJ_menuman']->add_module_item("pagemaster", "&amp;PAGE_user_op=view_page&amp;PAGE_id=" . $this->id,  "./index.php?module=pagemaster&amp;MASTER_op=Edit&amp;PAGE_id=" . $this->id, $item_active=1);
}

Parameters:
-------------
string $module name of your module
string $op_string extra info need to link to your modules item
string $call_back how to get back after adding the link
boolean $item_active whether or not link is defaulty active
