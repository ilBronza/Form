<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider;

class FieldsetParametersFile
{
	// public function getShowFieldsetsArray() : array
	// {
	// 	return $this->parameters;
	// }

	// public function getEditFieldsetsArray() : array
	// {
	// 	return $this->parameters;
	// }

	// public function getCreateFieldsetsArray() : array
	// {
	// 	return $this->parameters;
	// }

	// public function getStoreFieldsetsArray() : array
	// {
	// 	return $this->parameters;
	// }

	// public function getUpdateFieldsetsArray() : array {
	// 	return $this->getEditFieldsetsArray();
	// }

	// public function getParametersByType(string $type) : array
	// {
	// 	$getterMethod = implode("", [
	// 		'get',
	// 		ucfirst($type),
	// 		'FieldsetsArray'
	// 	]);

	// 	dd($this);

	// 	return $this->$getterMethod();
	// }

	static function getSanitizedContainerParameters(array $parameters)
	{
		if(is_string(current($parameters)))
			return [
				'default' => [
					'fields' => $parameters
				]
			];
		
		return $parameters;
	}

	static function buildNameAndLabelArray(string $name, array $parameters)
	{
		if(! ($parameters['name'] ?? false))
			$parameters['name'] = $name;

		if(! isset($parameters['label']))
			$parameters['label'] = __('fields.' . $name);

		return $parameters;
	}

	/**
	 * check if field is required in given rules array
	 *
	 * @param array $rules
	 * @return boolean
	 */
	static function hasRequiredRule(array $rules) : bool
	{
		return in_array('required', $rules);
	}

	/**
	 * build an array with a key per each rule
	 *
	 * @param array $parameters, array $rules
	 * @return array
	 */
	static function buildParametersRulesFromString(array $parameters) : array
	{
		$rules = $parameters['rules'];

		if(is_string($rules))
			$rules = explode("|", $rules);

		$parameters['rules'] = $rules;

		// $parameters['rules'] = [];

		// foreach($rules as $key => $rule)
		// 	if(strpos($rule, ":"))
		// 	{
		// 		$_rule = explode(":", $rule);

		// 		$parameters['rules'][$_rule[0]] = $_rule[1];

		// 		if($_rule[0] == 'max')
		// 			$parameters['max'] = $_rule[1];
		// 	}
		// 	else
		// 		$parameters['rules'][$rule] = true;

		// dd($parameters);

		return $parameters;
	}

	static function getFieldParametersFromString($fieldParameters)
	{
		$type = array_key_first($fieldParameters);

		$parameters['type'] = $type;

		$parameters['rules'] = $fieldParameters[$type];

		return $parameters;
	}

	// static function buildParametersRulesArray(array $parameters) : array
	// {
	// 	$rules = explode("|", $parameters[$type]);

	// 	return static::_buildParametersRules($parameters, $rules);
	// }

	static function sanitizeRules(array $parameters) : array
	{
		if(is_string($parameters['rules']))
			$parameters = static::buildParametersRulesFromString($parameters);

		if(! isset($parameters['required']))
			$parameters['required'] = static::hasRequiredRule($parameters['rules']);

		if($parameters['type'] == 'json')
		{
			foreach($parameters['fields'] as $fieldName => $fieldParameters)
			{
				if(count($fieldParameters) == 1)
					$fieldParameters = static::getFieldParametersFromString($fieldParameters);

				$fieldParameters = static::sanitizeRules($fieldParameters);

				$parameters['fields'][$fieldName] = static::buildNameAndLabelArray($fieldName, $fieldParameters);
			}
		}

		return $parameters;
	}

	static function parseFieldsetsParameters(array $fieldsetParameters) : array
	{
		if(! isset($fieldsetParameters['fields']))
			$fieldsetParameters = [
				'fields' => $fieldsetParameters
			];

		foreach($fieldsetParameters['fields'] as $fieldName => $fieldParameters)
		{
			if(count($fieldParameters) == 1)
				$fieldParameters = static::getFieldParametersFromString($fieldParameters);

			$fieldParameters = static::sanitizeRules($fieldParameters);

			$fieldsetParameters['fields'][$fieldName] = static::buildNameAndLabelArray($fieldName, $fieldParameters);
		}

		foreach($fieldsetParameters['fieldsets'] ?? [] as $fieldsetName => $_fieldsetParameters)
			$fieldsetParameters['fieldsets'][$fieldsetName] = static::parseFieldsetsParameters($_fieldsetParameters);

		return $fieldsetParameters;
	}

	static function parseFieldsParameters(array $parameters) : array
	{
		foreach($parameters as $fieldsetName => $fieldsetParameters)
			$parameters[$fieldsetName] = static::parseFieldsetsParameters($fieldsetParameters);

		return $parameters;
	}

	public function getFieldsetsParameters()
	{
		if(! empty($this->parameters))
			return $this->parameters;

		$this->setParameters(
			$this->_getFieldsetsParameters()
		);

		return $this->parameters;
	}

	//Deprecata in favore di buildParameters()
	// public function setParameters(array $parameters)
	// {
	// 	$parameters = static::getSanitizedContainerParameters($parameters);
	// 	$parameters = static::parseFieldsParameters($parameters);

	// 	$this->parameters = $parameters;
	// }

	public function setParameters(array $parameters)
	{
		$parameters = static::getSanitizedContainerParameters($parameters);

		$parameters = static::parseFieldsParameters($parameters);

		$this->parameters = $parameters;
	}

	static function makeByParameters(array $parameters) : self
	{
		$file = new static();

		$file->setParameters($parameters);

		return $file;
	}
}