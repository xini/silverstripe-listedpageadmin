<?php
class ListedPageAdmin extends CMSMain {
	
	/**
	 * List of managed page types in this interface.
	 * 
	 * Format:
	 * <code>
	 * array (
	 *     '{Title}' => array('{Parent Class Name}', '{Item Class Name}'),
	 * )
	 * </code>
	 * 
	 * Example:
	 * <code>
	 * array (
	 *     'News' => array('NewsHolder', 'NewsPage'),
	 * )
	 * </code>
	 * 
	 * @var array
	 */
	private static $managed_models = null;
	
	private static $url_segment = 'listedpages';
	private static $url_rule = '/$Action/$ID/$OtherID';
	private static $menu_title = 'Listed Pages';
	private static $required_permission_codes = 'CMS_ACCESS_CMSMain';
	private static $session_namespace = 'ListedPageAdmin';
		
	private static $allowed_actions = array(
		'add',
		'ListViewForm',
		'SearchForm',
	);
	
	public function init() {
		parent::init();
		// save current class to session for page CMSEditLink() (used for translations)
		Session::set($this->sessionNamespace() . ".currentAdminClass", $this->class);
		// get requirements
		Requirements::css(LISTEDPAGEADMIN_BASE.'/css/screen.css');
	}
	
	public function getManagedModels() {
		$models = new ArrayList();
		// get current selected model
		$requestParentID = $this->request->requestVar('ParentID');
		if (!$requestParentID) {
			$requestParentID = true;
		}
		// load defined models
		$stat = $this->stat('managed_models');
		if ($stat && count($stat) > 0) {
			foreach ($stat as $title => $classes) {
				if ($classes && count($classes) == 2) {
					$parentClassName = $classes[0];
					$parents = $parentClassName::get()->filter(array('ClassName' => $parentClassName));
					if ($parents && $parents->count() > 0) {
						foreach ($parents as $parent) {
							$model = new ArrayData(array(
								'Title' => $title,
								'ItemClass' => $classes[1],
								'ParentClass' => $classes[0],
								'ParentID' => $parent->ID,
								'Current' => (is_int($requestParentID) && (int) $requestParentID == $parent->ID) || $requestParentID == true ? true : false,
								'LinkListView' => Controller::join_links($this->LinkListView(), "?ParentID=".$parent->ID)
							)); 
							$models->add($model);
							$requestParentID = false;
						}
					} else {
						user_error(
							'ListedPageAdmin::getManagedModels(): 
							You need to create at least one page of the parent page types ('.$parentClassName.') specified in your $managed_models.', 
							E_USER_ERROR
						);
					}
				} else {
					user_error(
						'ListedPageAdmin::getManagedModels(): 
						You need to specify the item class name as well as the parent class name in public static $managed_models.', 
						E_USER_ERROR
					);
				}
			}
		} else {
			user_error(
				'ListedPageAdmin::getManagedModels(): 
				You need to specify at least one SiteTree subclass in public static $managed_models.
				Make sure that this property is defined, and that its visibility is set to "public"', 
				E_USER_ERROR
			);
		}
		return $models;
	}
	
	public function getCurrentParentID() {
		$parentID = $this->request->requestVar('ParentID');
		if (!$parentID) {
			$models = $this->getManagedModels();
			if ($models && $models->first()) {
				$parentID = $models->first()->getField('ParentID');
			}
		}
		return $parentID;
	}
		
	public function getModelClass($ParentID) {
		$models = $this->getManagedModels();
		foreach ($models as $model) {
			if ($model->getField('ParentID') == $ParentID) {
				return $model->getField('ItemClass');
			}
		}
		return null;
	}
	
	public function LinkPages() {
		return $this->Link();
	}
	
	public function LinkListView() {
		return $this->LinkWithSearch($this->Link('listview'));
	}

	public function LinkPageEdit($id = null) {
		if(!$id) $id = $this->currentPageID();
		return $this->LinkWithSearch(
			Controller::join_links($this->Link('show'), $id)
		);
	}

	public function LinkPageAdd($extra = null, $placeholders = null) {
		$link = $this->Link('add');
		$this->extend('updateLinkPageAdd', $link);
		if($extra) {
			$link = Controller::join_links($link, $extra);
		}
		if($placeholders) {
			$link .= (strpos($link, '?') === false ? "?$placeholders" : "&amp;$placeholders");
		}
		return $link;
	}
	
	public function SearchForm() {
		$form = parent::SearchForm();
		$fields = $form->Fields();
		$fields->removeByName('q[ClassName]');
		return $form;
	}
	
