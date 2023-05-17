<?php
session_start();
require('function.php'); 

$error_message=array();
$message_id=array();

try{
  $option=array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
  $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001',$option);

 
}catch(PDOException $e){
$error_message[]= $e->getMessage();
}


if(empty($_POST['user_name'])){
  $error_message[]='please input user name.';
}

if(empty($_POST['text'])){
  $error_message[]='please input text comment.';
}
// $text=mb_convert_encoding($_POST['text'],"UTF-8");
// $user_name=mb_check_encoding($_POST['user_name',"UTF-8"]);

if(isset($_FILES['image'])){
  $file_image=$_FILES['image'];
  if($file_image['size']>0){
    if($file_image['size']>1000000){
      $error_message[]='picture is too big';
    }else{
      move_uploaded_file($file_image['tmp_name'],'../image/'.$file_image['name']);
    }
  }
}elseif(empty($_FILES['image'])){
  $file_image['name']='character_ebi_fry.png';
}

if(empty($_POST['connent_id'])){
  $_POST['connent_id']='';
}



if(empty($error_message)){

$current_date=date("Y-m-d H:i:s");
$stmt = $pdo->prepare("INSERT INTO message (message,profile,view_name,post_date,post_id,user_id) VALUES(:text,:image,:user_name,:created_at,:post_id,:user_id)");
$stmt->bindParam(':text',$_POST['text'],PDO::PARAM_STR);
$stmt->bindParam(':image',$file_image['name'],PDO::PARAM_STR);
$stmt->bindParam(':user_name',$_POST['user_name'],PDO::PARAM_STR);
$stmt->bindParam(':created_at',$current_date,PDO::PARAM_STR);
$stmt->bindParam(':post_id',$_POST['connect_id'],PDO::PARAM_INT);
$stmt->bindParam(':user_id',$_SESSION['id'],PDO::PARAM_INT);



$res=$stmt->execute();

  
  if($res){
    header('Location:./index.php');
  }else{
    $error_message[]='insert できません';
  }

  $stmt=null;
}
$pdo=null;






?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="../jquery-3.6.0.min.js"></script>
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

  <title>Document</title>
</head>
<body>
  <div class="modal"></div>
  <div class="comment">
        
    <?php if(!empty($_GET['message_id'])): ?>
  
    <?php  $messages = get_message($_GET['message_id']); ?>
    
      <form method="post" enctype="multipart/form-data" accept-charset="ASCII">
        <label for="">Name</label>
            <input type="text" name="user_name" value="<?= $_SESSION['name']; ?>" required readonly><br>
            
            <?php if(isset($error_message)): ?>
              <?php foreach($error_message as $errormess): ?>
              <p style="color:red"><?php echo $errormess; ?></p> 
                <?php endforeach;?>
            <?php endif; ?>

            <?php foreach($messages as $message): ?>
            <p>"<?= nl2br($message['message']); ?>"</p>
            <?php endforeach; ?>
            
            <textarea id="" cols="30" rows="10" name="text"></textarea><br>
            <label id="label_file">
              <input type="file" name="image"  multiple style="display:none;" class="js-upload-file">ファイルを選択
            </label>
            <div class="js-upload-filename">ファイルが未選択です</div>
            <div class="fileclear js-upload-fileclear">選択ファイルをクリア</div>
            <input type="submit" value="とコメントする" name="comment">
            <input type="text" name="connect_id" value="<?= $_GET['message_id']; ?>"> 
            コメントする人のID"<?= $_SESSION['id']; ?>"
      </form>
      <?php endif; ?>
    <a href="./index.php">Cancel</a>
  </div>
  <style>
    input[type="file"]{
      opacity: 0;
      vissibility: hidden;
      position: absolute;
    }

    label#label_file {
      padding:10px 25px;
      margin: 0 0 10px;
      background: #aaa;
      color: #fff;
      display: inline-block;
      cursor: pointer;
    }

    .fileclear{
      display: none;
      margin:10px 0 0;
      text-decoration: underline;
      font-weight:bold;
      cursor: pointer;
    }
  </style>
  <script>
    $('.js-upload-file').on('change',function(){
      var file = $(this).prop('files')[0];
      $('.js-upload-filename').text(file.name);
      $('.js-upload-fileclear').show();
    });
    $('.js-upload-fileclear').click(function(){
      $('.js-upload-file').val('');
      $('.js-upload-filename').text('ファイルが未選択です');
      $(this).hide();
    });
  </script>
</body>
</html>
