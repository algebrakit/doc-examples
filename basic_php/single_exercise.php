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
            'exerciseSpec' => $exerciseSpec
        );

        return akitPost('/session/create/spec', $data);
    }
    
    $exerciseSpec =  array(
        'type'  => 'ALGEBRA',                        //this is an algebra exercise
        'instruction' => 'Find the derivation',
        'assignment' => 'Diff[ x(2-x), x]',  //the expression to be solved
        'palette' => 'equations',                    //indicates what buttons need to be present in the formula editor
        'audience' => 'uk_KS5'                       //id of student profile: language, solution strategy
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
            echo "<akit-exercise session-id='$sessionId' app-id='$appId' start-active='true'></akit-exercise>";
        ?>
       
        <!-- formula editor requires jquery -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <!-- katex takes care of displaying math formula in latex -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/contrib/auto-render.min.js"></script>
        <!--- configure and load AlgebraKIT api -->
        <script>
            AlgebraKIT = {
                config: {
                    widgets : [{
                            name:        'akit-exercise',
                            handwriting: 'myscript'
                        }]
                }
            };
        </script>
        <?php echo "<script src='".$host."/akit-widgets.min.js'></script>";?>
        <script>
            AlgebraKIT.injectWidgets();
        </script>
   </body>
</home>
