<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider;

use Auth;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormFieldsProvider;
use IlBronza\Form\FormFieldset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FieldsetsProvider
{
	public $parametersFile;

	public function __construct(string $type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}

	public function setParametersByFile(FieldsetParametersFile $file)
	{
		$getterMethod = implode("", [
			'get',
			ucfirst($this->getType()),
			'FieldsetsArray'
		]);

		$parameters = $file->$getterMethod();

		$this->setParameters($parameters);
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function getModel() : Model
	{
		return $this->model;
	}

	public function getParametersByFile(FieldsetParametersFile $file)
	{
		$this->setParametersByFile($file);

		return $this->getFieldsetsCollection();
	}

	public function getFieldsetsArray()
	{
		return $this->parameters;
	}

	protected function getFieldsetFields(array $fieldsetParameters) : array
	{
		$fieldsParameters = $fieldsetParameters['fields'];

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

	public function addFieldset(Collection $fieldsetCollection, string $name, array $fieldsetParameters)
	{
		if(! $this->userCanSeeFieldsetByRoles($fieldsetParameters))
			return ;

		$fields = $this->getFieldsetFields($fieldsetParameters);
		$subFieldsets = $this->getSubFieldsets($fieldsetParameters);
		$fieldsetOptions = $this->getFieldsetOptions($fieldsetParameters);

		$fieldset = FormFieldset::createByNameAndParameters($name, $fieldsetOptions);

		$fieldset->setModel(
			$this->getModel()
		);

		$fieldsetCollection->push($fieldset);

		foreach($fields as $fieldName => $field)
			$fieldset->addFormField(
				FormFieldsProvider::createByNameParameters($fieldName, $field)
			);

		foreach($subFieldsets as $name => $subFieldsetParameters)
			$this->addFieldset($fieldset->fieldsets, $name, $subFieldsetParameters);
	}

	public function getFieldsetsCollection() : Collection
	{
		$this->fieldsets = collect();

		$fieldsets = $this->getFieldsetsArray();

		foreach($fieldsets as $name => $fieldsetParameters)
			$this->addFieldset($this->fieldsets, $name, $fieldsetParameters);

		return $this->fieldsets;
	}


	/**
	 * START FIELD PARAMETERS
	**/

	private function getFieldParametersSingleRow(string $name, array $parametersString)
	{
		$type = array_key_first($parametersString);

		$parameters = $this->buildNameAndLabelArray($name, $parametersString);
		$parameters['type'] = $type;

		$rules = explode("|", $parametersString[$type]);

		return $this->buildParametersRules($parameters, $rules);
	}

	private function getFieldParameters(string $fieldName, array $parametersString)
	{
		if(count($parametersString) == 1)
			return $this->getFieldParametersSingleRow($fieldName, $parametersString);

		return $this->getFieldParametersKeyValueRow($fieldName, $parametersString);
	}

	/**
	 * END FIELD PARAMETERS
	**/



	/**
	 * START ROLES METHODS
	**/

	protected function userCanSeeFieldsetByRoles(array $fieldsetParameters) : bool
	{
		if(! ($fieldsetParameters['roles'] ?? false))
			return true;

		if(! Auth::user())
			abort(403);

		return Auth::user()->hasAnyRole($fieldsetParameters['roles']);		
	}

	protected function filterByRolesAndPermissions(array $fields) : array
	{
		$fields = $this->filterByRoles($fields);
		$fields = $this->filterByPermissions($fields);

		return $fields;
	}

	protected function filterByRoles(array $fields) : array
	{
		if(($user = Auth::user())&&($user->hasRole('superadmin')))
			return $fields;

		foreach($fields as $key => $field)
		{
			if(! isset($field['roles']))
				continue;

			if(($user)&&($user->hasRole($field['roles'])))
				continue;

			unset($fields[$key]);
		}

		return $fields;
	}

	protected function filterByPermissions(array $fields) : array
	{
		if(($user = Auth::user())&&($user->hasRole('superadmin')))
			return $fields;

		foreach($fields as $key => $field)
		{
			if(! isset($field['permissions']))
				continue;

			if(($user)&&($user->hasAnyPermission($field['permissions'])))
				continue;

			unset($fields[$key]);
		}

		return $fields;
	}

	/**
	 * END ROLES METHODS
	**/


}