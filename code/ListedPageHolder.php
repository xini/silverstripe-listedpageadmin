<?php
class ListedPageHolder extends Page {
	
	private static $extensions = array("ExcludeChildren");
	private static $excluded_children = array('ListedPage');
	
	// overwrites cms link used in translations
	public function CMSEditLink() {
		$class = Session::get("ListedPageAdmin.currentAdminClass");
		if ($class) {
			return singleton($class)->LinkPageEdit($this->ID);
		}
		return null;
	}
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("MenuTitle"); 
		$fields->removeByName("MenuTitle_original");
		return $fields;
	}
}

class ListedPageHolder_Controller extends Page_Controller {
	
}