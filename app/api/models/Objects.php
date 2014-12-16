<?php

class Objects extends ActiveRecord\Model {
	static $table_name = 'objects';

static $belongs_to = array(
     array('locations', 'class_name' => 'Location', 'foreign_key' =>"location_id"),array('conditions', 'class_name' => 'Condition','foreign_key' =>"Condition_id"),array('types','class_name' => 'Type','foreign_key' =>"type_id")
   );


}
?>