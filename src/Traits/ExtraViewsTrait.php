<?php

namespace IlBronza\Form\Traits;

use IlBronza\Buttons\Button;
use IlBronza\UikitTemplate\Fetcher;
use Illuminate\Support\Collection;

trait ExtraViewsTrait
{
	abstract function getValidExtraViewsPositions() : array;

	private function getExtraViewsCollection() : ? Collection
	{
		return $this->extraViews ?? null;
	}
	public function getFetchers() : Collection
	{
		return $this->fetchers;
	}

	private function createExtraViewsCollection()
	{
		$result = [];

		foreach($this->getValidExtraViewsPositions() as $position)
			$result[$position] = collect();

		$this->extraViews = collect($result);
	}

	private function checkForExtraViewsCollection()
	{
		if(! $this->getExtraViewsCollection())
			$this->createExtraViewsCollection();
	}

	private function checkValidPosition(string $position)
	{
		if(! in_array($position, static::getValidExtraViewsPositions()))
			throw new \Exception($position . ' is not a valid position for this ' . class_basename($this));
	}

	public function addFetcher(string $position, Fetcher $fetcher)
	{
		$this->checkValidPosition($position);

		$this->getFetchers()->push([
			'position' => $position,
			'fetcher' => $fetcher
		]);
	}

	public function addExtraView(string $position, string $viewName, array $parameters)
	{
		$this->checkValidPosition($position);
		$this->checkForExtraViewsCollection();

		$this->extraViews->get($position)[$viewName] = $parameters;
	}

	public function hasFetchersPositions($positions) : bool
	{
		foreach($positions as $position)
			if($this->getFetchers()->firstWhere('position', $position))
				return true;

		return false;
	}

	public function getFetchersPosition(string $position) : Collection
	{
		return $this->getFetchers()->where('position', $position)->pluck('fetcher');
	}

	public function getExtraViewsPosition(string $position) : Collection
	{
		if(! $this->getExtraViewsCollection())
			return collect();

		return $this->extraViews->get($position) ?? collect();
	}

	public function hasExtraViewsPosition(string $position) : bool
	{
		if(! $position = $this->getExtraViewsPosition($position))
			return false;

		return count($position) > 0;
	}

	public function hasExtraViewsPositions($positions = null) : bool
	{
		$positions = is_array($positions) ? $positions : func_get_args();

		if($this->hasFetchersPositions($positions))
			return true;

		if((! $this->getExtraViewsCollection()))
			return false;

		foreach($positions as $position)
			if($this->hasExtraViewsPosition($position))
				return true;

		return false;
	}

	public function renderExtraViews(string $position) : string
	{
		$result = [];

		foreach($this->getExtraViewsPosition($position) as $name => $parameters)
			$result[] = view($name, $parameters)->render();

		foreach($this->getFetchersPosition($position) as $fetcher)
			$result[] = $fetcher->render();

		return implode(" ", $result);
	}
}