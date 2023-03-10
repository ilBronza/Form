<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider\Traits;

trait FieldsetsProviderParsersTrait
{
	// /**
	//  * check if field is required in given rules array
	//  *
	//  * @param array $rules
	//  * @return boolean
	//  */
	// private function checkIfFieldIsRequired(array $rules)
	// {
	// 	return in_array('required', $rules);
	// }

	// /**
	//  * build an array with a key per each rule
	//  *
	//  * @param array $parameters, array $rules
	//  * @return array
	//  */
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
	// 	if($this->isSingleRowField($parametersString))
	// 		return $this->getFieldParametersSingleRow($fieldName, $parametersString);

	// 	return $this->getFieldParametersKeyValueRow($fieldName, $parametersString);
	// }

	// protected function isSingleRowField(array $fieldContent) : bool
	// {
	// 	return count($fieldContent) == 1;
	// }

	// public function parametersContainsRelationship(array $fieldParameters) : bool
	// {
	// 	return isset($fieldParameters['relation']);
	// }

	// public function getRelationshipsFields() : array
	// {
	// 	$fields = $this->getAllFieldsArray();

	// 	foreach($fields as $index => $fieldParameters)
	// 		if(! $this->parametersContainsRelationship($fieldParameters))
	// 			unset($fields[$index]);

	// 	return $fields;
	// }
}
