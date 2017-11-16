<?php 

    // if the code parameter has been sent, we retrieve the access_token

    //if($_GET['code']) {

        $code = "cb90b7259c204006a5b126c180ad908d";

        $url = "https://api.instagram.com/oauth/access_token";

        $access_token_parameters = array(

                'client_id'                =>     'f109d9f51beb4d65aec4e103ab96fc1a',

                'client_secret'            =>     'c6471c6f168f40d09035d4b88ccef186',

                'grant_type'               =>     'authorization_code',

                'redirect_uri'             =>     'http://www.glimnet.se/cms/pages.php?id=valkommen',

                'code'                     =>     $code

        );

        $curl = curl_init($url);    // we init curl by passing the url

        curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request

        curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer's certificate.

        $result = curl_exec($curl);   // to perform the curl session

        curl_close($curl);   // to close the curl session



        $arr = json_decode($result,true);

        echo $arr['access_token'];   // display the access_token

        echo $arr['user']['username'];   // display the username



    //}



?>