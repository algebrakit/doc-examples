<!DOCTYPE html>
<?php
    $host      = 'http://algebrakit.eu';
    $appId     = '<your-app-id>';
    $appSecret = '<your-app-password>';
    $publishId = null;
    
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
    
    function generateExercise($exerciseId, $nr, $level) {
        global $courseId, $publishId;
        $data = array(
            'exerciseId' => $exerciseId,
            'courseId'   => $courseId,
            'publishId'  => $publishId,
            'nr'         => $nr,
            'level'      => $level
        );
        return akitPost('/exercise/generate/cms', $data);
    }
    
    function getExerciseInfo($exerciseId) {
        global $courseId, $publishId;
        $data = array(
            'exerciseId' => $exerciseId,
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
            $exInfo = getExerciseInfo('f3b7ea3a-240d-4de9-923a-c6d11f38c72d');
            echo print_r($exInfo, true);
            for($ii=0; $ii<$exInfo['numberOfLevels']; $ii++) {
                $lev = $ii+1;
                echo "<h3>Exercises of level $lev</h3>";
                $exArr = generateExercise('f3b7ea3a-240d-4de9-923a-c6d11f38c72d', 5, $ii);
                for($jj=0; $jj<count($exArr); $jj++) {
                    $elm = $exArr[$jj]; 
                    if($jj==0) {
                        $resp = getSessionId($elm->exerciseSpec);
                        $sessionId = $resp['sessionId'];
                        $appId = $resp['appId'];
                        echo "<akit-exercise session-id='$sessionId' app-id='$appId'></akit-exercise>";
                    } else {
                        $instruction = $elm->view->instruction[0]->content;
                        $assignment = $elm->view->assignment[0]->content;
                        echo "<div class='exercise'>";
                        echo "  <div class='instruction'>$instruction</div>";
                        echo "  <div class='assignment'>$assignment</div>";
                    }
                    echo "</div>";
                }
                
            }

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
        <?php echo "<script src='".$host."/akit-widgets.min.js'></script>";?>

        <script>
            //trigger Katex to render all latex formulas
            renderMathInElement(document.body, {
            delimiters: [
                {left: "$$", right: "$$", display: true},
                {left: "$", right: "$", display: false}
            ]});
            AlgebraKIT.injectWidgets();
        </script>
   </body>
</home>
