
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; Path=/;' " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}


function login(form)
{
 var pw = form.password.value;
 var username = form.username.value;
   
    var data = ' {"username": "'+ username +'" , "password": "'+pw +'" }'; 
       $.ajax({
            type: "POST",
            url: "/api/user/authenticate",
            data:  data,
            dataType: "JSON",
            contentType: "JSON",
            timeout: (2 * 1000),
            success: function(msg) {
               

                            setCookie("token",msg.token,365);
                            alert("Du är nu inloggad");
                            window.location.replace("/");
                           

                          },
            error: function(xhr, ajaxOptions, thrownError) { alert("Inloggningen misslyckades! "+ ajaxOptions+xhr.responseText);}

        });



   return false;
}


function jsonify(object)
{
    var json = "{"; 
    $.each(object, function(key, value){
    json += "\""+ key + "\": \"" + object[key]+ "\",";

});

json=json.substring(0,json.lastIndexOf(",")) ;
    
json += "}";

return json;

}




function sendData(data,url,type, redirect)
{
    var urlvars = getUrlVars();
   

       $.ajax({
            type: type,
            url: url,
            data:  data,
            dataType: "JSON",
            contentType: "JSON",
            success: function(msg) {
                            alert("Oprationen lyckades!");
                            if(redirect != "")
                            {
                                
                                window.location.replace(redirect);
                            }
                                else{
                                window.location.replace(url.substring(url.lastIndexOf("/"),url.length) );
                            }
                          },
            error: function(xhr, ajaxOptions, thrownError) { alert("Ett fel har inträffat! Har du loggat in?");}

        });





}






function getUrlVars() {
    var map = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        map[key] = value;
    });
    return map;
}
function addObject(form)
{
    var urlvars = getUrlVars();
    object = {};
    object["discription"] = form.discription.value;
    object["type_id"] = form.type.value;
    object["sum"] = form.sum.value;
    object["assembly"] = form.assembly.value;
    object["instorage"] = form.sum.value;
    if (!isNaN(form.instorage.value))
    {
           object["instorage"] = form.instorage.value;
    }
    object["location_id"] = form.location.value;
    object["condition_id"] = form.condition.value;
    console.log(jsonify(object));

if(!isNaN(urlvars['id']))
{ 
    sendData(jsonify(object), "/api/objects/"+urlvars['id'], "PUT", "/details.html?id="+urlvars['id']);
}
else
{
    sendData(jsonify(object), "/api/objects/", "POST","");
}

return false;

}


  function deleteObject(id,type)
  {
    var r = confirm("Vill du ta bort?");
if (r == true) {
    

 $.ajax({
            type: 'DELETE',
            url: "/api/"+ type +"/"+id,
            
            success: function(msg) {
                            alert(type + " med referens "+ id + " togs bort"); location.reload();
 

                          },
            error: function(xhr, ajaxOptions, thrownError) { alert(type + " med referens "+id + " gick inte att ta bort"); }
            
        });
    return false;
    

} else {
    
}
    
    return false;

  }

function addLocation(form)
{
      object = {};
    object["name"] = form.name.value;
    object["city"] = form.city.value;
    object["adress"] = form.adress.value;
    object["postal"] = form.postal.value;
    console.log(jsonify(object));
 sendData(jsonify(object), "/api/locations", "POST","");

 return false;


}
function addType(form)
{
      object = {};
    object["name"] = form.name.value;
    console.log(jsonify(object));
 sendData(jsonify(object), "/api/types", "POST","");

 return false;


}
function addCondition(form)
{
      object = {};
    object["name"] = form.name.value;
    object["value"] = form.svalue.value;
    object["discription"] = form.discription.value;
    console.log(jsonify(object));
 sendData(jsonify(object), "/api/conditions", "POST","");

 return false;


}




function logout()
{ 
    

    $.ajax({
            type: 'DELETE',
            url: "/api/user/authenticate",
            dataType: "JSON",
            contentType: "JSON",
            success: function(msg) {
                            alert("Du är nu utloggad");
                            setCookie("token", "", -1);
                            location.reload();
                          },
            
            error: function(xhr, ajaxOptions, thrownError) { alert("Något gick fel"); setCookie("token", "", -1); location.reload();}
        });


    return false;
    

}

function addUser(form)
{
    var urlvar = getUrlVars()


    object = {};
    object["name"] = form.name.value;
    object["username"] = form.username.value;
    object["email"] = form.email.value;
    object["password"] = form.pw.value;
    object["access"] = form.access.value;

    if(form.pw.value == "" &&  urlvar['id']== "" ){ alert("Ditt lösenord kan ite vara blankt"); }
    else if (form.pw.value != "" && urlvar['id']!= "" ){sendData(jsonify(object), "/api/users", "POST","");}
    else if (urlvar['id'] !="" ){sendData(jsonify(object), "/api/user/"+urlvar['id'], "PUT","/users/index.html");}
    console.log(jsonify(object));
    

 return false;

}



function deletePicture(id)
{



         $.ajax({
            type: "DELETE",
            url: "/api/image/"+id,
            dataType: "JSON",
            contentType: "JSON",
            success: function(msg) {
                            alert("Oprationen lyckades!");
                            
                            location.reload();
                          },
            error: function(xhr, ajaxOptions, thrownError) { alert("Ett fel har inträffat! Har du loggat in?");}

        });



        return false;
}




function uploadImage(form)
{
    var urlvars = getUrlVars();
    var id = urlvars['id']
    var file_data = $('#fileToUpload').prop('files')[0];   
    var form_data = new FormData(form);                  
    form_data.append('file', file_data)                           
    $.ajax({
                url: "/api/image/"+id, // point to server-side PHP script 
                dataType: 'text',  // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                dataType: "JSON",                        
                type: 'post',
                success: function(msg) {
                            alert("Oprationen lyckades!");
                            
                            location.reload();
                          },
            error: function(xhr, ajaxOptions, thrownError) { alert("Ett fel har inträffat! Har du loggat in?");}
     });

return false
}