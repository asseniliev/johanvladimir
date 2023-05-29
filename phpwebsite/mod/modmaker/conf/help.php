<?php

$create_module_entry = "Create Module Entry";
$create_module_entry_content = "This option is used by module developers to help with module creation. Its info can also be stored in the boost.php file.";

$edit_module = "Edit Module info";
$edit_module_content = "This section allows you to edit mod maker data for the selected module.";

$act_deact = "Activate / Deactivate";
$act_deact_content = "This allows you to turn any module on or off.";

$mod_title = "Mod Title";
$mod_title_content = "This should be a simple 8-12 character title for your module. phpWebSite indexes the modules by this name, so it should also be unique. Do not include non-alphanumberic characters or spaces. Think of it as a file name.";

$proper_name = "Proper Name";
$proper_name_content = "Enter a short proper name for your module. It appears as the module gif\'s alt tag.";

$mod_directory = "Mod Directory";
$mod_directory_content = "The directory where your files are located. Usually the same as the mod_title. Do not enter any slashes.";

$mod_filename = "Mod Filename";
$mod_filename_content = "The core will load ONE file that decides the action of the module. Enter the name of that file here.";

$admin_operation = "Admin Operation";
$admin_operation_content = "When an administrator clicks on your module, you can specify to include an operation. For example : \'&op=run_admin_options\'";

$user_operation = "User Operation";
$user_operation_content = "When an user clicks on your module, you can specify to include an operation";

$runtime_operation = "Runtime Operation";
$runtime_operation_content = "If you want an specific operation to run when the module is initialized.";

$allow_view = "Allow View";
$allow_view_content = "Sometimes you may only want your module to be loaded on certain conditions. For instance, mainpage loads on the home page but not when any other modules are run. However, the user module has to load no matter what other module is loaded. Check the radio button for \'All\' if your module must always be loaded. Otherwise, click the \'Only\' option then choose the other modules you want your module to initialize on. Your module will default to an \'On\' position.";

$priority = "Priority";
$priority_content = "You may control when module is loaded. As a default, the users module is loaded 1st as it is needed by all other modules. The layout module is loaded last (priority 99) because it finalizes the page. If it doesn\'t matter when you module is loaded, just enter 50. If your module is dependant on another module loading, enter a priority higher than that module. Enter a lower priority if another module depends upon yours. Remember that all classes are loaded when the program is started, so you may be able to access functions in a class without the module loaded.";

$icon_name = "Icon Name";
$icon_name_content = "If you are using an icon to identify your admin or user module, enter its filename here.";

$administrator_module = "Administrator Module";
$administrator_module_content = "If your module allows the a standard user to make changes, check the User box. If you module has administrative functions, check the Administrator box. If your module runs without any interaction, just leave these unchecked.";

$class_files = "Class Files";
$class_files_content = "If you are you using classes in your module, it is STRONGLY suggested you enter the name of those files here. phpWebSite loads all the classes on initialization. If you have more than one class file, separate them with commas. If you enter them here, they do not need to be declared in your module.";

$session_variables = "Session Variables";
$session_variables_content = "If you are using sessions in your module, it is suggested you enter them here.";

$initialize_class_name = "Initialize Class Name";
$initialize_class_name_content = "If you are using classes, you may want a particular object declared immediately before the module is loaded. This allows modules with a required lower priority to take advantage of that object\'s variables and methods. Enter the name of the object and then the name of the class it uses.";

$legacy = "Legacy";
$legacy_content = "If you are trying to install a module from the 0.8.x series, check this box.";

$deity_only = "Deity Only";
$deity_only_content = "Sometimes a select few need to use your module (like the one you are using for example). Check this option if you only want deities to have permission to access this module.";
?>
