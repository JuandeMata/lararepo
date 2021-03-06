<?php

namespace Jespejoh\LaraRepo;

use Illuminate\Support\ServiceProvider;


/**
 * Class LaraRepoServiceProvider
 * @package Jespejoh\LaraRepo
 */
class LararepoServiceProvider extends ServiceProvider
{

    private $configPath = '/config/lararepo.php';


    /**
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.$this->configPath => config_path('lararepo.php'),
        ]);
    }


    /**
     *
     */
    public function register()
    {
        // merge default config
        $this->mergeConfigFrom(
            __DIR__.$this->configPath , 'lararepo'
        );

        // Bind the repositories.
        $this->bindRepositories();
    }


    /**
     *
     */
    private function bindRepositories()
    {
        // Load config parameters needed.
        $repositoriesBasePath = config( 'lararepo.path' );
        $baseNamespace = rtrim( config( 'lararepo.namespace' ), '\\' ) . '\\';
        $implementation = config( 'lararepo.implementation' );
        $skipRepositories = config( 'lararepo.skip' );

        $allRepos = \File::files( $repositoriesBasePath );

        foreach( $allRepos as $repo )
        {
            $interface = basename( $repo );
            if ( in_array( $interface, $skipRepositories ) ) continue;
            else
            {
                $interfaceName = str_replace( '.php', '', $interface );
                $commonName = str_replace( 'Interface', '', $interfaceName );

                $interfaceFullClassName = $baseNamespace.$interfaceName;

                $implementationFullClassName = $baseNamespace.$implementation.'\\'.$commonName;

                if ( class_exists( $implementationFullClassName ) )
                {
                    // Bind the class.
                    $this->app->bind( $interfaceFullClassName, function ( $app ) use ( $implementationFullClassName )
                    {
                        return $app->make( $implementationFullClassName );
                    });
                }
            }
        }
    }


}