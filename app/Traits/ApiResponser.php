<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ( $collection->isEmpty() ) {
            return $this->successResponse(['data' => $collection], $code);
        }

        $transformer = $collection->first()->transformer; // se obtiene el transformador

        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);
        $collection = $this->cacheResponse($collection);

        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer; // se obtiene el transformador
        $instance = $this->transformData($instance, $transformer);
        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse($message, $code);
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach( request()->query() as $query => $value ) {
            $attribute = $transformer::originalAttribute($query);

            if ( isset( $attribute, $value ) ) {
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if ( request()->has('sort_by') ) {
            $attribute = $transformer::originalAttribute(request()->sort_by);

            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];

        Validator::validate(request()->all(), $rules);

        // entrega la pagina en la que estamos
        $page = LengthAwarePaginator::resolveCurrentPage();

        // $perPage = 15; // elementos por pagina
        $perPage = request()->has('per_page') ? (int) request()->per_page : 15;

        $results = $collection->slice( ($page - 1) * $perPage, $perPage )->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(), // esto elimina los parametros de url, puede borrar el sort_by
        ]);

        $paginated->appends(request()->all()); // se agregan los parametros de la url, como el sort_by

        return $paginated;
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    protected function cacheResponse($data)
    {
        $url = request()->url(); // se obtiene la url actual
        $queryParams = request()->query();

        ksort($queryParams); // ordena el arreglo según la clave

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember($fullUrl, 30/60, function() use ($data) {
            return $data;
        });
    }

}