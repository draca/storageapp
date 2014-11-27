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

                $token = make_session($post->username);

            }
            
            echo json_encode(array('status' => 'ok', 'token' => $token ));
        }
        else{
            throw new Exception("Login error");

        }


      } catch (Exception $e) {
        echo "Login error";
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




$app->post('/user/:token', function ($token) use ($app) {

    //var_dump();
    try {

        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 4){ throw new Exception("Access Denied!!");}
        $payload = json_decode($app->request()->getBody());

        $post = new User();
        $post->email    = $payload->email;
        $post->name    = "";
        $post->username = $payload->username;
        $post->salt = hash ( "sha256",get_random_salt(),false);
        $post->password = kda($payload->password , $post->salt);
        $post->access = $payload-> access;
        $post->save();
        $token = make_session($post->username);
        echo json_encode(array('status' => 'ok', 'token' => $token ));

    } catch (Exception $e) {
        echo $e->getMessage();
    }

});

// PUT route
$app->put('/user/:id/:token', function ($id,$token) use ($app)  {
    try {


        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 4){ throw new Exception("Access Denied!!");}
        $payload = json_decode($app->request()->getBody());


        $post = User::find_by_pk($id);
// oldPwd new Pwd

        if( $payload->newPwd != ""  )
        {
            if( $post->password == kda($payload->oldPwd, $post->salt) )
            {

                $post->password = kda($payload->newPwd , $post->salt);

            }else{   throw new Exception("Wrong Password"); }


        } 


        if($payload-> access != ""){$payload-> access = $payload->access; }
        if($payload->email != ""){ $post->email    = $payload->email;}

        $post->save();

        echo json_encode(array('status' => 'ok'));

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
$app->post('/objects/:token',function ($token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 1){ throw new Exception("Access Denied!!");}



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

$app->put('/objects/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}



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
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}


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
$app->post('/conditions/:token',function ($token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 1){ throw new Exception("Access Denied!!");}

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

$app->put('/conditions/:id/:token',function ($id, $token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}

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

$app->delete('/conditions/:id/:token',function ($id) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 3){ throw new Exception("Access Denied!!");}

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
$app->post('/attributes/:token',function ($token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 1){ throw new Exception("Access Denied!!");}


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

$app->put('/attributes/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}


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

$app->delete('/attributes/:id/:token',function ($id, $token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 3){ throw new Exception("Access Denied!!");}


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
$app->post('/locations/:token',function ($token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}

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

$app->put('/locations/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}


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

$app->delete('/locations/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 3){ throw new Exception("Access Denied!!");}


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
$app->post('/type/:token',function ($token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
                $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 1){ throw new Exception("Access Denied!!");}

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

$app->put('/type/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 2){ throw new Exception("Access Denied!!");}
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

$app->delete('/type/:id/:token',function ($id,$token) use ($app) {
   try { 

        $payload = json_decode($app->request()->getBody());
        $session = is_session_active($token);
        $user = User::find_by_pk($session->username);
        if($user -> access < 3){ throw new Exception("Access Denied!!");}
        $object=Location::all('all', array('conditions' => array('id' => $id)));
        $object -> delete();

        echo json_encode(array('status' => 'ok'));

    } catch (Exception $e) {

        echo $e->getMessage();
    }
});

$app->get('/image/:id',function($id) use ($app) {
$res = $app->response();
$res->header('Content-Type', 'image/jpg');


try{

 $pic = Picture::find_by_pk($id);
 $res = $res->body($pic -> data);

   } catch (Exception $e) {

        $res->header('Content-Type', 'text/html');
        echo $e->getMessage();
    }


});

$app->get('/object/:id/imagelist',function($id) use ($app) 
{
    try{
        $object = Picture::find("all",array('conditions' =>  array("object_id" => $id),'select' => 'id,mime,object_id'  )  ) ;
        echo arToJson( $object ) ;

    } catch (Exception $e) {

        
        echo $e->getMessage();
    }
});




$app->post('/image/:id',function() use ($app) {


$payload = json_decode($app->request()->getBody());
$session = is_session_active($token);
$user = User::find_by_pk($session->username);
if($user -> access < 2){ throw new Exception("Access Denied!!");}

try{


$uploadOk = 1;
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        
        $uploadOk = 1;
    } else {
        throw new Exception ( "File is not an image.");
        $uploadOk = 0;
    }
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    throw new Exception ( "Sorry, your file is too large.");
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    throw new Exception ( "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    throw new Exception ("Sorry, your file was not uploaded.");
// if everything is ok, try to upload file
} else {
$pic= fopen ( $_FILES["fileToUpload"]["tmp_name"], "r");
$temp = fread($pic,$_FILES["fileToUpload"]["size"]);
$picture = new Picture();
$picture -> object_id = $id;
$picture -> mime = $check["mime"];
$picture -> data = $temp;
$picture -> save();

fclose ($pic);
unlink($_FILES["fileToUpload"]["tmp_name"]);

echo json_encode(array('status' => 'ok'));



}

}catch (Exception $e){ echo $e->getMessage(); }


});



/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();