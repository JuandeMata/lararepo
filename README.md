# LaraRepo

A package that provides a neat implementation for integrating the Repository pattern with Laravel &amp; Eloquent.

## Goal

Working with repositories can provide a great way to not only decouple your code but also separate concerns and
isolate stuff, as well as separate and group responsibilities. Most of the time we will perform really generic actions
on our database tables, like create, update, filter or delete.  

However, using repositories is not always a good idea, specially with Laravel and its ORM, Eloquent, as it -sometimes-
forces you to give up some great features in favor of *better architecture* (it depends). For some projects this may be
an overkill and therefore is up to the developer to see if this level of complexity is needed or not.

This package aims to provide a boilerplate when implementing the Repository pattern on Laravel's Eloquent ORM. The way
it provides it it's using a `RepositoryInterface` and a basic `Repository` implementation that is able to work with Eloquent models,
 providing basic methods that will cover 85% if the Database operations that you will probably do in your application.

## Installation

1. Install this package by adding it to your `composer.json` or by running `composer require jespejoh/lararepo` in your project's folder.
2. Add the provider to your `config/app.php` file: `Jespejoh\LaraRepo\LararepoServiceProvider::class`
3. Publish the configuration file by running `php artisan vendor:publish --provider="/vendor/jespejoh/lararepo/config/lararepo.php"`
4. Open the configuration file (`config/lararepo.php`) and configure paths according to your needs.
5. Ready to go!


## Usage

To start using the package, you just need to create a folder where you will place all your repository interfaces and the
repository implementations and extend every single repository from the `EloquentRepository` class offered by the package.  

The Eloquent model to be handled by the repository that you have created should also be injected via the Repo constructor.  

The package then will try to load all the repository interfaces and bind them to a repository implementation according to
the parameters specified in the `config/lararepo.php` file.

## Examples

Let's consider we will have all our repositories in a folder called "Repositories", under a folder called "MyWebbApp" inside
the "app" folder: app/MyWebApp/Repositories.

At the root of this folder we'll have all our interfaces following the next name convention: `[RepositoryName]Interface.php`

**NOTE**: It does not really matter the name that we use as long as we use "Interface" as suffix. This is important because the 
auto binder will try to find all files matching this pattern.

Inside this Repositories folder, we must have another folder called Eloquent, that will contain all our implementations for
the repositories, following the next name convention: `[RepositoryName].php`.  

We should have a structure like this:  

```
+-- app
|   +-- MyApp
|       +-- Repositories
|           +-- UsersRepositoryInterface.php
|           +-- RolesRepositoryInterface.php
|           +-- CommentsRepositoryInterface.php
|           +-- PostsRepositoryInterface.php
|           +-- Eloquent
|               +-- UsersRepository.php
|               +-- RolesRepository.php
|               +-- CommentsRepository.php
|               +-- PostsRepository.php  
```

Let's see what the `UsersRepositoryInterface.php` and the `UsersRepository.php` would have.

```php

<?php

namespace MyApp\Repositories;

use Jespejoh\LaraRepo\RepositoryInterface;

interface UsersRepositoryInterface extends RepositoryInterface {

    // here you would write the contract for methods that your repository will implement.
}

```

```php

<?php

namespace MyApp\Repositories\Eloquent;

use Jespejoh\LaraRepo\src\EloquentRepository;
use MyApp\Repositories\UsersRepositoryInterface;
use MyApp\Models\User;

class UsersRepositoryInterface extends EloquentRepository implements UsersRepositoryInterface  {

    public function __construct( User $user ) {
        parent::__construct( $user );
    }
    
    // methods that your repository should implement...
}

```

Now we need to configure the `config/lararepo.php` file to match our paths and namespace:  

```php

    'path' => app_path('MyApp/Repositories'),
    
    'namespace' => 'MyApp\Repositories',

    'implementation' => 'Eloquent',

```


Now the repository is ready to be used and injected in other services or controllers:

```php

<?php

namespace MyApp\Services\Users;

use MyApp\Repositories\UsersRepositoryInterface;

class UsersService implements UsersServicesInterface  {

    protected $usersRepository;

    public function __construct( UsersRepositoryInterface $usersRepositoryInterface ) {
        $this->usersRepository = $usersRepositoryInterface;
    }
   
    // other methods in your service.
}

```

**NOTE**: This example assumes that you have configured your `composer.json` to autoload the files on app/MyApp with the MyApp
namespace.

## Methods shipped by default.

The repository package offers a series of methods by default. These are:

```php

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
     * Restores from soft-delet as many entities as the filter matches.
     *
     * @param $value
     * @param $value mixed Value used for the filter.
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return boolean TRUE It will always return TRUE.
     */
    public function restore( $value, $field = 'id' );
    
    /**
     * @return int number of records matching the criteria (or total amount of records).
     */
    public function count();

```


## Criteria

To avoid having our repository full of methods like `findActiveUsers()`, `findActiveUsersOlderThan( $date )` and so on,
we'll be using Criteria to apply filters to our searches or queries.  

A Criteria is just a PHP object that implements the CriteriaInterface provided by this package and that operates on the
Eloquent model to apply the Query or set of queries that we want to apply for an specific search.  

To create your own criterias, place them wherever you want and make them implement the CriteriaInterface provided.  

For instance: Imagine we have an application where users can register via Facebook, Twitter or email. We would need
to retrieve all users based on the method they used for registering. We would have a criteria like this:  

```php

<?php

namespace MyApp\Repositories\Eloquent\Criteria\Users;

use Jespejoh\LaraRepo\Criteria\CriteriaInterface;
use MyApp\Models\User;

class RegisteredVia implements CriteriaInterface  {

    protected $registeredVia;
    protected $onlyActive;

    public function __construct( $registeredVia, $onlyActive = TRUE ) {
    
        $this->registeredVia = $registeredVia;
        $this->onlyActive = $onlyActive;
        
    }
    
    
    public function apply( $queryBuilder ) {
    
        if ( $this->onlyActive ) $queryBuilder = $queryBuilder->where( 'active', TRUE );
        
        return $queryBuilder->where( 'registered_via', $this->registered_via );
        
    }
   
}

```

Now in your services or controllers you can use this criteria like this:


```php

    $registeredViaFacebookCriteria = new RegisteredVia( 'facebook' );
    
    return $this->usersRepository->addCriteria( $registeredViaFacebookCriteria )->findAllByCriteria();
   

```

We could even chain different criterias:


```php

    $registeredViaFacebookCriteria = new RegisteredVia( 'facebook' );
    $orderByCreationDate = new OrderBy( 'created_at', 'ASC' );
    
    return $this->usersRepository
                ->addCriteria( $registeredViaFacebookCriteria )
                ->addCriteria( $orderByCreationDate )
                ->findAllByCriteria();
```


## Changelog

--No version released yet--


## Credits

- [Jesús Espejo](https://github.com/jespejoh) ([Twitter](https://twitter.com/jespejo89))
- This great article by Bosnadev was source of Inspiration for almost everything included in this package: https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/


## Bugs & contributing

* Found a bug? That's good and bad. Let me know using the Issues on Github.
* Need a feature or have something interesting to contribute with? Great! Open a pull request.


## License

The MIT License (MIT). Please see [License File](https://github.com/ellipsesynergie/api-response/blob/master/LICENSE) for more information.