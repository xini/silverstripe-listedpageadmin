SilverStripe Article Module
===========================
Adds the functionality to edit sitetree objects in a listed model admin environment. Also works with translatable.


## Requirements
* SilverStripe CMS 3.1.x
* excludechildren


## Installation
* Extract the downloaded archive into your site root so that the destination folder is called 'listedpageadmin'
* Run dev/build?flush=all to regenerate the manifest


## Usage
In order to edit pages in this listed view instead of the normal pages section of the CMS, the page type needs to be hiiden in the site tree.

Use this extension to hide the child page types (i.e. NewsPage) from site tree. Example:

```
	HidePageTypeSiteTreeExtension::set_hidden_classes(array('NewsPage'));
```

Also, the child pages need to inherit from the class `ListedPage`.

To edit the child page types in the listed admin, we need to define the parental structure of the pages. Therefore the custom ListedPageAdmin needs to declare this structure in the static variable `$managed_models`:

```
	class NewsAdmin extends ListedPageAdmin {
		
		private static $url_segment = 'news';
		private static $menu_title = 'News';
		
		private static $managed_models = array (
			'News' => array('NewsArchiveOverview', 'NewsPage'),
		);
		
	}
```

The structure of the definition follows the following rules:

```
	'[name of the admin section]' => array('[parent page type]', '[child page type]'),
```
