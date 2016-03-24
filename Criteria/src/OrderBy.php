<?php

namespace Jespejoh\LaraRepo\Criteria\src;

use Jespejoh\LaraRepo\Criteria\CriteriaInterface;

/**
 * Class OrderBy
 * @package Jespejoh\LaraRepo\Criteria\src
 */
class OrderBy implements CriteriaInterface
{
    protected $orderBy;
    protected $direction;


    /**
     * @param $orderBy
     * @param string $direction
     */
    public function __construct( $orderBy, $direction = 'ASC' )
    {
        $this->orderBy = $orderBy;
        $this->direction = $direction;
    }


    /**
     * @param mixed $queryBuilder
     * @return mixed
     */
    public function apply( $queryBuilder )
    {
        return $queryBuilder->orderBy( $this->orderBy, $this->direction );
    }


}