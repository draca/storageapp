<?php

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


?>