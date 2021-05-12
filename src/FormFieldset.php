<?php

namespace IlBronza\Form;

use Illuminate\Database\Eloquent\Model;
use \IlBronza\FormField\FormField;

class FormFieldset
{
	public $fields;
	public $name;
	public $legend;
	public $form;

	public $width = 1;
	public $columns = 1;

	public $collapse = true;
	public $divider = false;

	public $translateLegend = null;

	public function __construct(string $name, Form $form, array $parameters = [])
	{
		$this->name = $name;
		$this->legend = $name;
		$this->form = $form;

		$this->fields = collect();

		$this->manageParameters($parameters);
	}

	public function hasCollapse()
	{
		return $this->collapse;
	}

	public function hasDivider()
	{
		return $this->divider;
	}

	public function setLegend(string $legend)
	{
		$this->legend;
	}

	public function translateLegend()
	{
		if($this->translateLegend !== null)
			return $this->translateLegend;

		if(! $this->form)
			return ;

		return $this->form->translateLegend();
	}

	public function getLegend()
	{
		if($this->translateLegend())
			return __('fieldsets.' . $this->legend);

		return $this->legend;
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

		foreach($parameters as $key => $value)
			$this->$key = $value;
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
