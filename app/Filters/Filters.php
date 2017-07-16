<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
	protected $request;
	protected $builder;

	protected $filters = [];

	public function __construct(Request $request)
	{
		$this->request = $request;	
	}

	public function apply($builder)
	{
		$this->builder = $builder;

		foreach($this->getFilters() as $filter => $value) {
			if(method_exists($this, $filter)) {
				$this->$filter($value);	
			}		
		}

		return $this->builder;
		
	}

	public function getFilters()
	{
		// return only those requests that are in the $filters array;
		// The intersect method removes any values from the original collection that are not present in the given array or collection.
		return $this->request->intersect($this->filters);	
	}


}