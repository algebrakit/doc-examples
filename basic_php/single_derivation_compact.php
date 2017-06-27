<!DOCTYPE html>
<?php
    $host      = 'http://algebrakit.eu';
    $appId     = '<your-app-id>';
    $appSecret = '<your-app-password>';

    function akitPost($endpoint, $data) {
        global $host, $appId, $appSecret;
        $url  = $host.$endpoint;
        $data['appId']     = $appId;
        $data['appSecret'] = $appSecret;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $json_response = curl_exec($curl);
        return (array)json_decode($json_response);
    }
    
    //makes the call to AlgebraKiT's webservice to retrieve a session id from
    //an exercise specification
    function getSessionId($exerciseSpec) {
        $data = array(
            'exerciseSpec' => $exerciseSpec,
            'appId' => $appId,
            'appSecret' => $appSecret
        );
        return akitPost('/session/create/spec', $data);
    }
    
    
    $exerciseSpec =  array(
        'type'  => 'ALGEBRA',                   //this is an algebra exercise
        'solve' => 'Solve[2x^2+5x+15=3-5x,x]',  //the expression to be solved
        'palette' => 'equations',               //indicates what buttons need to be present in the formula editor
        'audience' => 'uk_KS3'                  //id of student profile: language, solution strategy
    );
    $resp = getSessionId($exerciseSpec);
    $sessionId = $resp['sessionId'];
    $appId = $resp['appId'];
?>


<home>
   <head>
        <link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.css"/>
   </head>
   <body>
        <?php
            echo "<akit-derivation-compact session-id='$sessionId' app-id='$appId''></akit-derivation-compact>";
        ?>
       
        <!-- katex takes care of displaying math formula in latex -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/contrib/auto-render.min.js"></script>
        <!--- configure and load AlgebraKIT api -->
        <script>
            AlgebraKIT = {
                config: {
                    widgets : ['akit-derivation-compact']
                }
            };
        </script>
        <?php echo "<script src='".$host."/akit-widgets.min.js'></script>";?>
        <script>
            AlgebraKIT.injectWidgets();
        </script>
   </body>
</home>
