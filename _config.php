<?php

define('LISTEDPAGEADMIN_BASE', basename(dirname(__FILE__)));

// remove parent class from menu
CMSMenu::remove_menu_item('ListedPageAdmin'); 
CMSMenu::remove_menu_item('ListedPageEditController');
CMSMenu::remove_menu_item('ListedPageSettingsController');
CMSMenu::remove_menu_item('ListedPageHistoryController');
CMSMenu::remove_menu_item('ListedPageAddController');
