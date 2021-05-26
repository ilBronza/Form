<?php

namespace IlBronza\Form;

use Illuminate\Database\Eloquent\Model;
use \IlBronza\FormField\FormField;
use IlBronza\Form\FormFieldset;

class Form
{
	public $method = 'POST';
	public $action;
	public $model;

	public $title;

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

	public function __construct()
	{
		$this->fields = collect();
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

	public function hasCollapse()
	{
		return $this->collapse;
	}

	public function setSubmitButtonText(string $submitButtonText)
	{
		$this->submitButtonText = $submitButtonText;
	}

	public function getSubmitButtonText()
	{
		return $this->submitButtonText ?? __('forms.save');
	}
}