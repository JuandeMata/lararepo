<?php

namespace Jespejoh\LaraRepo;

use Jespejoh\LaraRepo\Criteria\CriteriaInterface;

/**
 * Interface RepositoryInterface
 * @package Jespejoh\LaraRepo
 */
interface RepositoryInterface
{
    /**
     * Finds one item by the provided field.
     *
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @param array $columns Columns to retrieve with the object.
     * @return mixed Model|NULL An Eloquent object when there is a result, NULL when there are no matches.
     */
    public function findOneBy( $value, $field = 'id', array $columns = ['*'] );

    /**
     * Finds ALL items the repository abstract without any kind of filter.
     *
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAll( array $columns = ['*'] );

    /**
     * Finds ALL items by the provided field.
     *
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAllBy( $value, $field, array $columns = ['*'] );

    /**
     * Finds ALL the items in the repository where the given field is inside the given values.
     *
     * @param array $value mixed Array of values used for the filter.
     * @param string $field Field on the database that you will filter by.
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAllWhereIn( array $value, $field,  array $columns = ['*'] );

    /**
     * Allows you to eager-load entity relationships when retrieving entities, either with or without criterias.
     *
     * @param array|string $relations Relations to eager-load along with the entities.
     * @return mixed The current repository object instance.
     */
    public function with( $relations );

    /**
     * @param CriteriaInterface $criteria Object that declares and implements the criteria used.
     * @return mixed The current repository object instance.
     */
    public function addCriteria( CriteriaInterface $criteria );

    /**
     * Finds ONE (the first) item in the repository that matches the saved/given criteria.
     *
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Model|NULL A model object when there is a result, NULL when there are no matches.
     */
    public function findOneByCriteria( array $columns = ['*'] );

    /**
     * Finds ALL items in the repository that match the saved/given criteria.
     *
     * @param array $columns Columns to retrieve with the objects.
     * @return mixed Collection Laravel Eloquent's Collection that may or may not be empty.
     */
    public function findAllByCriteria( array $columns = ['*'] );

    /**
     * Skips the current criteria (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|TRUE $status If you want to skip the criteria or not.
     * @return mixed The current repository object instance.
     */
    public function skipCriteria( $status = TRUE );

    /**
     * Returns a collection with the current criteria loaded in the repository, whether it's being applied or not.
     *
     * @return mixed Collection Current criteria loaded into the repository.
     */
    public function getCriteria();

    /**
     * Returns a Paginator that based on the criteria or filters given.
     *
     * @param int $perPage Number of results to return per page.
     * @param array $columns Columns to retrieve with the objects.
     * @return Paginator object with the results and the paginator.
     */
    public function paginate( $perPage, array $columns = ['*'] );

    /**
     * Allows you to set the current page with using the paginator. Useful when you want to overwrite the $_GET['page']
     * parameter and retrieve a specific page directly without using HTTP.
     *
     * @param int $page The page you want to retrieve.
     * @return mixed The current repository object instance.
     */
    public function setCurrentPage( $page );

    /**
     * Resets the current scope of the repository. That is: clean the criteria, and all other properties that could have
     * been modified, like current page, etc.
     *
     * @return mixed The current repository object instance.
     */
    public function resetScope();

    /**
     * Creates a new entity of the entity type the repository handles, given certain data.
     *
     * @param array $data Data the entity will have.
     * @return mixed Model|NULL An Eloquent object when the entity was created, NULL in case of error.
     */
    public function create( array $data );

    /**
     * Updates as many entities as the filter matches with the given $data.
     *
     * @param array $data Fields & new values to be updated on the entity/entities.
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return mixed Model|NULL An Eloquent object representing the updated entity, NULL in case of error.
     */
    public function updateBy( array $data, $value, $field = 'id' );

    /**
     * Removes as many entities as the filter matches. If softdelete is applied, then they will be soft-deleted.
     *
     * @param $value
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return boolean TRUE It will always return TRUE.
     */
    public function delete( $value, $field = 'id' );

    /**
     * @return int number of records matching the criteria (or total amount of records).
     */
    public function count();

}