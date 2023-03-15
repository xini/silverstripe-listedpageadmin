# SilverStripe ListedPageAdmin Module

**This is an archived project and is no longer maintained. Please do not file issues or pull-requests against this repo. If you wish to continue to develop this code yourself, we recommend you fork it or contact us.**

Adds the functionality to edit sitetree objects in a listed model admin environment and hide them from the site tree. Also works with translatable.

## Requirements
* SilverStripe CMS 3.1.x
* https://github.com/micschk/silverstripe-excludechildren


## Installation
* Extract the downloaded archive into your site root so that the destination folder is called 'listedpageadmin'
* Run dev/build?flush=all to regenerate the manifest


## Usage
To edit the child page types in the listed admin, we need to define the parental structure of the pages. Therefore the custom ListedPageAdmin needs to declare this structure in the static variable `$managed_models`:

```
	class NewsAdmin extends ListedPageAdmin {
		
		private static $url_segment = 'news';
		private static $menu_title = 'News';
		
		private static $managed_models = array (
			'News' => array('NewsHolder', 'NewsPage'),
		);
		
	}
```

The structure of the definition follows the following rules:

```
	'[name of the admin section]' => array('[parent page type]', '[child page type]'),
```

The Holder class needs to extend ListedPageHolder and the (hidden) Page from ListedPage.

```
	class NewsHolder extends ListedPageHolder {
		...
	}

	class NewsPage extends ListedPage {
		...
	}
```
