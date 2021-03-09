<?php

namespace ilBronza\Form;

use Illuminate\Database\Eloquent\Model;
use \ilBronza\FormField\FormField;

class FormFieldset
{
	public $fields;
	public $name;
	public $form;

	public $width = 1;
	public $columns = 1;

	public function __construct(string $name, Form $form, array $parameters = [])
	{
		$this->name = $name;
		$this->form = $form;

		$this->fields = collect();

		$this->manageParameters($parameters);
	}

	public function setWidth($width)
	{
		$this->width = $width;
	}

	private function manageParameters(array $parameters)
	{
		if($width = ($parameters['width'] ?? false))
			$this->setWidth($width);

		if($columns = ($parameters['columns'] ?? false))
			$this->setFieldsColumns($columns);
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

	public function getHtmlClasses()
	{
		$result = [];

		if(is_int($this->width))
			$result[] = 'uk-width-1-' . $this->width;
		else
			$result[] = 'uk-width-' . $this->width;

		return implode(" ", $result);
	}
}
