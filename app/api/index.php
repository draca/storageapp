<?php



/*************************************************************************************************************************************/

//App varibles

/*************************************************************************************************************************************/


error_reporting(0);


require 'Slim/Slim.php';
require 'ActiveRecord.php';




 ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory('models');
    $cfg->set_connections(array(
         'development' => 'mysql://root@127.0.0.1/forshaga_storage'
        //'development' => 'mysql://m4314_riman:siberian@localhost/m43147_db'
    ));
 });




\Slim\Slim::registerAutoloader();



$app = new \Slim\Slim();

$app->contentType( 'application/json' );




/*************************************************************************************************************************************/

//Support functions

/*************************************************************************************************************************************/

function make_session($username)
{

    try
    {
        $session = Session::find_by_pk($username);
    } catch(Exception $e)
        {
           if( $session == null )
           {

                $session = new Session();
                $session-> token = hash ( "sha256" ,get_random_salt(),false);
                $session-> user = $username;

                $session->save();

            }
        }
        return $session -> token;
}

function kda($password , $salt)
{
    $temp = $password;
    for($i=0; $i<200; $i++)
    {
        $temp = hash ( "sha256" , $temp + $salt , false  );
    }

    return $temp;
}
function is_session_active($token)
{

    $session = Session::find("any",array('conditions' =>  array("token" => $token))  ) ;
    if(null!=$session)
    {
         $username = $session -> user;
        

        $user = User::find("any",array('conditions' =>  array("username" => $username),'select' => 'username,email,name,access'  )  ) ;

        return $user;

    }

    return "";
}

