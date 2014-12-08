<?php

class ObjectsController extends ApplicationController {

  public function index() {
    
    # Get content from DB
    $objects = Objects::all();
    $this->objects = $objects;

  }

  public function show() {
    $objects = Objects::find("all", array('conditions' =>  array("id" => $id)));
    $this->$objects = $objects;
  }
  public function add() {
    $_COOKIE[$cookie_name];


  }
  public function delete() {
    
  }
  public function update() {
    
  }



}

?>