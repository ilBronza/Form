<?php

namespace IlBronza\Form;

use IlBronza\Form\FormFieldset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use \IlBronza\FormField\FormField;

class FormFieldset
{
	public $fields;
	public $fieldsets;
	public $parentFieldset;

	public $name;
	public $legend;
	public $form;

	public $width = 1;
	public $columns = 1;

	public $containerHtmlClasses = [];

	public $htmlClasses = [
		'uk-margin-bottom',
		'uk-fieldset'
	];

	public $collapse = true;
	public $divider = false;
	public $uniqueId;

	public $description;
	public $descriptionText;

	public $translateLegend = true;
	public ?string $translatedLegend = null;

	public $visible = true;

	public function setVisibility(bool $visible)
	{
		$this->visible = $visible;
	}

	public function getVisibility(bool $visible)
	{
		return $this->visible;
	}

	public function printDuduah()
	{
		$this->form = null;

		return json_encode( $this);
	}

	public function __construct(string $name, Form $form = null, array $parameters = [])
	{
		$this->name = $name;
		$this->legend = $name;
		$this->form = $form;

		$this->fields = collect();

		$this->manageParameters($parameters);
		$this->setUniqueId($parameters['id'] ?? null);

		$this->fieldsets = collect();
	}

	public function setUniqueId(string $uniqueId = null) : string
	{
		if($uniqueId)
			return $this->uniqueId = $uniqueId;

		return $this->uniqueId = Str::slug($this->name) . "_" . rand(0, 99999);
	}

	public function setForm(Form $form = null)
	{
		$this->form = $form;
	}

	public function getForm() : ? Form
	{
		return $this->form;
	}

	public function getUniqueId()
	{
		return $this->uniqueId;
	}

	public function hasView()
	{
		return isset($this->view);
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function setModelRecursively(Model $model)
	{
		$this->setModel($model);

		foreach($this->getFields() as $field)
			$field->setModel($model);

		foreach($this->getFieldsets() as $fieldset)
			$fieldset->setModelRecursively($model);
	}

	public function getModel() : ? Model
	{
		if($this->model ?? null)
			return $this->model;

		if($model = $this->getForm()?->getModel())
			return $model;

		if($model = $this->getParentFieldset()?->getModel())
			return $model;

		return null;
	}

	public function getViewVariables() : array
	{
		// if((! isset($this->form))&&(! isset($this->form->model)))
		// 	return [];

		// $model = $this->form->model;

		if(! ($model = $this->getModel()))
			return [];

		$result = [];

		foreach($this->view['variables'] ?? [] as $name => $method)
			$result[$name] = $model->$method();

		return $result;
	}

	public function getView()
	{
		if(! $this->hasView())
			return false;

		return $this->view['name'];
	}

	public function renderView()
	{
		$viewName = $this->getView();

		$variables = $this->getViewVariables();

		return view($viewName, $variables)->render();
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
		if($this->translatedLegend)
			return $this->translatedLegend;

		if($this->translateLegend !== null)
			return $this->translateLegend;

		if(! $this->form)
			return ;

		return $this->form->translateLegend();
	}

	public function getLegend()
	{
		if($this->translatedLegend)
			return $this->translatedLegend;

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
		$formField->setFieldset($this);

		$this->fields->push($formField);

		return $this;
	}

	public function getFields() : Collection
	{
		return $this->fields;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function getColumnsClass()
	{
		if(is_int($columns = $this->getColumns()))
			return "uk-child-width-1-{$columns}@m";

		if(! is_array($columns))
			throw new \Exception('Valore di colonne non ammesso. Usare "columns" intero o array es. ["2@m", "4@l"]');

		$pieces = [];

		foreach($columns as $column)
			$pieces[] = "uk-child-width-1-{$column}";

		return implode(" ", $pieces);
	}

	public function setFieldsColumns($columns)
	{
		$this->columns = $columns;
	}

	public function getHtmlClasses()
	{
		return array_merge([
			'fieldset' . $this->name
			],
			$this->htmlClasses
		);
	}

	public function getContainerHtmlClassesString() : string
	{
		return implode(" ", $this->getContainerHtmlClasses());
	}

	public function getContainerHtmlClasses() : array
	{
		if(is_int($width = $this->width))
			$this->containerHtmlClasses[] = 'uk-width-1-' . $width . '@m';

		else if(! is_array($width))
			throw new \Exception('Valore di width non ammesso. Usare "width" intero o array es. ["2@m", "4@l"]');

		else
		{
			$pieces = [];

			foreach($width as $_width)
				$pieces[] = "uk-width-{$_width}";

			$this->containerHtmlClasses[] = implode(" ", $pieces);	
		}

		$this->containerHtmlClasses[] = 'fieldset-container-' . $this->name;

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

	public function addFormFieldToFieldset(FormField $formField, string $fieldset)
	{
		if(! $this->fieldsets[$fieldset])
			$this->addFormFieldset($fieldset);

		$this->fieldsets[$fieldset]->addFormField($formField);

		$formField->setForm($this->form);
	}

	static function createByNameAndParameters(string $name, array $parameters)
	{
		return new static($name, null, $parameters);
	}

	public function addFieldset(FormFieldset $formFieldset)
	{
		$this->fieldsets->push($formFieldset);

		$formFieldset->setParentFieldset($this);
	}

	public function getFieldsets() : Collection
	{
		return $this->fieldsets;
	}

	public function setParentFieldset(FormFieldset $formFieldset)
	{
		$this->parentFieldset = $formFieldset;
	}

	public function getParentFieldset() : ? Formfieldset
	{
		return $this->parentFieldset;
	}

	public function addFormFieldset(string $name, array $parameters = [])
	{
		$fieldset = new static($name, $this->form, $parameters);

		$this->fieldsets[$name] = $fieldset;
		$fieldset->setDivider($parameters['divider'] ?? $this->hasDivider());

		return $fieldset;
	}

	public function renderShow()
	{
		return view("form::uikit.fieldsets.show", ['fieldset' => $this]);
	}

}
