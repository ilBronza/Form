<?php

namespace IlBronza\Form\Traits;

use Exception;
use Illuminate\Support\Collection;
use IlBronza\UikitTemplate\Fetcher;

/**
 * Gives a renderable element fetchers grouped by their rendering position.
 *
 * Fetchers use the same shape as extra views consumers expect:
 * ['position' => string, 'fetcher' => Fetcher].
 */
trait HasFetchersTrait
{
	/** @var Collection<int, array{position: string, fetcher: Fetcher}>|null */
	public ?Collection $fetchers = null;

	/**
	 * @param iterable<array{position: string, fetcher: Fetcher}> $fetchers
	 */
	public function setFetchers(iterable $fetchers = []) : static
	{
		$this->clearFetchers();

		foreach ($fetchers as $fetcher)
			$this->addFetcher($fetcher['position'], $fetcher['fetcher']);

		return $this;
	}

	public function addFetcher(string $position, Fetcher $fetcher) : static
	{
		$this->checkValidFetcherPosition($position);

		$this->getFetchers()->push([
			'position' => $position,
			'fetcher' => $fetcher,
		]);

		return $this;
	}

	public function addFetcherByUrl(string $url, string $position = 'bottom', array $parameters = []) : static
	{
		return $this->addFetcher($position, new Fetcher(array_merge(
			$parameters,
			['url' => $url],
		)));
	}

	/**
	 * @param iterable<Fetcher> $fetchers
	 */
	public function addFetchers(string $position, iterable $fetchers) : static
	{
		foreach ($fetchers as $fetcher)
			$this->addFetcher($position, $fetcher);

		return $this;
	}

	/** @return Collection<int, array{position: string, fetcher: Fetcher}> */
	public function getFetchers() : Collection
	{
		return $this->fetchers ??= collect();
	}

	/** @return Collection<int, Fetcher> */
	public function getFetchersPosition(string $position) : Collection
	{
		return $this->getFetchers()
			->where('position', $position)
			->pluck('fetcher');
	}

	public function hasFetchers() : bool
	{
		return $this->getFetchers()->isNotEmpty();
	}

	public function hasFetchersPosition(string $position) : bool
	{
		return $this->getFetchersPosition($position)->isNotEmpty();
	}

	public function hasFetchersPositions($positions = null) : bool
	{
		$positions = is_array($positions) ? $positions : func_get_args();

		foreach ($positions as $position)
			if ($this->hasFetchersPosition($position))
				return true;

		return false;
	}

	public function clearFetchers(?string $position = null) : static
	{
		if ($position === null)
		{
			$this->fetchers = collect();

			return $this;
		}

		$this->fetchers = $this->getFetchers()
			->reject(fn (array $fetcher) => $fetcher['position'] === $position)
			->values();

		return $this;
	}

	public function getValidFetchersPositions() : array
	{
		if (property_exists($this, 'availableFetchersPositions'))
			return static::$availableFetchersPositions;

		if (method_exists($this, 'getValidExtraViewsPositions'))
			return $this->getValidExtraViewsPositions();

		return ['top', 'bottom', 'left', 'right'];
	}

	protected function checkValidFetcherPosition(string $position) : void
	{
		if (! in_array($position, $this->getValidFetchersPositions()))
			throw new Exception($position . ' is not a valid fetcher position for this ' . class_basename($this));
	}
}