	public function Breadcrumbs($unlinked = false) {
		$defaultTitle = LeftAndMain::menu_title_for_class($this->class);
		
		$parentID = $this->getCurrentParentID();
		$models = $this->getManagedModels();
		foreach ($models as $model) {
			if ($model->getField('ParentID') == $parentID) {
				$defaultTitle = $model->getField('Title');
			}
		}
		$title = _t("{$this->class}.MENUTITLE", $defaultTitle);
		
		$items = new ArrayList(array(
			new ArrayData(array(
				'Title' => $title,
				'Link' => ($unlinked) ? false : Controller::join_links($this->LinkPages(), "?ParentID=".$parentID)
			))
		));
		
		$record = $this->currentPage();
		if($record && $record->exists()) {
			$items->push(new ArrayData(array(
				'Title' => $record->Title,
				'Link' => ($unlinked) ? false : Controller::join_links($this->Link('show'), $record->ID)
			)));	
		}
		return $items;
	}
	
	public function currentPageID() {
		if($this->request->requestVar('ID') && is_numeric($this->request->requestVar('ID')))	{
			return $this->request->requestVar('ID');
		} elseif (isset($this->urlParams['ID']) && is_numeric($this->urlParams['ID'])) {
			return $this->urlParams['ID'];
		} elseif(Session::get($this->sessionNamespace() . ".currentPage")) {
			return Session::get($this->sessionNamespace() . ".currentPage");
		} else {
			return null;
		}
	}
		
	public function getList($params, $parentID = 0) {
		$list = null;
		$models = $this->getManagedModels();
		if ($models && $models->first()) {
			$parentID = $parentID > 0 ? $parentID : $models->first()->getField('ParentID');
			$list = parent::getList($params, $parentID);
			$list = $list->sort('Created DESC');
		}
		return $list;
	}
	
	public function ListViewForm() {
		
		$form = parent::ListViewForm();
		
		// get gridfield
		$gridField = $form->Fields()->fieldByName('Page');
		
		// remove up-link
		$gridField->getConfig()->removeComponentsByType('GridFieldLevelup');
		
		// update edit link
		$controller = $this;
		$columns = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
		$columns->setFieldFormatting(array(
			'listChildrenLink' => function($value, &$item) use($controller) {
				return null;
			},
			'getTreeTitle' => function($value, &$item) use($controller) {
				return '<a class="action-detail" href="' . Controller::join_links($controller->Link('show'), $item->ID) . '">' . $item->TreeTitle . '</a>';
			}
		));
		
		// add summary fields
		$parentID = $this->getCurrentParentID();
		$className = $this->getModelClass($parentID);
		$summaryFields = singleton($className)->summaryFields();
		if ($summaryFields && count($summaryFields) > 0) {
			
			// get current fields and formatting from grid field
			$fields = $columns->getDisplayFields($gridField);
			$formatting = $columns->getFieldFormatting();
			// remove default fields
			if (!isset($summaryFields['Created'])) {
				unset($fields['Created']);
				unset($formatting['Created']);
			}
			if (!isset($summaryFields['LastEdited'])) {
				unset($fields['LastEdited']);
				unset($formatting['LastEdited']);
			}
			unset($fields['singular_name']);
						
			// title is already in the gridfield, remove from summary fields
			if (isset($summaryFields['Title'])) {
				unset($summaryFields['Title']);
			}
			
			// add summary fields to grid
			$fields = array_merge($fields, $summaryFields);
			//debug::log("fields: ".print_r($fields, true));
			$columns->setDisplayFields($fields);
			
			$header = $gridField->getConfig()->getComponentByType('GridFieldSortableHeader');
			$sorting = $header->getFieldSorting();
			//debug::log("sorting before: ".print_r($sorting, true));
			$sorting = array_merge($sorting, array_combine(array_keys($summaryFields), array_keys($summaryFields)));
			//debug::log("sorting: ".print_r($sorting, true));
			$header = $header->setFieldSorting($sorting);
		}
		
		return $form;
	}

	public function add() {
		$parentID = $this->getCurrentParentID();
		$className = $this->getModelClass($parentID);
		
		if(is_numeric($parentID) && $parentID > 0) $parentObj = DataObject::get_by_id("SiteTree", $parentID);
		else $parentObj = null;
		
		if(!$parentObj || !$parentObj->ID) $parentID = 0;

		if($parentObj) {
			if(!$parentObj->canAddChildren()) return Security::permissionFailure($this);
			if(!singleton($className)->canCreate()) return Security::permissionFailure($this);
		} else {
			return Security::permissionFailure($this);
		}

		$record = $this->getNewItem("new-$className-$parentID", false);
		$record->ParentID = $parentID;
		
		if(class_exists('Translatable') && $record->hasExtension('Translatable') && isset($data['Locale'])) {
			$record->Locale = $data['Locale'];
		}

		try {
			$record->write();
		} catch(ValidationException $ex) {
			$form->sessionMessage($ex->getResult()->message(), 'bad');
			return $this->getResponseNegotiator()->respond($this->request);
		}

		$editController = singleton('ListedPageAdmin');
		$editController->setCurrentPageID($record->ID);

		Session::set(
			"FormInfo.Form_EditForm.formError.message", 
			_t('CMSMain.PageAdded', 'Successfully created page')
		);
		Session::set("FormInfo.Form_EditForm.formError.type", 'good');
		
		return $this->redirect(Controller::join_links($this->Link('show'), $record->ID));
	}
	
}