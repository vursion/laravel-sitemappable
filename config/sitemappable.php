<?php

return [

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Sitemappable model shipped with this package.
     */
    'db_table_name' => 'sitemap',

    /*
     * The generated XML sitemap is cached to speed up performance.
     */
    'cache' => '60 minutes',

    /*
     * The batch import will loop through this directory and search for models
     * that use the IsSitemappable trait.
     */
    'model_directory' => 'app/Models',

    /*
     * If you're extending the controller, you'll need to specify the new location here.
     */
    'controller' => Vursion\LaravelSitemappable\Http\Controllers\SitemappableController::class,

];
