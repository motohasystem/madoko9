<?php
    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
 
    echo '<Response>';
    $CallSid = $_REQUEST['CallSid'];
    $CallerNumber = $_REQUEST['From'];
 
    // TODO: Tempからの情報の読み出し
    $file = fopen( sprintf("%s.txt",$CallSid), "r" );
    $input_data = fgets($file);
    fclose( $file );
    $data_array = split(",",$input_data);

    echo '<Say language="ja-JP">ご入力いただいた内容を確認させてください。</Say>';
    echo '<Say language="ja-JP">現在位置は</Say>';
    print(sprintf('<Say language="ja-JP">%d</Say>',$data_array[0]));
    echo '<Say language="ja-JP">です。</Say>';

    echo '<Say language="ja-JP">目的地は</Say>';
    print(sprintf('<Say language="ja-JP">%d</Say>',$data_array[1]));
    echo '<Say language="ja-JP">です。</Say>';

    echo '<Say language="ja-JP">配車希望時間は</Say>';
    if($data_array[2]=="1") {
        echo '<Say language="ja-JP">15分後</Say>';
    }else
    if($data_array[2]=="2") {
        echo '<Say language="ja-JP">30分後</Say>';
    }else
    if($data_array[2]=="3") {
        echo '<Say language="ja-JP">1時間後</Say>';
    }

    echo '<Say language="ja-JP">です。</Say>';

    echo '<Say language="ja-JP">乗車人数は</Say>';
    print(sprintf('<Say language="ja-JP">%d人</Say>',$data_array[3]));
    echo '<Say language="ja-JP">です。</Say>';

    echo '<Redirect method="GET">checkin.xml</Redirect>';

    echo '</Response>';
?>