<?php

namespace ilBronza\Form;

use ilBronza\Form\Traits\FormButtonsTrait;
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
	use FormButtonsTrait;

	public $method = 'POST';
	public $action;
	public $model;

	public $title;

	public $card = false;
	public $cardClasses = [];

	public $submit = true;
	public $extraSubmitButtons;

	public $fieldsets = [];
	public $fields;

	public $mustShowLabel;
	public $mustShowPlaceholder = true;

	public $displayAsSwitcher = false;
	public $orientation = 'uk-form-horizontal';

	public function __construct()
	{
		$this->fields = collect();
		$this->extraSubmitButtons = collect();
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

	/**
	 * DEPRECATED for setModel
	 * assign model to form to get field values
	 *
	 * @param Model $model
	 **/
	public function assignModel(Model $model)
	{
		$this->setModel($model);
	}

	/**
	 * assign model to form to get field values
	 *
	 * @param Model $model
	 **/
	public function setModel(Model $model)
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

	public function hasCancel()
	{
		return $this->cancel ?? false;
	}

	public function useCancel()
	{
		$this->cancel = true;
	}

	public function setCancelUrl(string $cancelUrl)
	{
		$this->useCancel();

		$this->cancelUrl = $cancelUrl;
	}

	public function getCancelUrl()
	{
		return $this->cancelUrl;
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
}