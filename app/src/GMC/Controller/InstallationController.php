<?php
namespace GMC\Controller;

/*
|--------------------------------------------------------------------------
| Installation controller
|--------------------------------------------------------------------------
|
| Installation related logic
|
*/

class InstallationController extends BaseController {

  /**
   * Construct
   */
  public function __construct()
  {
  }

  /**
   * Check installation
   */
  public static function check()
  {
    /**
     * Database checks
     */

    \App::error(function(\PDOException $exception)
    {
      if($exception->getCode() == 1044 || $exception->getCode() == '3D000')
      {
        $title = 'Database error';
        $msg = 'Error connecting to database, please check <code>/local/app/config/' . \App::environment() . '/database.php</code>';
        $msg .= '<br><br>For more information about the installation, visit <a href="http://nowsquare.com/store-finder/documentation/v1">http://nowsquare.com/store-finder/documentation/v1</a>';
        $error = $exception->getMessage();

        return \Response::view('app.errors.general', compact('title', 'subtitle', 'msg', 'error'));
      }
      elseif($exception->getCode() == 23000)
      {
        $PDOException = $exception->getCode();
        return $exception->getCode();
      }
      else
      {
        echo $exception->getMessage();
      }
      die();
    });

    if(! \Schema::hasTable('users'))
    {
      // Database exists, no tables found
      InstallationController::migrate();
    }

    /**
     * Directory permissions
     */
    $dirs = array(
      '/storage/cache/',
      '/storage/logs/',
      '/storage/meta/',
      '/storage/meta/services.json',
      '/storage/sessions/',
      '/storage/uploads/',
      '/storage/views/',
      '/database/',
      '/database/production.sqlite'
    );

    $error = '';

    foreach($dirs as $dir)
    {
      $full_dir = app_path() . $dir;
      if(! \File::isWritable($full_dir))
      {
        if(strpos($full_dir, '../') !== false) $full_dir = str_replace('../', '', $full_dir);
        $error .= '' . $full_dir . ' is not writeable.<br>';
      }
    }

    if($error != '')
    {
      $title = 'Need permission';
      $msg = 'The files and / or directories below need write permission.';
      $msg .= '<br><br>For more information about the installation, visit <a href="https://nowsquare.com/google-maps-creator/documentation/v1/getting-started/installation</a>';

      echo \View::make('app.errors.general', compact('title', 'subtitle', 'msg', 'error'));
      die();
    }
  }

  /**
   * Install database and seed
   */
  public static function migrate()
  {
    //set_time_limit(0);

    \Artisan::call('migrate', ['--path' => "app/database/migrations", '--force' => true]);
    \Artisan::call('db:seed', ['--force' => true]);
  }

  /**
   * Remove all tables
   */
  public static function clean()
  {
    /**
     * Empty all user directories
     */
    $gitignore = '*
!.gitignore';

    $dirs = array(
      '/storage/cache/',
      '/storage/logs/',
      '/storage/sessions/',
      '/storage/uploads/',
      '/storage/views/'
    );

    foreach($dirs as $dir)
    {
      $full_dir = app_path() . $dir;
      $success = \File::deleteDirectory($full_dir, true);
      if($success)
      {
        // Deploy .gitignore
        \File::put($full_dir . '.gitignore', $gitignore);
      }
    }

    /**
     * Clear cache
     */
    \Artisan::call('cache:clear');

    /**
     * Drop all tables in database
     */
    $tables = [];
 
    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
 
    foreach(\DB::select('SHOW TABLES') as $k => $v)
    {
      $tables[] = array_values((array)$v)[0];
    }
 
    foreach($tables as $table)
    {
      \Schema::drop($table);
    }
  }

  public function getReset($key)
  {
    $demo_path = base_path() . '/../reset-demo';

    if($key == \Config::get('app.key') && \File::isDirectory($demo_path))
    {
      // Clean cache, database and files (MySQL only)
      //\GMC\Controller\InstallationController::clean();

      /**
       * Clear uploaded files
       */

      $full_dir = app_path() . '/storage/uploads';
      $success = \File::deleteDirectory($full_dir, true);
      if($success)
      {
        // Deploy .gitignore
        //\File::put($full_dir . '.gitignore', $gitignore);
      }

      // Database tables
      //\GMC\Controller\InstallationController::migrate();

      // Seed demo data
      $src = $demo_path . '/demo-database.sqlite';
      $target = base_path() . '/app/database/production.sqlite';

      \File::copy($src, $target);
    }
  }
}