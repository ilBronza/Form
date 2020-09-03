<?php

namespace ilBronza\Form;

use Illuminate\Database\Eloquent\Model;
use \ilBronza\FormField\FormField;

class FormFieldset
{
	public $fields;
	public $name;
	public $form;
	public $columns = 1;

	public function __construct(string $name, Form $form)
	{
		$this->name = $name;
		$this->form = $form;

		$this->fields = collect();
	}

	public function addFormField(FormField $formField)
	{
		$formField->form = $this->form;
		$this->fields->push($formField);

		return $this;
	}

	public function setFieldsColumns(int $columns)
	{
		$this->columns = $columns;
	}
}

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

	public $mustShowLabel;
	public $mustShowPlaceholder;

	public function __construct()
	{
		$this->fields = collect();
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
	}

	public function addFormFieldToFieldset(FormField $formField, string $fieldset)
	{
		if(! $this->fieldsets[$fieldset])
			$this->addFormFieldset($fieldset);

		$this->fieldsets[$fieldset]->addFormField($formField);

		$formField->setForm($this);
	}

	public function addFormFieldset(string $name)
	{
		$fieldset = new FormFieldset($name, $this);

		$this->fieldsets[$name] = $fieldset;

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
		return 'uk-form-horizontal';
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

	public function render()
	{
		return view("form::uikit.form", ['form' => $this]);
	}
}