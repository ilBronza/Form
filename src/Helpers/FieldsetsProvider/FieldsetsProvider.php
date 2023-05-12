<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider;

use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\Form\Form;
use IlBronza\Form\FormFieldset;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Form\Helpers\FieldsetsProvider\Traits\FieldsetsProviderGettersSettersTrait;
use IlBronza\Form\Helpers\FieldsetsProvider\Traits\FieldsetsProviderParsersTrait;
use IlBronza\Form\Helpers\FieldsetsProvider\Traits\FieldsetsProviderRolesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FieldsetsProvider
{
	use FieldsetsProviderRolesTrait;
	use FieldsetsProviderGettersSettersTrait;
	use FieldsetsProviderParsersTrait;

	public $form;
	public $model;
	public $parametersFile;

	static function setFieldsetsParametersByFile(
		FieldsetParametersFile $file,
		Model $model
	) : static
	{
		$fieldsetProvider = new static();

		$fieldsetProvider->setModel($model);

		$fieldsetProvider->setParametersByFile($file);

		return $fieldsetProvider;
	}

	static function setFieldsetsParametersByArray(
		array $parameters,
		Model $model
	) : static
	{
		$fieldsetProvider = new static();

		$fieldsetProvider->setModel($model);
		$fieldsetProvider->setParameters($parameters);

		return $fieldsetProvider;
	}

	static function getFieldsetsByParametersFile(FieldsetParametersFile $file, Model $model)
	{
		$fieldsetProvider = new static(static::$type);

		$fieldsetProvider->setModel($model);

		$fieldsetProvider->setParametersByFile($file);

		return $fieldsetProvider->getFieldsetsCollection();
	}

	static function getFieldsetsCollectionByParametersFile(
		FieldsetParametersFile $file,
		Model $model
	)
	{
		$fieldsetProvider = new static();

		$fieldsetProvider->setModel($model);
		$fieldsetProvider->setParametersByFile($file);

		return $fieldsetProvider->setFieldsetsCollectionToModel();
	}

	static function addFieldsetsToFormByParametersFile(
		Form $form,
		FieldsetParametersFile $file,
		Model $model
	)
	{
		$fieldsetProvider = new static();

		$fieldsetProvider->setForm($form);
		$fieldsetProvider->setModel($model);
		$fieldsetProvider->setParametersByFile($file);

		return $fieldsetProvider->setFieldsetsCollectionToForm();
	}

	protected function _getFieldsetFields(array $fieldsetParameters) : array
	{
		if(isset($fieldsetParameters['fields']))
			return $fieldsetParameters['fields'];

		return $fieldsetParameters;
	}

	protected function getFieldsetFields(array $fieldsetParameters) : array
	{
		$fieldsParameters = $this->_getFieldsetFields($fieldsetParameters);

		return $this->filterByRolesAndPermissions($fieldsParameters);
	}

	protected function getSubFieldsets(array $fieldsetParameters) : array
	{
		if(isset($fieldsetParameters['fieldsets']))
			return $fieldsetParameters['fieldsets'];

		return [];
	}

	protected function getFieldsetOptions(array $fieldsetParameters)
	{
		if(! isset($fieldsetParameters['fields']))
			return [];
		
		unset($fieldsetParameters['fields']);

		return $fieldsetParameters;
	}

	public function createFieldset(string $name, array $fieldsetParameters) : FormFieldset
	{
		$fieldsetOptions = $this->getFieldsetOptions($fieldsetParameters);

		$fieldset = FormFieldset::createByNameAndParameters($name, $fieldsetOptions);

		$fieldset->setVisibility(
			$this->userCanSeeFieldsetByRoles($fieldsetParameters)
		);

		$fields = $this->getFieldsetFields($fieldsetParameters);

		foreach($fields as $fieldName => $field)
			$fieldset->addFormField(
				FormFieldsProvider::createByNameParameters($fieldName, $field)
			);

		$subFieldsets = $this->getSubFieldsets($fieldsetParameters);

		foreach($subFieldsets as $name => $subFieldsetParameters)
			$fieldset->addFieldset(
				$this->createFieldset(
					$name, $subFieldsetParameters
				)
			);

		return $fieldset;
	}

	public function _getFieldsetsCollection(array $parameters) : Collection
	{
		$result = collect();

		foreach($parameters as $name => $fieldsetParameters)
			$result->push(
				$this->createFieldset(
					$name, $fieldsetParameters
				)
			);

		return $result;
	}

	public function getFieldsetsCollection() : Collection
	{
		$fieldsetsArray = $this->getParametersArray();

		return $this->_getFieldsetsCollection($fieldsetsArray);
	}

	public function setFieldsetsCollectionToModel()
	{
		$fieldsetsCollection = $this->getFieldsetsCollection();

		foreach($fieldsetsCollection as $fieldset)
			$fieldset->setModel(
				$this->getModel()
			);

		return $fieldsetsCollection;
	}

	public function setFieldsetsCollectionToForm()
	{
		$fieldsetsCollection = $this->getFieldsetsCollection();

		$this->form->addFieldsets($fieldsetsCollection);
	}


	/**
	 * check if field is required in given rules array
	 *
	 * @param array $rules
	 * @return boolean
	 */
	// private function checkIfFieldIsRequired(array $rules)
	// {
	// 	return in_array('required', $rules);
	// }

	/**
	 * build an array with a key per each rule
	 *
	 * @param array $parameters, array $rules
	 * @return array
	 */
	// private function buildParametersRules($parameters, $rules)
	// {
	// 	if(! isset($parameters['required']))
	// 		$parameters['required'] = $this->checkIfFieldIsRequired($rules);

	// 	$parameters['rules'] = [];

	// 	foreach($rules as $key => $rule)
	// 		if(strpos($rule, ":"))
	// 		{
	// 			$_rule = explode(":", $rule);

	// 			$parameters['rules'][$_rule[0]] = $_rule[1];

	// 			if($_rule[0] == 'max')
	// 				$parameters['max'] = $_rule[1];
	// 		}
	// 		else
	// 			$parameters['rules'][$rule] = true;

	// 	return $parameters;		
	// }

	// private function buildNameAndLabelArray(string $name, array $parameters)
	// {
	// 	return [
	// 			'name' => $name,
	// 			'label' => $parameters['label'] ?? __('fields.' . $name),
	// 		];
	// }

	// protected function getFieldParametersSingleRow(string $name, array $parametersString)
	// {
	// 	$type = array_key_first($parametersString);

	// 	$parameters = $this->buildNameAndLabelArray($name, $parametersString);
	// 	$parameters['type'] = $type;

	// 	$rules = explode("|", $parametersString[$type]);

	// 	return $this->buildParametersRules($parameters, $rules);
	// }

	// public function getFieldParameters(string $fieldName, array $parametersString)
	// {
	// 	if(! isset($parametersString['type']))
	// 		throw new \Exception('Missing "type" array element in field ' . $name);

	// 	if(! isset($parametersString['rules']))
	// 		throw new \Exception('Missing "rules" array element in field ' . $name);

	// 	// if($parametersString['type'] == 'select')
	// 	// 	if(! isset($parametersString['list']))
	// 	// 		if(! isset($parametersString['relation']))
	// 	// 			throw new \Exception('Missing "relation" or "list" array element in field ' . $name . ', necessary to retrieve select elements');

	// 	// $parameters = array_merge(
	// 	// 	$parametersString, 
	// 	// 	$this->buildNameAndLabelArray($name, $parametersString)
	// 	// );

	// 	dd($parametersString);
	// 	$rules = $parametersString['rules'];

	// 	if(is_string($rules))
	// 		$rules = explode("|", $parametersString['rules']);

	// 	return $this->buildParametersRules($parameters, $rules);


	// 	// if($this->isSingleRowField($parametersString))
	// 	// 	return $this->getFieldParametersSingleRow($fieldName, $parametersString);

	// 	// return $this->getFieldParametersKeyValueRow($fieldName, $parametersString);
	// }

	// protected function isSingleRowField(array $fieldContent) : bool
	// {
	// 	return count($fieldContent) == 1;
	// }

	public function parametersContainRelationship(array $fieldParameters) : bool
	{
		return isset($fieldParameters['relation']);
	}

	public function parametersContainBelongsToRelationship(array $fieldParameters) : bool
	{
		$relationMethod = $fieldParameters['relation'];

		$relationType = class_basename(
			get_class(
				$this->getModel()->{$relationMethod}()
			)
		);

		return $relationType == 'BelongsTo';
	}

	public function getBindableAttributeFieldsNames() : array
	{
		$result = [];

		foreach($this->getBindableAttributeFields() as $index => $fieldParameters)
			$result[$index] = $fieldParameters['name'];

		return $result;
	}

	public function getBindableAttributeFields() : array
	{
		$fields = $this->getAllFieldsArray();

		foreach($fields as $index => $fieldParameters)
		{
			if(! $this->parametersContainRelationship($fieldParameters))
				continue;

			if($this->parametersContainBelongsToRelationship($fieldParameters))
			{
				$relationMethod = $fieldParameters['relation'];
				$fields[$index]['name'] = $this->getModel()->{$relationMethod}()->getForeignKeyName();

				continue;
			}

			unset($fields[$index]);
		}

		return $fields;
	}

	public function getExtraTableRelationshipsFields() : array
	{
		$fields = $this->getAllFieldsArray();

		foreach($fields as $index => $fieldParameters)
		{
			if(! $this->parametersContainRelationship($fieldParameters))
				unset($fields[$index]);

			else if($this->parametersContainBelongsToRelationship($fieldParameters))
				unset($fields[$index]);
		}

		return $fields;
	}

}