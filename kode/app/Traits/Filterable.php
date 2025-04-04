<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
trait Filterable
{
  
    /**
     * Get Only Recycled Data
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeRecycle(Builder $q): Builder{
        return $q->when(request()->routeIs('admin.*.recycle.list'),fn(Builder $query): Builder => $query->onlyTrashed());
    }

    /**
     * scope search filter
     *
     * @param Builder $query
     * @param array $params
     * @param boolean $like
     * @return Builder
     */
    public function scopeSearch(Builder $query,array $params,bool $like = true)  :Builder{

        $search = request()->input("search");
        if (!$search) return $query;
        $search = $like ? "%$search%" : $search;



        return $query->where(function(Builder $q) use ($params, $search) {
               return collect($params)->map(function(string $param) use($q,$search)  {
                    return $q->when((strpos($param, ':') !== false),
                      fn(Builder $q) :Builder => $this->searchRelationalData($q, $param, $search),
                          fn(Builder $q): Builder => $q->orWhere($param, 'LIKE', $search));});});
    }


    /**
     * Scope filter
     *
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function scopeFilter(Builder $query,array $params): Builder{

        $filters   = array_keys(request()->all());
        collect($params)->map(function(string $param) use($query,$filters) : Builder{
            return $query->when((strpos($param, ':') !== false),
                        fn(Builder $q): Builder => 
                              $this->filterRelationalData($query, $param, $filters),
                                    fn(Builder $query): Builder =>
                                        $query->when(in_array($param, $filters) && request()->input($param) !== null , 
                                            fn(Builder $query): Builder => $query->when(gettype(request()->input($param)) === 'array',
                                                fn(Builder $query) : Builder => $query->whereIn($param,  request()->input($param)),
                                                   fn(Builder $query) : Builder =>  $query->where($param, request()->input($param)))));
                        });

        return $query;


    }

    /**
     * Date Filter
     *
     * @param Builder $query
     * @param string $column
     * @return Builder
     */
    public function scopeDate(Builder $query, string $column = 'created_at') : Builder {


            try {
                if (!request()->input('date'))   return $query;

                $dateRangeString             = request()->input('date');
                $start_date                  = $dateRangeString;
                $end_date                    = $dateRangeString;
                if (strpos($dateRangeString, ' - ') !== false) list($start_date, $end_date) = explode(" - ", $dateRangeString); 
        
                $start_date = Carbon::createFromFormat('m/d/Y', $start_date)->format('Y-m-d');
                $end_date   = Carbon::createFromFormat('m/d/Y', $end_date)->format('Y-m-d');
        
                return $query->where(fn (Builder $query): Builder =>  
                                $query->whereBetween($column , [$start_date, $end_date])
                                        ->orWhereDate($column , $start_date)
                                        ->orWhereDate($column , $end_date));
            } catch (\Throwable $th) {
                return $query;
            }



    }


    /**
     * Search relational data
     *
     * @param Builder $query
     * @param string  $relations
     * @param string $search
     * @return Builder
     */
    private function searchRelationalData(Builder $query,string $relations, string $search): Builder{

        list($relation, $keys) = explode(":", $relations); 
        collect(explode(',',$keys))->map(fn(string $column): Builder => 
            $query->orWhereHas( $relation , fn (Builder $q)  : Builder =>  $q->where($column,'like',$search))
        );

        return $query;
    }


    /**
     * Filter relational data
     *
     * @param Builder $query
     * @param string $relations
     * @param array $filters
     * @return Builder
     */
    private function filterRelationalData(Builder $query,string $relations,array $filters): Builder{
        list($relation, $keys) = explode(":", $relations); 
        collect(explode(',', $keys))->map( fn(string $column): Builder =>
                $query->when(in_array($relation, $filters) && request()->input($relation) != null ,
                         fn(Builder $query) :Builder => $query->whereHas($relation,
                                 fn(Builder $q) :Builder => $q->where($column,request()->input($relation)))));
        return $query;
    }

}