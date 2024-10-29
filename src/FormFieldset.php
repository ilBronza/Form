<?php

namespace IlBronza\Form;

use Exception;
use IlBronza\Buttons\Button;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function collect;
use function config;
use function implode;

class FormFieldset
{
	public ? Model $model;
	public Collection $fetchers;
	public Collection $buttons;
	public $showLegend = true;
	public $fields;
	public $fieldsets;
	public $parentFieldset;

	public $name;
	public $legend;
	public $form;

	public $width = 1;
	public $columns = 1;

	public $containerHtmlAttributes = [];
	public $containerHtmlClasses = [];

	public $htmlAttributes = [];
	public $htmlClasses = [
		'uk-margin-bottom',
		'uk-fieldset'
	];

	public function getMarginSize() : string
	{
		if($form = $this->getForm())
			return $form->getMarginSize();

		return 'medium';
	}

	public $extraHtmlClasses = [];

	public $legendHtmlClasses = [];
	public $bodyHtmlClasses = [];

	public $gridSizeHtmlClass = 'uk-grid-small';

	public ? bool $collapse;
	public ? bool $collapseRow;
	public ? bool $collapseColumn;

	public $divider = false;
	public $uniqueId;

	public $description;
	public $descriptionText;

	public $translateLegend = true;
	public ?string $translatedLegend = null;
	public ?string $translationPrefix = null;

	public $visible = true;

	public function __construct(string $name, Form $form = null, array $parameters = [])
	{
		$this->name = $name;
		$this->legend = $name;
		$this->form = $form;

		$this->fields = collect();

		$this->buttons = collect();

		$this->manageParameters($parameters);
		$this->setUniqueId($parameters['id'] ?? null);

		$this->fieldsets = collect();
	}

	private function manageParameters(array $parameters)
	{
		if ($width = ($parameters['width'] ?? false))
			$this->setWidth($width);

		if ($classes = ($parameters['classes'] ?? false))
			$this->addHtmlClasses($classes);

		if ($containerClasses = ($parameters['containerClasses'] ?? false))
			$this->addContainerHtmlClasses($containerClasses);

		if ($columns = ($parameters['columns'] ?? false))
			$this->setFieldsColumns($columns);

		if ($buttons = ($parameters['buttons'] ?? []))
		{
			$this->addButtons($buttons);
			unset($parameters['buttons']);
		}

		unset($parameters['buttons']);

		foreach ($parameters as $key => $value)
			$this->$key = $value;
	}

	public function setWidth($width)
	{
		$this->width = $width;
	}

	public function addHtmlClasses(array $classes)
	{
		foreach ($classes as $class)
			$this->addClass($class);
	}

	public function addClass(string $class)
	{
		$this->htmlClasses[] = $class;
	}

	public function addContainerHtmlClasses(array $classes)
	{
		foreach ($classes as $class)
			$this->addContainerClass($class);
	}

	public function addContainerClass(string $class)
	{
		$this->containerHtmlClasses[] = $class;
	}

	public function setFieldsColumns($columns)
	{
		$this->columns = $columns;
	}

	static function createByNameAndParameters(string $name, array $parameters)
	{
		return new static($name, null, $parameters);
	}

	public function setVisibility(bool $visible)
	{
		$this->visible = $visible;
	}

	public function getVisibility() : bool
	{
		return $this->isVisible();
	}

	public function isVisible() : bool
	{
		return $this->visible;
	}

	public function printDuduah()
	{
		$this->form = null;

		return json_encode($this);
	}

	public function getUniqueId()
	{
		return $this->uniqueId;
	}

	public function setUniqueId(string $uniqueId = null) : string
	{
		if ($uniqueId)
			return $this->uniqueId = $uniqueId;

		return $this->uniqueId = Str::slug($this->name) . "_" . rand(0, 99999);
	}

