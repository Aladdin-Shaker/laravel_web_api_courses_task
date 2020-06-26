<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponser
{
    // Success Response
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    // Failed Response
    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code]);
    }

    // Show all comming data from response
    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }
        // $collection = $this->filterData($collection);
        $collection = $this->sortData($collection);
        $collection = $this->paginateData($collection);
        return $this->successResponse($collection, $code);
    }


    // Show single comming data from response
    protected function showOne(Model $instance, $code = 200)
    {
        return $this->successResponse($instance, $code);
    }

    // Show specific message
    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    // sort data by something
    protected function sortData(Collection $collection)
    {
        // check if there is sort_by query in the request
        if (request()->has('sort_by')) {
            $attribute = request()->sort_by; // get attribute of sort_by
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    // filter data comming from request
    protected function filterData(Collection $collection)
    {
        // get the query from the request and get the attribute with value
        // ex => http://127.0.0.1:8000/api/activities?title=test
        // $query = title  & $value = test
        foreach (request()->query() as $query => $value) {
            if (isset($query, $value)) {
                // dd(isset($query, $value));
                $collection = $collection->where($query, $value);
            }
        }
        return $collection;
    }

    // paginate data
    protected function paginateData(Collection $collection)
    {
        // rules for per page
        $rules = [
            'per_page' => 'integer|min:1|max:50'
        ];
        // to validate we are in trait not controller, so should use validator from Facades class
        Validator::validate(request()->all(), $rules);
        $perPage = 15;  // number of rows each page
        // check if there any per page number from user
        if (request()->has('per_page')) {
            $perPage = (int) request()->per_page;
        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage(); // get the current page
        // data per page => start by zero as any array
        $result = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        // make the pagination
        $paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
        // when we resole the "path" parameter, the parameter current page => will ignore the other parameter like sort and filter
        $paginated->appends(request()->all());
        return $paginated;
    }
}
