<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use Illuminate\Database\Eloquent\Model;

class EditFieldsetsProvider extends FieldsetsProvider
{
	static function addFieldsetsToFormByParametersFile()
	{

	}

	static function getFieldsetsByParametersFile(FieldsetParametersFile $file, Model $model)
	{
		$fieldsetProvider = new static('show');

		$fieldsetProvider->setModel($model);

		return $fieldsetProvider->getParametersByFile($file);
	}
}