	public function setModelRecursively(Model $model)
	{
		$this->setModel($model);

		foreach ($this->getFields() as $field)
			$field->setModel($model);

		foreach ($this->getFieldsets() as $fieldset)
			$fieldset->setModelRecursively($model);
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function getFields() : Collection
	{
		return $this->fields;
	}

	public function getFieldsets() : Collection
	{
		return $this->fieldsets;
	}

	public function getFetchers() : Collection
	{
		if (isset($this->fetchers))
			return $this->fetchers;

		return collect();
	}

	public function addButtons(Collection|array $buttons)
	{
		foreach($buttons as $button)
			$this->addButton($button);
	}

	public function addButton(Button $button)
	{
		$this->buttons->push($button);
	}

	public function getButtons() : Collection
	{
		if (isset($this->buttons))
			return $this->buttons;

		return collect();
	}

	public function showLegend() : bool
	{
		return $this->showLegend;
	}

	public function renderView()
	{
		$viewName = $this->getView();

		$variables = $this->getViewVariables();
		$parameters = $this->getViewParameters();

		$resultingParameters = $variables + $parameters;

		return view($viewName, $resultingParameters)->render();
	}

	public function getView()
	{
		if (! $this->hasView())
			return false;

		return $this->view['name'];
	}

	public function hasView()
	{
		return isset($this->view);
	}

	public function getViewVariables() : array
	{
		// if((! isset($this->form))&&(! isset($this->form->model)))
		// 	return [];

		// $model = $this->form->model;

		if (! ($model = $this->getModel()))
			return [];

		$result = [];

		foreach ($this->view['variables'] ?? [] as $name => $method)
			$result[$name] = $model->$method();

		return $result;
	}

	public function getModel() : ?Model
	{
		if ($this->model ?? null)
			return $this->model;

		if ($model = $this->getForm()?->getModel())
			return $model;

		if ($model = $this->getParentFieldset()?->getModel())
			return $model;

		return null;
	}

	public function getForm() : ?Form
	{
		return $this->form;
	}

	public function setForm(Form $form = null)
	{
		$this->form = $form;
	}

	public function getParentFieldset() : ?Formfieldset
	{
		return $this->parentFieldset;
	}

	public function setParentFieldset(FormFieldset $formFieldset)
	{
		$this->parentFieldset = $formFieldset;
	}

	public function getViewParameters() : array
	{
		return $this->view['parameters'] ?? [];
	}

	public function getCollapseDividerString() : string
	{
		$pieces = [];

		if(($this->hasCollapse())||($this->hasCollapseColumn() && $this->hasCollapseRow()))
			$pieces = ['uk-grid-collapse'];
		else if($this->hasCollapseColumn())
			$pieces = ['uk-grid-column-collapse'];
		else if($this->hasCollapseRow())
			$pieces = ['uk-grid-row-collapse'];

		if($this->hasDivider())
			$pieces = ['uk-grid-divider'];

		return implode(" ", $pieces);
	}

	public function hasCollapse()
	{
		if(isset($this->collapse))
			return $this->collapse;

		return config('form.collapse', true);
	}

	public function hasCollapseRow()
	{
		if(isset($this->collapseRow))
			return $this->collapseRow;

		return config('form.collapseRow', true);
	}

	public function hasCollapseColumn()
	{
		if(isset($this->collapseColumn))
			return $this->collapseColumn;

		return config('form.collapseColumn', true);
	}

	public function getLegend()
	{
		if ($this->translatedLegend)
			return $this->translatedLegend;

		if ($this->translateLegend())
			return __($this->getTranslationPrefix() . '.' . $this->legend);

		return $this->legend;
	}

	public function setLegend(string $legend)
	{
		$this->legend;
	}

	public function translateLegend()
	{
		if ($this->translatedLegend)
			return $this->translatedLegend;

		if ($this->translateLegend !== null)
			return $this->translateLegend;

		if (! $this->form)
			return;

		return $this->form->translateLegend();
	}

	public function getTranslationPrefix() : string
	{
		return $this->translationPrefix ?? 'fieldsets';
	}

	public function getHtmlClassesString()
	{
		return implode(" ", $this->getHtmlClasses());
	}

	public function getHtmlClasses()
	{
		return array_merge([
			'fieldset' . $this->name
		], $this->htmlClasses);
	}

	public function getLegendHtmlClassesString()
	{
		return implode(" ", $this->getLegendHtmlClasses());
	}

	public function getLegendHtmlClasses() : array
	{
		return $this->legendHtmlClasses;
	}

	public function getBodyHtmlClassesString()
	{
		return implode(" ", $this->getBodyHtmlClasses());
	}

	public function getBodyHtmlClasses() : array
	{
		return $this->bodyHtmlClasses;
	}

	public function getFieldsDefaultParameters() : array
	{
		$result = [];

		if ($this->translationPrefix)
			$result['translationPrefix'] = $this->translationPrefix;

		return $result;
	}

	public function getColumnsClass()
	{
		if (is_int($columns = $this->getColumns()))
			return "uk-child-width-1-{$columns}@m";

		if (! is_array($columns))
			throw new Exception('Valore di colonne non ammesso. Usare "columns" intero o array es. ["2@m", "4@l"]');

		$pieces = [];

		foreach ($columns as $column)
			$pieces[] = "uk-child-width-1-{$column}";

		return implode(" ", $pieces);
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function getContainerHtmlAttributesString() : string
	{
		return implode(" ", $this->getContainerHtmlAttributes());
	}

	public function getContainerHtmlAttributes() : array
	{
		return $this->containerHtmlAttributes;
	}

	public function getHtmlAttributesString() : string
	{
		return implode(" ", $this->getHtmlAttributes());
	}

	public function getHtmlAttributes() : array
	{
		return $this->htmlAttributes;
	}

	public function getGridSizeHtmlClass() : string
	{
		return $this->gridSizeHtmlClass;
	}

	public function getContainerHtmlClassesString() : string
	{
		return implode(" ", $this->getContainerHtmlClasses());
	}

	public function getContainerHtmlClasses() : array
	{
		if (is_int($width = $this->width))
			$this->containerHtmlClasses[] = 'uk-width-1-' . $width . '@m';

		else if (! is_array($width))
			throw new Exception('Valore di width non ammesso. Usare "width" intero o array es. ["2@m", "4@l"]');

		else
		{
			$pieces = [];

			foreach ($width as $_width)
				$pieces[] = "uk-width-{$_width}";

			$this->containerHtmlClasses[] = implode(" ", $pieces);
		}

		$this->containerHtmlClasses[] = 'fieldset-container-' . $this->name;

		return $this->containerHtmlClasses;
	}

	public function getDescription() : ? string
	{
		if (! $this->description)
			return null;

		if ($this->descriptionText)
			return $this->descriptionText;

		return __('fieldsets.' . $this->legend . 'Description');
	}

	public function addFormFieldToFieldset(FormField $formField, string $fieldset)
	{
		if (! $this->fieldsets[$fieldset])
			$this->addFormFieldset($fieldset);

		$this->fieldsets[$fieldset]->addFormField($formField);

		$formField->setForm($this->form);
	}

	public function addFormFieldset(string $name, array $parameters = [])
	{
		$fieldset = new static($name, $this->form, $parameters);

		$this->fieldsets[$name] = $fieldset;
		$fieldset->setDivider($parameters['divider'] ?? $this->hasDivider());

		return $fieldset;
	}

	public function setDivider(bool $value = true)
	{
		$this->divider = $value;
	}

	public function hasDivider()
	{
		return $this->divider;
	}

	public function addFormField(FormField $formField)
	{
		$formField->form = $this->form;
		$formField->setFieldset($this);

		$this->fields->push($formField);

		return $this;
	}

	public function addFieldset(FormFieldset $formFieldset)
	{
		$this->fieldsets->push($formFieldset);

		$formFieldset->setParentFieldset($this);
	}

	public function renderShow() : View
	{
		return view("form::uikit.fieldsets.show", ['fieldset' => $this]);
	}

}