function get_random_salt()
{
    $valid_chars ="qwertyuiopasdfghjklzxcvbnm,.-1234567890+!#¤%&/()=?QWERTYUIOPASDFGHJKL*>ZXCVBNM;:_";
    $length=100;
    // start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}



function arToJson( $data, $options = null ) {
    $out = "[";

    if (is_array($data) )
    {

    foreach( $data as $row) {
        if ($options != null) {
            $out .= $row->to_json($options);
        }else {
            $out .= $row->to_json(); 
            $out .= ",";
        }
    }
    $out = rtrim($out, ',');
    $out .= "]";
    return $out;

    }else {return $data->to_json();}
}


/*************************************************************************************************************************************/

//User functions

/*************************************************************************************************************************************/


$app->get('/user', function () {

    try {
        echo arToJson( User::all(array( 'select' => 'username,email,name,access'   ) ) );
    } catch (Exception $e) {
        echo $e->getMessage();
    }

});


$app->get('/user/search/:query', function( $query ) {

    try {
        echo arToJson( User::all( 'first', array( 'conditions' => array( 'username' => $query ),'select' => 'username,email,name,access'  ) ) );
    } catch (Exception $e) {
        echo $e->getMessage();
    }

});

$app->get('/user/token/:query', function( $query ) {

    try {
            $session = is_session_active($query);
        echo  arToJson($session) ;
    } catch (Exception $e) {
        echo $e->getMessage();
    }

});


$app->post('/user/authenticate', function () use ($app) {

//var_dump();
    try {
        $payload = json_decode($app->request()->getBody());



        $post = User::find_by_pk($payload->username);



        if($post->password == kda($payload->password, $post->salt))
        {
            try {
                $session = Session::find_by_pk($post->username);
            } catch(Exception $e) {

            $session = new Session();
            $session-> token = hash ( "sha256" ,get_random_salt(),false);
            $session-> user = $post->username;

            $session->save();

            }
            $token = make_session($post->username);
            echo json_encode(array('status' => 'ok', 'token' => $token ));
        }
        else{
            throw new Exception("Login error");

        }


      } catch (Exception $e) {
        echo $e->getMessage();
    }

});


$app->delete('/user/authenticate', function () use ($app){


    try{
        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($payload->token);

        if($session -> username == ""){ throw new Exception("Not found");}

        $post = Session::find_by_pk($session->username);
        $post->delete();
        echo json_encode(array('status' => 'ok'));
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }


});




$app->post('/user', function () use ($app) {

    //var_dump();
    try {
        $payload = json_decode($app->request()->getBody());

        $post = new User();
        $post->email    = $payload->email;
        $post->name    = "";
        $post->username = $payload->username;
        $post->salt = hash ( "sha256",get_random_salt(),false);
        $post->password = kda($payload->password , $post->salt);
        $post->access = 1;
        $post->save();
        $token = make_session($post->username);
        echo json_encode(array('status' => 'ok', 'token' => $token ));

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});




// PUT route
$app->put('/user/:token', function ($token) use ($app)  {
    try {


        $session = is_session_active($token);
        $payload = json_decode($app->request()->getBody());


        $post = User::find_by_pk($session->username);
// oldPwd new Pwd

        if( $payload->newPwd != ""  )
        {
            if( $post->password == kda($payload->oldPwd, $post->salt) )
            {

                $post->password = kda($payload->newPwd , $post->salt);

            }else{   throw new Exception("Wrong Password"); }


        } 



        if($payload->email != ""){ $post->email    = $payload->email;}
        $post->twitter = $payload->twitter;
        $post->facebook = $payload->facebook;

        $post->save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

// DELETE route
$app->delete('/user/:token', function ($token) use ($app) {
   try {

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $post = User::find_by_pk($session->username);
        User::delete($post);

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


/*************************************************************************************************************************************/

//Storage app functions

/*************************************************************************************************************************************/


// Object list routes

$app->get('/objects/', function () {

    try {
     
        echo arToJson( Objects::all() );

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});
$app->post('/objects/',function () use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);

        $object = new Objects($payload);
        $object -> save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


// Object routes

$app->get('/objects/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Objects::all('all', array('conditions' => array('id' => $id)));

       echo arToJson( $object ) ;

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->put('/objects/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Objects::all('first', array('conditions' => array('id' => $id)));
        $object = $object[0];

        foreach ($payload as $k => $v) 
        {
            $object -> $k = $v;
            echo "\$a[$k] => $v.\n";
        }
       $object -> save();
        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->delete('/objects/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Objects::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

// Condition list routes

$app->get('/conditions/', function () {

    try {
     
        echo arToJson( Conditions::all() );

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});
$app->post('/conditions/',function () use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);

        $object = new Conditions($payload);
        $object -> save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


// Condition routes

$app->get('/conditions/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Conditions::all('all', array('conditions' => array('id' => $id)));

       echo arToJson( $object ) ;

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->put('/conditions/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Conditions::all('first', array('conditions' => array('id' => $id)));
        $object = $object[0];

        foreach ($payload as $k => $v) 
        {
            $object -> $k = $v;
            echo "\$a[$k] => $v.\n";
        }
       $object -> save();
        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->delete('/conditions/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Condition::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

// Attributes list routes

$app->get('/attributes/object/:id', function ($id) {

    try {
     
        echo arToJson( Condition::all('all', array('conditions' => array('object_id' => $id))) );

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});
$app->post('/attributes/',function () use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);

        $object = new Attribute($payload);
        $object -> save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


// attribute routes

$app->get('/attributes/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Attribute::all('all', array('conditions' => array('id' => $id)));

       echo arToJson( $object ) ;

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->put('/attributes/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Attribute::all('first', array('conditions' => array('id' => $id)));
        $object = $object[0];

        foreach ($payload as $k => $v) 
        {
            $object -> $k = $v;
            echo "\$a[$k] => $v.\n";
        }
       $object -> save();
        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->delete('/attributes/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Attribute::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

// Location list routes

$app->get('/locations/', function () {

    try {
     
        echo arToJson( Location::all() );

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});
$app->post('/locations/',function () use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);

        $object = new Location($payload);
        $object -> save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


// Location routes

$app->get('/locations/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('all', array('conditions' => array('id' => $id)));

       echo arToJson( $object ) ;

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->put('/locations/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('first', array('conditions' => array('id' => $id)));
        $object = $object[0];

        foreach ($payload as $k => $v) 
        {
            $object -> $k = $v;
            echo "\$a[$k] => $v.\n";
        }
       $object -> save();
        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->delete('/locations/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});
// Type list routes

$app->get('/type/', function ($id) {

    try {
     
        echo arToJson( Location::all( ));

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});
$app->post('/type/',function () use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);

        $object = new Location($payload);
        $object -> save();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


// Location routes

$app->get('/type/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('all', array('conditions' => array('id' => $id)));

       echo arToJson( $object ) ;

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->put('/type/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('first', array('conditions' => array('id' => $id)));
        $object = $object[0];

        foreach ($payload as $k => $v) 
        {
            $object -> $k = $v;
            echo "\$a[$k] => $v.\n";
        }
       $object -> save();
        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->delete('/type/:id',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $object=Location::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});


$app->post('/image',function() use ($app) {

$payload = json_decode($app->request()->getBody());


if (!isset($_FILES['uploads'])) {
        echo "No files uploaded!!";
        return;
    }
    $imgs = array();

    $files = $_FILES['uploads'];
    $cnt = count($files['name']);

    for($i = 0 ; $i < $cnt ; $i++) {
       
        

            $name = uniqid('img-'.date('Ymd').'-');
            echo is_writable('/uploads/' . $name) == false;
            if( move_uploaded_file($files['tmp_name'][$i], '/uploads/' . $name) ==true){
                $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);}
            

        
    }

    $imageCount = count($imgs);

    if ($imageCount == 0) {
       echo json_encode(array('status' => 'No files uploaded!!'));
       return;
    }
    echo json_encode(array('status' => 'ok'));



});



/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();