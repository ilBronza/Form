<?php

namespace IlBronza\Form\Helpers\FieldsetsProvider\Traits;

use Auth;

trait FieldsetsProviderRolesTrait
{
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
}