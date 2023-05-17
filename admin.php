<?php
define('adminpass','dddd');
$stmt=null;
$pdo=null;
$res=null;
$option=null;
$error_message=array();
$success_message=array();
$message_array=array();
$id_array=array();

session_start();
if(isset($_POST['logout'])){
  unset($_SESSION['admin_log']);
}
if(isset($_POST['guest_page'])){
  unset($_SESSION['admin_log']);
  header('Location:./index.php');
}

try{
  $option=array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );

  $pdo = new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001',$option);
}catch(PDOException $e){
  $error_message[] = $e->getMessage();
}

if(isset($_POST['admin_log'])){
  if($_POST['admin_pass']===adminpass){
    $_SESSION['admin_log'] = true;
  }else{
    $error_message[]='failure.....';
  }
}

//これがないとボタン押してないのにエラーメッセージが出たりしてしまう。
  if(!empty($_POST['submit'])){

    if(empty($_POST['view_name'])){
      $error_message[]='Input name';
    }

    if(empty($_POST['message'])){
      $error_message[]='Input message';
    }elseif(10<mb_strlen($_POST['message'])){
      $error_message[]='whthin 10 letter';
    }
//これがないと空白なのに投稿できちゃう。
    if(empty($error_message)){
      $current_date=date("Y-m-d H:i:s");

      $stmt = $pdo->prepare("INSERT INTO message (view_name,message,post_date) VALUES(:view_name,:message,:current_date)");

      $stmt->bindParam(':view_name', $_POST['view_name'],PDO::PARAM_STR);
      $stmt->bindParam(':message',$_POST['message'],PDO::PARAM_STR);
      $stmt->bindParam(':current_date',$current_date,PDO::PARAM_STR);

      $res=$stmt->execute();

      if($res){
        $success_message[]='$success';
      }else{
        $error_message[]='failure...';
      }

      $stmt=null;

      header('Location:./');
      exit;

    }
    
  }

$sql="SELECT * from message order by post_date desc";
    $message_array = $pdo->query($sql);
$pdo=null;

if(isset($_POST['select_id'])){

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BULLETINE [Admin]</title>
</head>
<body>
  <h1>BULLETINE [Admin]</h1>

  <?php if(!empty($success_message)): ?>
    <?php foreach($success_message as $value): ?>
      <p><?php echo $value; ?></p>
      <?php endforeach; ?>
      <?php endif; ?>

  <?php if(!empty($error_message)): ?>
    <p><?php foreach($error_message as $value): ?></p>
      <?php echo $value; ?>
      <?php endforeach; ?>
      <?php endif ?>

<?php if(isset($_SESSION['admin_log']) && $_SESSION['admin_log']===true): ?>

<form action="" method="post">
     <input type="submit" name="logout" value="LogOut">
</form>

<form action="" method="post">
  <input type="submit" name="guest_page" value="Go GuestPage">
</form>

<br>
    <form method="post" action="admin.php">
      <div>
        <label for="">Name</label><br>
        <input type="text" name="view_name">
      </div>
      <div>
        <label for="">Message</label><br>
        <textarea name="message" id="" cols="30" rows="10"></textarea>
      </div>
      <input type="submit" name="submit" value="Post">
    </form>
  <hr>

    <?php foreach($message_array as $value): ?>
    <article>
      <div>
        <label for="">[Name]</label>
        <?php echo $value['view_name']; ?><br>
        <label for="">[Message]</label>
        <p><?php echo nl2br($value['message']); ?></p>
        <label for="">[Time]</label>
        <time><?php echo $value['post_date']; ?></time>
      </div>
    </article>
    <p><a href="edit.php?message_id=<?php echo $value['id'];?>">Edit</a>
    <a href="delete.php?message_id=<?php echo $value['id'];?>">Dlete</a>
    <?php echo $value['id']; ?>
    
    </p>

    <form method="post" action="delete.php">
      <input type="checkbox" name="select_id[]" value="<?php echo $value['id']; ?>">
      <hr>
      <?php endforeach; ?>
      <input type="submit" value="selectDelete">
    </form>

<?php else: ?>

    <form action="" method="post">
      <div>
        <label for="">please admin login</label>
        <input type="password" name="admin_pass">
      </div>
      <input type="submit" name="admin_log" value="login">
    </form>
    <a href="index.php">cancel</a>

<?php endif; ?>
</body>
</html>