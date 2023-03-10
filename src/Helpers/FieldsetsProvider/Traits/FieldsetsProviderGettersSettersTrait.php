<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider\Traits;

use IlBronza\FormField\FormField;
use IlBronza\Form\Form;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use Illuminate\Database\Eloquent\Model;


trait FieldsetsProviderGettersSettersTrait
{
	public function getType()
	{
		return static::$type;
	}

	public function setForm(Form $form = null)
	{
		$this->form = $form;
	}

	public function getForm() : ? Form
	{
		return $this->form;
	}

	public function setModel(Model $model)
	{
		$this->model = $model;

		if($form = $this->getForm())
			$form->setModel($model);
	}

	public function getModel() : Model
	{
		return $this->model;
	}

	public function getParametersArray() : array
	{
		return $this->parameters;
	}

	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}

	public function setParametersByFile(FieldsetParametersFile $file)
	{
		$parameters = $this->getParametersByFile($file);

		$this->setParameters($parameters);
	}

	public function getParametersByFile(FieldsetParametersFile $file) : array
	{
		return $file->getFieldsetsParameters();
	}

	public function getAllFieldsByParameters(string $name, array $givenParameters) : array
	{
		$result = $this->getFieldsetFields($givenParameters);
		
		$subFieldsets = $this->getSubFieldsets($givenParameters);

		foreach($subFieldsets as $_name => $subFieldsetParameters)
		{
			$result = array_merge(
				$result,
				$this->getAllFieldsByParameters($_name, $subFieldsetParameters)
			);
		}

		return $result;
	}

	public function getAllFieldsArray() : array
	{
		$fieldsetsParameters = $this->getParametersArray();

		$result = [];

		foreach($fieldsetsParameters as $name => $fieldsetParameters)
			$result = array_merge(
				$result,
				$this->getAllFieldsByParameters('base', $fieldsetParameters)
			);

		return $result;
	}

	public function getFormFieldByName(string $fieldName) : FormField
	{
		return FormField::createFromArray(
			$this->getFieldsParametersByName($fieldName)
		);
	}

	public function getFieldsParametersByName(string $name) : array
	{
		$fields = $this->getAllFieldsArray();

		foreach($fields as $fieldName => $parameters)
			if($name == $fieldName)
				return $parameters;

		throw new \Exception($name . ' not found on fieldslist');
	}
}




























