function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
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


function login(username, pw)
{
   
    var temp;
    var data = ' { "username": "'+ username +'" , "password": "'+pw +'" }'; 

       $.ajax({
            type: "POST",
            url: "/api/user/authenticate",
            data:  data,
            dataType: "json",
            success: function(msg) {
               
                            temp = msg;
                           
                            setCookie("token",msg.token,365);
                            token = msg.token;

                          },
            error: function(xhr, ajaxOptions, thrownError) { alert("fel!");}

        });



    
}