<?php

namespace IlBronza\Form;

use IlBronza\CRUD\Traits\IlBronzaPackages\CRUDExtraButtonsTrait;
use IlBronza\Form\FormFieldset;
use IlBronza\Form\Traits\ExtraViewsTrait;
use IlBronza\Form\Traits\FormButtonsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;
use \IlBronza\FormField\FormField;

class Form
{
	use FormButtonsTrait;

	use ExtraViewsTrait;
	use CRUDExtraButtonsTrait;

	public $extraViews;
	public Collection $fetchers;

	public string $displayMode = 'form';

	static $availableExtraViewsPositions = [
		'outherTop',
		'outherBottom',
		'innerTop',
		'innerBottom',
		'left',
		'right',
		'outherLeft',
		'outherRight'
	];

	public function getValidExtraViewsPositions() : array
	{
		return static::$availableExtraViewsPositions;
	}

	public $gridSizeHtmlClass = 'uk-grid';

	public bool $updateEditor;

	public function hasUpdateEditor() : bool
	{
		if(! $this->getModel()?->exists)
			return false;

		if(isset($this->updateEditor))
			return $this->updateEditor;

		return config('form.updateEditor', false);
	}

	public function setUpdateEditor(bool $updateEditor) : void
	{
		$this->updateEditor = $updateEditor;
	}

	public $method = 'POST';
	public $action;
	public $model;

	public $title;
	public $intro;

	public $cancelButton = true;
	public $cancelHref;

	public $card = false;
	public $cardClasses = [];

	public $fieldsets;
	public $fields;

	public $htmlClasses = [];

	public $mustShowLabel;
	public $mustShowPlaceholder = true;

	public $displayAsSwitcher = false;
	public $orientation = 'uk-form-horizontal';
	public $translateLegend = true;

	public $closureAlignment = 'left';

	public $submitButtonText;

	public $collapse = true;
	public $divider = false;

	public $allDatabaseFields = [];

	public $showElementUrl;


	public $closureButtons;

	public function __construct()
	{
		$this->fields = collect();
		$this->fieldsets = collect();

		$this->closureButtons = collect();

		$this->fetchers = collect();
	}

	public function getClosureAlignmentString() : string
	{
		return $this->closureAlignment;
	}

	public function setClosureAlignmentString(string $position)
	{
		$this->closureAlignment = $position;
	}

	public function setTranslateLegend(bool $value)
	{
		$this->translateLegend = $value;
	}

	public function translateLegend()
	{
		return $this->translateLegend;
	}

