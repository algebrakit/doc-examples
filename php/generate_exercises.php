<!DOCTYPE html>
<!--
This is a demo of how to run multiple randomized exercises from AlgebraKiT's CMS.
For optimal performance the sessions for all exercises are obtained with one
call to AlgebraKiT. The result contains the html to include the interaction into the webpage (with inlined initialization data)
-->
<?php
    $host       = 'https://algebrakit.eu';
    $apiKey     = '...';  //The API key that you created in the management console
    $apiVersion = 2; //1 = original/deprecated version
    
    $exlist =  Array(
            0 => Array(
                    'exerciseId' => "...", //"<exercise id can be found in the cms (edit mode)>",
                    'version' => "latest", //Exercise version, can be either a version number or the string "latest". "latest" here means the version that as saved last in the cms
                    'nr' => 2,     //for randomized exercises: number of instances to generate
                    'level' => 0,  //applicable to multilevel exercises (exercise arrangements)
                    'attributes' => array('start-active'=>true)
                ),

            1 => Array
                (
                    'exerciseId' => "...",//"<exercise id can be found in the cms (edit mode)>",
                    'version' => 1
                ),
        );

function akitPost($endpoint, $data) {
        global $apiKey, $host;
        $url  = $host.$endpoint;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json", "x-api-key: $apiKey"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $json_response = curl_exec($curl);
        return json_decode($json_response);
    }

    function getSessions() {
        global $publishId, $exlist, $apiVersion;
        $data = array(
            'exercises' => $exlist,
            'api-version' => $apiVersion
        );
        return akitPost('/session/create', $data);
    }
    
    $sessionData = getSessions();
?>


<html>
   <head>
        <style>
            .exercise {
                margin:10px;
            }
        </style>
   </head>
   <body>
        <?php
            //for each exericse...
            if(is_object($sessionData) && !$sessionData->success) {
                echo "Failed to generate sessions";
            } else {
                for ($ii = 0; $ii < count($sessionData); $ii++) {
                    $ex = $sessionData[$ii];
                    if($ex->success) {
                        //for each of the requested nr of instances...
                        for($nn=0; $nn < count($ex->sessions); $nn++) {
                            // insert a tag for this interaction.
                            // use the html returned with the session data for better performance
                            // (initialization data is inlined)
                            echo $ex->sessions[$nn]->html;
                            echo '<br><br>';
                        }
                    } else {
                        echo $ex->msg;
                    }
                } 
            }
        ?>
       
        <!-- formula editor requires jquery -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <!--- Global object AlgebraKIT will be the front end API of AlgebraKiT and is used for configuration -->
        <script>
            AlgebraKIT = {
                config: {
                    theme: 'akit'  //algebrakit theme is default
                }
            };
        </script>
        <!-- this script adds AlgebraKiT's API to global object AlgebraKIT -->
        <script src="https://widgets.algebrakit.eu/akit-widgets.min.js"></script>
        <script>
             AlgebraKIT.injectWidgets().then(function(warr) {
                 //after creation of the widgets, you can add listeners to learning events.
                 var w = warr[0];
                 console.log(w.id);
                 AlgebraKIT.addListener(
                        w.id,
                        'exercise-hint',
                        function() {
                            console.log('hint requested')
                            console.log(arguments);
                        });
             });
        </script>
   </body>
</html>