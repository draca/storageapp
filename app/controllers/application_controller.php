<?php

require 'lib/php-activerecord/ActiveRecord.php';

class ApplicationController {

  public function __construct() {
    
    # Setup database connection
    ActiveRecord\Config::initialize(function($cfg) {
      $cfg->set_model_directory('../app/models');
      $cfg->set_connections(
        array(
          'default' => 'mysql://'.database_default_username.''.(empty(database_default_password) ? '': ':'.database_default_password).'@'.database_default_hostname.'/'.database_default_database
        )
      );
      $cfg->set_default_connection('default');
    });
    
  }

}

?>