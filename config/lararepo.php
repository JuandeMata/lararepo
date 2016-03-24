<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Interfaces & repositories base path
    |--------------------------------------------------------------------------
    |
    | By default the package considers that your interfaces live in
    | app/Repositories. You can however set this path to whatever value
    | you want. I personally like to locate all my project files inside a
    | folder located in "app", something like "app/MyApp/Repositories".
    |
    */
    'path' => app_path('Repositories'),

    /*
    |--------------------------------------------------------------------------
    | Interfaces namespace
    |--------------------------------------------------------------------------
    |
    | You can specify the namespace used in your repositories interfaces.
    | Once again, I like to put everything under the namespace of my app,
    | so my repository interfaces usually live under the namespace of my
    | application: "MyApp\Repositories".
    |
    */
    'namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Implementation
    |--------------------------------------------------------------------------
    |
    | As we can have same interface but different implementations that support
    | our repositories, we can define the implementation that we want to use.
    | For now, only Eloquent is supported. This is important because by default
    | our repositories should live in a sub-directory located in 'path' and
    | name the same way than this "implementation" variable. This is done
    | because the repositories are automatically mapped and loaded based on
    | a combination of the implementation and the path. So to say: if you have
    | your repositories under "app/Repositories", and the implementation is
    | 'Eloquent', the system will automatically try to bind every file
    | located in 'app/Repositories/MyCustomRepositoryInterface.php' to a file
    | located at 'app/Repositories/Eloquent/MyCustomRepository.php'. The namespace
    | of the implementation must also be preceded by this value. In the previous
    | example, the namespace of our repository would be: "App\Repositories\Eloquent".
    | Be with capital letter, and name this configuration value exactly the same
    | way that your folder.
    |
    | Values supported: Eloquent.
    |
    */
    'implementation' => 'Eloquent',


    /*
    |--------------------------------------------------------------------------
    | Skip repositories
    |--------------------------------------------------------------------------
    |
    | Sometimes you may wish to skip the auto-binding of some repositories.
    | You can specify here what of those INTERFACES should be skipped from the
    | auto-binder. You must specify the name of the file, as the skip happens
    | when scanning the repository.
    |
    */
    'skip' => [ 'BaseRepositoryInterface.php' ],

];