<?php
    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
 
    echo '<Response>';
 
    $CallSid = $_REQUEST['CallSid'];
    $CallerNumber = $_REQUEST['From'];
    # @start snippet
    $user_pushed = (int) $_REQUEST['Digits'];
    # @end snippet
 
    if ($user_pushed == 1)
    {
        echo '<Say language="ja-JP">このサービスは電話で配車の手配を行います。</Say>';
    }
    else if ($user_pushed == 2){
    }
    // 選択画面に進む
    echo '<Redirect method="GET">/select_start_point.xml</Redirect>';
 
    echo '</Response>';
?>