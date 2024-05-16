<?php

namespace IlBronza\Form\Helpers;

class FieldsetsExtractorHelper
{
	static function getFieldsParametersByFieldsetsParametersArray(array $fieldsets) : array
	{
		$result = [];

		foreach($fieldsets as $fieldsetArray)
		{
			foreach($fieldsetArray['fields'] as $name => $fieldParameters)
				$result[$name] = $fieldParameters;

			if($fieldsetArray['fieldsets'] ?? false)
				$result = array_merge(
					$result,
					static::getFieldsParametersByFieldsetsParametersArray($fieldsetArray['fieldsets'])
				);
		}

		return $result;
	}
}