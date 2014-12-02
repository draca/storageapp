<?php

class ObjectsController extends ApplicationController {

  public function index() {
    
    # Get content from DB
    $Objects = Objects::all();
    $this->Objects = $Objects;
    var_dump($Objects);

  }

	public function show() {
    	$Objects = Objects::find_by_pk($id);
    	$this->Objects = $Objects;
    	var_dump($Objects);
  }

	public function add() {
    
  }
	public function delete() {
    
  }


}

?>