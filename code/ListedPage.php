<?php
class ListedPage extends Page implements HiddenClass {
	
	private static $defaults = array(
		'ShowInMenus' => false,
		'ShowInSitemap' => false,
	);
	
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
		
		if ($this->hasExtension('Translatable')) {
			$fields->insertBefore(LiteralField::create('langinfo', '<p class="langinfo">'._t('ListedPage.CURRENTLANGUAGE', 'Current Language').': '.i18n::get_language_name(i18n::get_lang_from_locale($this->Locale), true).'</p>'), 'Root');
		}
		
		return $fields;
	}
}

class ListedPage_Controller extends Page_Controller {
	
}