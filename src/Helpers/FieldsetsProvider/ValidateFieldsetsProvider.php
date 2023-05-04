<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use Illuminate\Database\Eloquent\Model;

class ValidateFieldsetsProvider extends FieldsetsProvider
{
	static function getValidationParametersByArray(
		array $parameters,
		Model $model		
	) : array
	{
		$fieldsetProvider = static::setFieldsetsParametersByArray($parameters, $model);

		return $fieldsetProvider->getValidationParameters();
	}

	static function getValidationParametersByFile(
		FieldsetParametersFile $file,
		Model $model
	) : array
	{
		$fieldsetProvider = static::setFieldsetsParametersByFile($file, $model);

		return $fieldsetProvider->getValidationParameters();
	}

	public function extractFieldsetFields(array $fieldsetParameters) : array
	{
		$fieldsetParameters = $this->getAllFieldsByParameters("null", $fieldsetParameters);

		dd($fieldsetParameters);
	}

	public function getValidationParameters() : array
	{
		$fieldsetsParametersArray = $this->getParametersArray();

		$validationArray = [];

		foreach($fieldsetsParametersArray as $fieldset)
			$validationArray = $this->getValidationArrayByFieldset($fieldset, $validationArray);

		return $this->parseUniqueRules($validationArray);
	}

	public function parseUniqueRules(array $rules) : array
	{
		$model = $this->getModel();

		foreach($rules as $field => $rule)
		{
			if(is_string($rule))
				$rule = explode('|', $rule);

			foreach($rule as $index => $_rule)
				if(strpos($_rule, "unique:") !== false)
				{
					$rule[$index] = \Illuminate\Validation\Rule::unique(
						$model->getTable()
					)->ignore(
						$model->getKey(),
						$model->getKeyName()
					);

					$rules[$field] = $rule;
				}
		}

		return $rules;
	}


	public function getValidationArrayByFieldset(array $fieldset, array $validationArray) : array
	{
		if(! $this->userCanSeeFieldsetByRoles($fieldset))
			return $validationArray;

		$fields = $this->getFieldsetFields($fieldset);

		foreach($fields as $fieldName => $fieldContent)
			$validationArray = $this->addValidationArrayField($validationArray, $fieldContent, $fieldName);

		foreach($fieldset['fieldsets'] ?? [] as $fieldset)
			$validationArray = $this->getValidationArrayByFieldset($fieldset, $validationArray);

		return $validationArray;
	}

	private function addValidationArrayField(array $validationArray, array $fieldContent, string $fieldName)
	{
		// if($this->isSingleRowField($fieldContent))
		// 	return $this->addValidationArraySingleRow($validationArray, $fieldContent, $fieldName);

		return $this->addValidationArrayMultipleRow($validationArray, $fieldContent, $fieldName);
	}

	public function addJsonFieldValidationArrayField(array $validationArray, array $fieldContent, string $fieldName) :array
	{
		$validationArray[$fieldName] = $fieldContent['rules'];

		foreach($fieldContent['fields'] as $subFieldName => $subFieldContent)
		{
			$_validationKey = $fieldName . '.' . $subFieldName;

			$validationArray[$_validationKey] = 'array';
			$validationArray = $this->addValidationArrayField($validationArray, $subFieldContent, $_validationKey . '.*');
		}

		return $validationArray;
	}


	private function addValidationArrayMultipleRow(array $validationArray, array $fieldContent, string $fieldName) :array
	{
		if($fieldContent['disabled'] ?? false)
			return $validationArray;

		if($fieldContent['type'] == 'json')
			return $this->addJsonFieldValidationArrayField($validationArray, $fieldContent, $fieldName);

		$validationArray[$fieldName] = $fieldContent['rules'];

		return $validationArray;
	}

}