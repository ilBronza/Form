<?php

namespace IlBronza\Form;

use IlBronza\Form\FormFieldset;

use IlBronza\Form\Traits\ExtraViewsTrait;
use IlBronza\Form\Traits\FormButtonsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use \IlBronza\FormField\FormField;

class Form
{
	use FormButtonsTrait;

	use ExtraViewsTrait;

	public $extraViews;
	static $availableExtraViewsPositions = [
		'outherTop',
		'outherBottom',
		'innerTop',
		'innerBttom',
		'left',
		'right',
		'outherLeft',
		'outherRight'
	];

	public function getValidExtraViewsPositions() : array
	{
		return static::$availableExtraViewsPositions;
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

	public $fieldsets = [];
	public $fields;

	public $htmlClasses = [];

	public $mustShowLabel;
	public $mustShowPlaceholder = true;

	public $displayAsSwitcher = false;
	public $orientation = 'uk-form-horizontal';
	public $translateLegend = true;

	public $submitButtonText;

	public $collapse = true;
	public $divider = true;

	public $allDatabaseFields = [];


	public $closureButtons;

	public function __construct()
	{
		$this->fields = collect();

		$this->closureButtons = collect();
	}

	public function setTranslateLegend(bool $value)
	{
		$this->translateLegend = $value;
	}

	public function translateLegend()
	{
		return $this->translateLegend;
	}

	public function setTitle(string $title)
	{
		$this->title = $title;
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

	public function hasCard(bool $value = null)
	{
		if(is_null($value))
			return $this->card;

		$this->card = $value;
	}

	public function assignModel(Model $model)
	{
		$this->model = $model;
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

	public function setDivider(bool $value)
	{
		return $this->divider = $value;
	}

	public function hasDivider()
	{
		return $this->divider;
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

	public function getCancelHref()
	{
		if($this->cancelHref)
			return $this->cancelHref;

		return url()->previous();
	}

	public function getBackToListUrl()
	{
		return $this->backToListUrl ?? false;
	}

	public function setBackToListUrl(string $url)
	{
		$this->backToListUrl = $url;
	}

	public function getFormOrientationClass()
	{
		return $this->orientation;
	}

	public function setVerticalForm()
	{
		$this->orientation = 'uk-form-stacked';
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

	public function getName()
	{
		if($this->name ?? false)
			return $this->name;

		return Route::currentRouteName();
	}

	public function getId()
	{
		if($this->id ?? false)
			return $this->id;

		$this->id = Str::slug($this->getName()) . rand(0, 99999);

		return $this->id;
	}
}