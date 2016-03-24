<?php

namespace Jespejoh\LaraRepo\src;

use Jespejoh\LaraRepo\Criteria\CriteriaInterface;
use Jespejoh\LaraRepo\RepositoryInterface;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * Class EloquentRepository
 * @package Jespejoh\LaraRepo\src
 */
abstract class EloquentRepository implements RepositoryInterface
{

    protected $model;
    protected $with;
    protected $skipCriteria;
    protected $criteria;
    private $cleanModel;

    /**
     * @param Model $model
     * @param Collection $collection
     */
    public function __construct( Model $model, Collection $collection )
    {
        $this->model = $model;
        // A clean copy of the model is needed when the scope needs to be reset.
        $this->cleanModel = $model;
        $this->skipCriteria = FALSE;
        $this->criteria = $collection;
    }

    /**
     * @param $value
     * @param string $field
     * @param array $columns
     * @return mixed
     */
    public function findOneBy($value, $field = 'id', array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        return $this->model->where( $field, $value)->first( $columns );
    }

    /**
     * @param $value
     * @param $field
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($value, $field, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        return $this->model->where( $field, $value )->get( $columns );
    }

    /**
     * @param array $value
     * @param string $field
     * @param array $columns
     * @return mixed
     */
    public function findAllWhereIn(array $value, $field, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        return $this->model->whereIn( $field, $value )->get( $columns );
    }

    /**
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll( array $columns = ['*'] )
    {
        $this->eagerLoadRelations();
        return $this->model->all( $columns );
    }

    /**
     * @param array|string $relations
     * @return $this
     */
    public function with( $relations )
    {
        if ( is_string( $relations ) ) $relations = func_get_args();

        $this->with = $relations;

        return $this;
    }


    /**
     * @param CriteriaInterface $criteria
     * @return $this
     */
    public function addCriteria( CriteriaInterface $criteria)
    {
        $this->criteria->push($criteria);
        return $this;
    }


    /**
     * @param array $columns
     * @return mixed
     */
    public function findOneByCriteria( array $columns = ['*'] )
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->first( $columns );

        return $result;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function findAllByCriteria( array $columns = ['*'] )
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->get( $columns );

        return $result;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria( $status = TRUE )
    {
        $this->skipCriteria = $status;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }


    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();
        return $this->model->paginate( $perPage, $columns );
    }


    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage( $currentPage )
    {
        Paginator::currentPageResolver(function() use ($currentPage)
        {
            return $currentPage;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->criteria = $this->criteria->make();
        $this->skipCriteria( FALSE );
        $this->model = $this->cleanModel;
        return $this;
    }


    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $cleanFields = $this->cleanUnfillableFields( $data );

        $createdObject = $this->model->create( $cleanFields );

        return $createdObject;
    }

    /**
     * @param array $data
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function updateBy(array $data, $value, $field = 'id')
    {
        $cleanFields = $this->cleanUnfillableFields( $data );

        $updateObject = $this->model->where( $field, $value)->update( $cleanFields );

        return $updateObject;
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function delete( $value, $field = 'id' )
    {
        return $this->model->where( $field, $value )->delete();
    }


    /*******************************************************************************************************************
     *******************************************************************************************************************
     *******************************************************************************************************************/

    /**
     *
     */
    protected function eagerLoadRelations()
    {
        if ( is_array( $this->with ) )
        {
            $this->model = $this->model->with( $this->with );
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function cleanUnfillableFields( array $data )
    {
        $fillableFields = $this->cleanModel->getFillable();

        foreach( $data as $key => $value )
        {
            if ( !in_array( $key, $fillableFields ) ) unset( $data[ $key ] );
        }

        return $data;
    }

    /**
     * @return $this
     */
    private function applyCriteria()
    {
        if( $this->skipCriteria === TRUE ) return $this;

        foreach( $this->getCriteria() as $criteria )
        {
            if( $criteria instanceof CriteriaInterface ) $this->model = $criteria->apply( $this->model, $this );
        }

        return $this;
    }


}