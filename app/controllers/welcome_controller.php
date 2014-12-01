<?php

class WelcomeController extends ApplicationController {

  public function index() {
    
    # Get content from DB
    $pictures = Picture::find("all", array('conditions' =>  array("object_id" => $id), 'select' => 'id,mime,object_id'));
    $this->pictures = $pictures;

  }

  public function upload_image() {
    
  }

}

?>