	public function setTitle(string $title) : static
	{
		$this->title = $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setIntro(string $intro)
	{
		$this->intro = $intro;
	}

	public function getIntro()
	{
		return $this->intro;
	}

	public function addHtmlClass(string $htmlClass)
	{
		$this->htmlClasses[] = $htmlClass;
	}

	public function getHtmlClasses() : array
	{
		return $this->htmlClasses;
	}

	public function getFormHtmlClassesString() : string
	{
		return implode(" ", $this->getHtmlClasses());
	}

	public function addCardClasses(array $classes = [])
	{
		$this->cardClasses = array_merge(
			$this->cardClasses,
			$classes
		);
	}

	public function getCardClasses()
	{
		return $this->cardClasses;
	}

	public function setCard() : static
	{
		$this->hasCard(true);

		return $this;
	}

	public function hasCard(bool $value = null)
	{
		if(is_null($value))
			return $this->card;

		$this->card = $value;
	}


	public function assignModel(Model $model)
	{
		$this->setModel($model);
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function getModel() : ? Model
	{
		return $this->model;
	}

	public function addFormField(FormField $formField)
	{
		$this->fields->push($formField);

		$formField->setForm($this);

		return $this;
	}

	public function addFormFieldToFieldset(FormField $formField, string $fieldset)
	{
		if(! $this->fieldsets[$fieldset])
			$this->addFormFieldset($fieldset);

		$this->fieldsets[$fieldset]->addFormField($formField);

		$formField->setForm($this);
	}

	public function getFields() : Collection
	{
		return $this->fields;
	}

	public function setDivider(bool $value)
	{
		return $this->divider = $value;
	}

	public function hasDivider()
	{
		return $this->divider;
	}

	public function addFieldsets(Collection $fieldsetsCollection)
	{
		foreach($fieldsetsCollection as $fieldset)
			$this->addFieldset($fieldset);
	}

	public function addFieldset(FormFieldset $fieldset)
	{
		$this->fieldsets->push($fieldset);

		$fieldset->setForm($this);
	}

	public function addFormFieldset(string $name, array $parameters = [])
	{
		$fieldset = new FormFieldset($name, $this, $parameters);

		$this->fieldsets[$name] = $fieldset;
		$fieldset->setDivider($this->hasDivider());

		return $fieldset;
	}

	public function hasFieldsets()
	{
		return !! count($this->fieldsets);
	}

	public function flattenFieldsets()
	{
		foreach($this->fieldsets as $fieldset)
			foreach($fieldset->fields as $field)
				$this->fields->push($field);

		$this->fieldsets = [];
	}

	public function getMethodAttribute()
	{
		if(! in_array($method = $this->getMethod(), ['GET', 'POST']))
			return 'POST';

		return $method;
	}

	public function setMethod(string $method)
	{
		$this->method = $method;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setAction(string $action)
	{
		$this->action = $action;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getCancelUrl()
	{
		if($href = $this->getCancelHref())
			return $href;

		if(url()->previous() != url()->current())
			return url()->previous();

		try
		{
			if(($model = $this->getModel())&&($model->exists))
				return $model->getShowUrl();

			if(method_exists($model, 'getCancelUrl'))
				return $model->getCancelUrl();

			return $model->getIndexUrl();			
		}
		catch(\Throwable $e)
		{
			return url()->previous();
		}
	}

	public function setCancelHref(string $href) : static
	{
		$this->cancelHref = $href;

		return $this;
	}

	public function getCancelHref()
	{
		return $this->cancelHref;
	}

	public function getBackToListUrl()
	{
		return $this->backToListUrl ?? false;
	}

	public function setEditElementUrl(string $url) : static
	{
		$this->editElementUrl = $url;

		return $this;
	}

	public function getEditElementUrl() : ? string
	{
		return $this->editElementUrl ?? null;
	}

	public function setBackToListUrl(string $url)
	{
		$this->backToListUrl = $url;
	}

	public function getShowElementUrl()
	{
		return $this->showElementUrl;
	}

	public function setShowElementUrl(string $url)
	{
		$this->showElementUrl = $url;
	}

	public function getFormOrientationClass()
	{
		return $this->orientation;
	}

	public function setHorizontalForm() : static
	{
		$this->orientation = 'uk-form-horizontal';

		return $this;
	}

	public function setVerticalForm() : static
	{
		$this->orientation = 'uk-form-stacked';

		return $this;
	}

	public function setStackedForm()
	{
		$this->orientation = 'uk-form-stacked';
	}

	public function mustShowLabel(bool $mustShowLabel = null)
	{
		if(is_null($mustShowLabel))
			return $this->mustShowLabel;

		$this->mustShowLabel = $mustShowLabel;
	}

	public function mustShowPlaceholder(bool $mustShowPlaceholder = null)
	{
		if(is_null($mustShowPlaceholder))
			return $this->mustShowPlaceholder;

		$this->mustShowPlaceholder = $mustShowPlaceholder;
	}

	static function createFromArray(array $parameters)
	{
		$field = new static();

		foreach($parameters as $name => $value)
			$field->$name = $value;

		return $field;
	}

	public function getFieldByName(string $name)
	{
		foreach($this->fields as $field)
			if($field->name == $name)
				return $field;

		foreach($this->fieldsets as $fieldset)
			foreach($fieldset->fields as $field)
				if($field->name == $name)
					return $field;

		return false;
	}

	public function displayAsSwitcher(bool $status = true)
	{
		$this->displayAsSwitcher = $status;
	}

	public function mustDisplayAsSwitcher()
	{
		return $this->displayAsSwitcher;
	}

	public function render()
	{
		return view("form::uikit.form", ['form' => $this]);
	}

	public function _render()
	{
		return view("form::uikit._form", ['form' => $this]);
	}

	public function _renderAjax()
	{
		return view("form::uikit._ajax", ['form' => $this]);
	}

	public function getDisplayMode() : string
	{
		return $this->displayMode;
	}

	public function isInFormDisplayMode() : bool
	{
		return $this->getDisplayMode() == 'form';
	}

	public function isInPdfDisplayMode() : bool
	{
		return $this->getDisplayMode() == 'pdf';
	}

	public function isInShowDisplayMode()
	{
		return $this->getDisplayMode() == 'show';
	}

	public function setDisplayMode(string $mode) : static
	{
		$this->displayMode = $mode;

		return $this;
	}

	public function _renderPdf() : View
	{
		return $this->setDisplayMode('pdf')
				->_render();
	}

	public function _renderShow() : View
	{
		return $this->setDisplayMode('show')
				->_render();
	}

	public function renderContent()
	{
		return view("form::uikit._content", ['form' => $this])->render();
	}

	public function hasCollapse()
	{
		return $this->collapse;
	}

	public function setAllDatabaseFields(array $allDatabaseFields)
	{
		$this->allDatabaseFields = $allDatabaseFields;
	}

	public function getDatabaseField(string $name)
	{
		return $this->allDatabaseFields[$name] ?? null;
	}

	public function getGridSizeHtmlClass() : string
	{
		return "{$this->gridSizeHtmlClass} " . config('form.grid-size', 'uk-grid-small');
	}

	public function getName() : ? string
	{
		if($this->name ?? false)
			return $this->name;

		if($model = $this->getModel())
			return $this->name = $model->getName();

		return Route::currentRouteName();
	}

	public function getId() : ? string
	{
		if($this->id ?? false)
			return $this->id;

		$this->id = Str::slug($this->getName()) . rand(0, 99999);

		return $this->id;
	}
}