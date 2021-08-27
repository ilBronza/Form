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

	public $containerHtmlClasses = [];
	public $htmlClasses = [
		'uk-margin-small-bottom',
		'uk-fieldset'
	];

	public $collapse = true;
	public $divider = false;

	public $description;
	public $descriptionText;

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

	public function setDivider(bool $value = true)
	{
		$this->divider = $value;
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

	public function addHtmlClasses(array $classes)
	{
		foreach($classes as $class)
			$this->addClass($class);
	}

	public function addClass(string $class)
	{
		$this->htmlClasses[] = $class;		
	}

	public function addContainerHtmlClasses(array $classes)
	{
		foreach($classes as $class)
			$this->addContainerClass($class);
	}

	public function addContainerClass(string $class)
	{
		$this->containerHtmlClasses[] = $class;
	}

	public function getHtmlClassesString()
	{
		return implode(" ", $this->getHtmlClasses());
	}

	private function manageParameters(array $parameters)
	{
		if($width = ($parameters['width'] ?? false))
			$this->setWidth($width);

		if($classes = ($parameters['classes'] ?? false))
			$this->addHtmlClasses($classes);

		if($containerClasses = ($parameters['containerClasses'] ?? false))
			$this->addContainerHtmlClasses($containerClasses);

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
		return $this->htmlClasses;
	}

	public function getContainerHtmlClassesString() : string
	{
		return implode(" ", $this->getContainerHtmlClasses());
	}

	public function getContainerHtmlClasses() : array
	{
		if(is_int($this->width))
			$this->containerHtmlClasses[] = 'uk-width-1-' . $this->width . '@m';
		else
			$this->containerHtmlClasses[] = 'uk-width-' . $this->width . '@m';

		return $this->containerHtmlClasses;
	}

	public function getDescription() : ? string
	{
		if(! $this->description)
			return null;

		if($this->descriptionText)
			return $this->descriptionText;

		return __('fieldsets.' . $this->legend . 'Description');
	}
}
