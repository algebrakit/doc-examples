<!DOCTYPE html>
<!--
This is a demo of how to run a randomized exercise from AlgebraKiT's CMS.
- Server side (php):
   * the number of levels for the exercise is obtained via  /exercise-info
   * a session is created for the exercise using the highest level
   * the resulting session id is used to set the attribute of <akit-exercise>
- Client side (javascript)
   * the widget is created by AlgebraKIT.injectWidgets()
-->
<?php
    $appId     =  '<your-app-id>';
    $appSecret =  '<your-app-password';
    $publishId =  '<publish-id-or-null>';
    $courseId  =  '<id-of-course-containing-the-exercise>';
    $exerciseId = '<id-of-the-randomized-exercise>';
    
    function akitPost($endpoint, $data) {
        global $appId, $appSecret;
        $url  = 'https://algebrakit.eu'.$endpoint;
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
    function getSessionId($exId, $level) {
        global $courseId, $publishId;
        $data = array(
            'exerciseId' => $exId,
            'courseId'   => $courseId,
            'publishId'  => $publishId,
            'level'      => $level
        );
        return akitPost('/session/create/cms', $data);
    }
    
    function getExerciseInfo($exId) {
        global $courseId, $publishId;
        $data = array(
            'exerciseId' => $exId,
            'courseId'   => $courseId,
            'publishId'  => $publishId
        );
        return akitPost('/exercise-info', $data);
    }
?>


<home>
   <head>
        <link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.css"/>
        <link rel='stylesheet' href="http://algebrakit.eu/widgets/exercise/akit-exercise.css"/>
        <style>
            .exercise {
                margin:10px;
            }
        </style>
   </head>
   <body>
        <?php
            $exInfo = getExerciseInfo($exerciseId);
            echo print_r($resp, true);
            $lev = $exInfo['numberOfLevels'];
            echo "<h3>Exercises of level $lev</h3>";
            $sessionId = $resp['sessionId'];
            echo "<akit-exercise session-id='$sessionId' app-id='$appId' start-active></akit-exercise>";
        ?>
       
        <!-- formula editor requires jquery -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <!-- katex takes care of displaying math formula in latex -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/contrib/auto-render.min.js"></script>
        <!--- exposes AlgebraKIT api -->
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
        <script src='https://algebrakit.eu/akit-widgets.min.js'></script>
        <script>
             AlgebraKIT.injectWidgets();
        </script>
   </body>
</home>
