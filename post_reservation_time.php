<?php
    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
 
    echo '<Response>';
 
    $CallSid = $_REQUEST['CallSid'];
    $CallerNumber = $_REQUEST['From'];
    # @start snippet
    $user_pushed = (int) $_REQUEST['Digits'];
    # @end snippet
 
    // Tempへの選択情報の書き込み
    $file = fopen( sprintf("%s.txt",$CallSid), "a" );
    fwrite( $file, sprintf("%d,",$user_pushed) );
    fclose( $file );


    // TODO: 入力内容のチェック
    if(1!=1) { // 正しくない選択肢だった場合もう一度入力に戻す。
        echo '<Redirect method="GET">input_reservation_time.xml</Redirect>';
    } else {   // チェックと通ったら次の入力へ
        echo '<Redirect method="GET">input_population.xml</Redirect>';
    } 
    echo '</Response>';
?>