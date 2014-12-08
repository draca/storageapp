<?php

class UserController extends ApplicationController {

  public function show($id) {
    echo $_GET['id'];
  }

}

?>