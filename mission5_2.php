<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>mission_5-1</title>
	</head>

<body>

<?php
    #trueなら編集、falseなら投稿
    $edit_Flag = false;

	$edit_name = null;
    $edit_comment = null;
    $edit_number = null;
    $edit_password = null;

#データベースへの接続
    $dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
#テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS mission_5_re"
	. " ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "day TEXT,"
    . "pass char(18)"
	. ");";
    $stmt = $pdo->query($sql);

#編集
    if(isset($_POST['edit'])){
        $edit_Flag = True;
        $confirm_pass = null;
        $input_pass = $_POST["editpass"];
        $id = $_POST["edit"];

        $sql = 'SELECT * FROM mission_5_re';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($id == $row['id']){
                $confirm_pass = $row['pass'];
            }
        }

        if(($confirm_pass == "") or (($input_pass == "") and ($confirm_pass == ""))){
            echo "パスワードが投稿の際に登録されていないため、編集できません。"."<br />";
        } elseif($confirm_pass == $input_pass) {
            $sql = 'SELECT * FROM mission_5_re';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($_POST['edit'] == $row['id']){
                    $edit_number = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                    $edit_password = $row['pass'];
                }
            }
        } else {
            echo "パスワードが違います"."<br />";
        }
    }
    
    

?>

<!-- 投稿フォーム -->
<!-- edit_Flag=trueならedit_がつく。falseならただのname -->
<!-- 編集される名前コメントを反映させる -->
    <form action = "<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
        <input type = "text" name="name" placeholder="名前" value = "<?php echo $edit_name; ?>" /><br>
        <input type = "text" name="comment" placeholder="コメント" value = "<?php echo $edit_comment; ?>" /><br>
        <input type = "hidden" name="edit_hidden_number" value="<?php echo $edit_number; ?>" />
        <input type = "hidden" name="edit_hidden_password" value="<?php echo $edit_password; ?>" />
        <input type = "password" name="pass" placeholder="パスワード" /><br>
	    <input type = "submit" name = "<?php if($edit_Flag){echo "edit_submit"; }else{echo "new_submit"; }?>" value="送信"/><br>
    </form>
<!-- 削除番号指定フォーム -->
	<form action="<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
		 <input type="text" name="delete" placeholder="削除対象番号" /><br>
         <input type="password" name="delpass" placeholder="パスワード" /><br>
		 <input type="submit" value="削除"　/><br>
	</form>
<!-- 編集番号指定フォーム -->
    <form action="<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
      <input type="text" name="edit" placeholder="編集対象番号"/><br>
      <input type="password" name="editpass" placeholder="パスワード" /><br>
      <input type="submit" value="編集" />
    </form>



<?php
    #新規投稿 insertでデータ入力
    if(isset($_POST['new_submit'])){
        if(isset($_POST['name']) and isset($_POST['comment'])){
            $sql = $pdo -> prepare("INSERT INTO mission_5_re (name, comment, day, pass) VALUES (:name, :comment, :day, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':day', $day, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $day = (string)(date("Y年m月d日 H:i:s"));
            $pass = $_POST["pass"];
            $sql -> execute();
        } else {
            echo "正しく入力してください。"."<br />";
        }
    }

    #削除部分　deleteで削除
    if(isset($_POST["delete"]) && isset($_POST["delpass"])){
        $confirm_pass = null;
        $input_pass = $_POST["delpass"];
        $id = $_POST["delete"];

        $sql = 'SELECT * FROM mission_5_re';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($id == $row['id']){
                $confirm_pass = $row['pass'];
            }
        }

        if(($confirm_pass == "") or (($input_pass == "") and ($confirm_pass == ""))){
            echo "パスワードが投稿の際に登録されていないため、削除できません。"."<br />";
        } elseif($confirm_pass == $input_pass) {
            $sql = 'delete from mission_5_re where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo "パスワードが違います"."<br />";
        }
    }

    #編集部分　
    //編集するデータの取得
    if(isset($_POST["edit_submit"])){
        //updateで編集
        //編集された名前とコメントが送信されたとき
        //配列、ループ処理を使う？
        //
        $confirm_pass = null;
        $input_pass = $_POST["pass"];
        $id = $_POST["edit_hidden_number"]; //変更する投稿番号
        $password = $_POST["edit_hidden_password"];
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $day = date("Y月m月d日 H:i:s");

        $sql = 'SELECT * FROM mission_5_re';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($id == $row['id']){
                $confirm_pass = $row['pass'];
            }
        }

        if(($confirm_pass == "") or (($input_pass == "") and ($confirm_pass == ""))){
            echo "パスワードが投稿の際に登録されていないため、削除できません。"."<br />";
        } elseif($confirm_pass == $input_pass) {
            $sql = 'update mission_5_re set name=:name,comment=:comment,day=:day,pass=:pass where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':day', $day, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $password, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            echo "パスワードが違います"."<br />";
        }
    }


    #表示部分
    $sql = 'SELECT * FROM mission_5_re';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].' ';
        echo $row['day'].'<br>';
    echo "<hr>";
    } 
?>
</body>
</html>