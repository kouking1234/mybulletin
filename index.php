<?php
require('function.php');
// require('ajax_post_favorite_process.php');
$stmt=null;
$pdo=null;
$res=null;
$option=null;
$error_message=array();
$success_message=array();
$message_array=array();
$profile=array();
session_start();


if(isset($_SESSION['id'])){
  $msg='Hello!'.'  '.htmlspecialchars($_SESSION['name'],\ENT_QUOTES,'UTF-8');
}else{
  $msg='ログインしないと投稿できません。';
}

if(!empty($_POST['logout'])){
  if($_POST['logout']){
  session_destroy();
  header('Location:index.php');
}

}

try{
  $option=array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  );

  $pdo = new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001',$option);
}catch(PDOException $e){
  $error_message[] = $e->getMessage();
}

if(isset($_FILES['profile'])){
   $profile=$_FILES['profile'];
     if($profile['size']>0){
  if($profile['size']>1000000){
    $error_message[]='picture size too big';
  }else{
    move_uploaded_file($profile['tmp_name'],'../image/'.$profile['name']);
  }
}
}elseif(empty($_FILES['profile'])){
  $profile['name']='character_ebi_fry.png';
}

if(!empty($_POST['send'])){

  if(empty($_POST['message'])){
    $error_message[]='Input message';
  }

  
  

  if(empty($error_message)){
    $current_date=date("Y-m-d H:i:s");
    
    $stmt = $pdo->prepare("INSERT INTO message (view_name,message,post_date,profile,post_id,user_id) VALUES(:view_name,:message,:current_date,:profile,:post_id,:user_id)");

    $stmt->bindParam(':view_name', $_POST['view_name'],PDO::PARAM_STR);
    $stmt->bindParam(':message',$_POST['message'],PDO::PARAM_STR);
    $stmt->bindParam(':current_date',$current_date,PDO::PARAM_STR);
    $stmt->bindParam(':profile',$profile['name'],PDO::PARAM_STR);
    $stmt->bindParam(':post_id',$_POST['post_id'],PDO::PARAM_INT);
    $stmt->bindParam(':user_id',$_SESSION['id'],PDO::PARAM_INT);

    $res=$stmt->execute();

    if($res){
      $_SESSION['success_message'] = '投稿しました';
    
    }else{
      $error_message[]='failure...';
    }

    $stmt=null;

    //  header('Location:index.php');

  }
  
}
    $pdo=null;
  
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <script src="../jquery-3.6.0.min.js"></script>
  <title>BULLETINE</title>


</head>
<body>
  <p>bulletin</p>
  adminの方はこちら<br>
  ↓↓<br>
  <a href="admin.php">Admin Page</a><br>

  <?php echo $msg; ?>
  <?php if(isset($_SESSION['id'])): ?>
    <form method="post">
      <input type="submit" name="logout" value="logout">
    </form>   
    <a href="mypage.php">mypage</a>

  <?php elseif(empty($_SESSION['id'])): ?>
    <a href="log.php">login</a><br>
    <?php endif; ?>

  <?php if(!empty($error_message)): ?>
      <p><?php foreach($error_message as $value): ?></p>
        <?php echo $value; ?>
        <?php endforeach; ?>
        <?php endif; ?>

    <?php if(isset($_SESSION['success_message'])){
        echo $_SESSION['success_message'];
    }
    ?>

 <?php if(isset($_SESSION['id'])): ?>
  <a href="#" class="post_process">投稿</a>
  <div class="modal"></div>
    <div class="post_window">

      <div class="empty_message"></div>
      <form method="post" enctype="multipart/form-data" accept-charset="ASCII">
        <div>
        
          <label for="">Name</label><br>
          <input type="text" name="view_name" id="text" value="<?= $_SESSION['name']; ?>" readonly>
          
        </div>
        <div>
          <label for="">Message</label><br>
          <textarea id="message"  name="message" cols="30" rows="10"></textarea>
        </div>
        <input type="file" name="profile" accept="image/*" multiple>
        <input type="hidden" name="post_id" value="">
        <input class="post_button" type="submit" name="send" value="Post" >
        <button class="cancel" >cancel</button>
      </form>
    </div>
 
  
  <?php endif; ?>
<hr>

<?php $message_array = get_post(''); ?>
  <!-- 投稿を表示 -->

  <?php foreach($message_array as $value): ?>
    <?php $count_likes = count_likes($value['id']);
          $count_likes
    ?>

    <button type="button" onclick="location.href='contribution_detail.php?cb_id=<?= $value['id']; ?>'">
      <article>
          <img src="../image/<?= $value['profile']; ?>">
        <div>
          <label for="">[user]</label>
          <?php echo $value['view_name']; ?><br>
          <label for="">[Message]</label>
          <p><?php echo nl2br($value['message']); ?></p>
          <label for="">[Time]</label>
          <time><?php echo $value['post_date']; ?></time><br>
          投稿id:<?php echo $value['id']; ?>
          投稿者ID:<?php echo $value['user_id']; ?>
        </div>
      </article>
      <?php $message_id[]=$value['id']; ?>
      <?php if(isset($_SESSION['id'])): ?>
      <a class="comment_pro" href="comment_ad.php?message_id=<?php echo $value['id']; ?>">comment</a>
      <a href="user_delete.php?message_id=<?= $value['id']; ?>">delete</a>
      
     

   <?php  if(check_favorite_duplicate($_SESSION['id'],$value['id'])) { ?>

    <form action="index.php?post_delelike_id=<?= $value['id'] ?>" method="post">
      <button type="submit" name ="dele_like">&#9829;</button>
      <?php 
        if($count_likes==null){
          echo 0;
        }else{
          echo $count_likes['COUNT(fav_post_id)'];
        }
      ?>
    </form>
      
              <!-- いいね解除 -->
    <?php 
          if(isset($_POST['dele_like'])){
            $sql = "DELETE
                    FROM favorite
                    WHERE fav_user_id = :user_id AND fav_post_id = :post_id";
                    
                      try{
                        $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
                        $user='root';
                        $password='kouking2001';
                        $dbh=new PDO($dsn,$user,$password);
                        $stmt = $dbh->prepare($sql);
      //foreachだと一番最初の投稿のpost_idがvalue['id']に格納されるため
      //いいねをID2の投稿のイイねを押してもID1の投稿にイイねされてしまう
      //不具合が生じた。なので:post_idをGETで取得しないと個別に反映されない
                        $stmt->execute(array(':user_id' =>$_SESSION['id'] ,':post_id' => $_GET['post_delelike_id'] ));
                        header("Location:index.php");
                        exit();
                    }catch (\Exception $e){
                      error_log('エラー発生:'. $e->getMessage());
                      echo json_encode("error");
                    }
              
                }
    // ↓このelseはcheck_favorite_duplicateのやつ
   }else{ ?>

          <form action="index.php?post_like_id=<?= $value['id'] ?>" method="post">
                <button type="submit" name="like">&#9825;</button>
          <?php     
           if($count_likes==null){
               echo 0;
           }else{//COUNT(fav_post_id)はprint_rで$count_likesを表示させると
                 //連想配列でそういう値が入っているから取り出してるだけ。
           echo $count_likes['COUNT(fav_post_id)'];
           }       
        ?>
           </form>
           
                  <!-- いいね登録 -->
         <?php  
         if(isset($_POST['like'])){
          $sql= "INSERT INTO favorite(fav_user_id,fav_post_id)
                 VALUES(:user_id,:post_id)";
                  try{
                    $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
                    $user='root';
                    $password='kouking2001';
                    $dbh=new PDO($dsn,$user,$password);
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute(array(':user_id' =>$_SESSION['id'] ,':post_id' => $_GET['post_like_id'] ));
                    header("Location:index.php");
                    exit();
                    
                }catch (\Exception $e){
                  error_log('エラー発生:'. $e->getMessage());
                  echo json_encode("error");
                }
         }
            
   }    
      ?>
        
<?php

// ここにこれを入れるとvalue['id']に最初の値が入っちゃって
// いいねが最初の投稿に反映されてしまう。
// if(isset($sql)):
//                try{
//                 $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
//                 $user='root';
//                 $password='kouking2001';
//                 $dbh=new PDO($dsn,$user,$password);
//                 $stmt = $dbh->prepare($sql);
//                 $stmt->execute(array(':user_id' =>$_SESSION['id'] ,':post_id' => $value['id'] ));
//                 header("Location:index.php");
//                 exit();
//             }catch (\Exception $e){
//               error_log('エラー発生:'. $e->getMessage());
//               echo json_encode("error");
//             }
//           endif;
    ?>
    <!-- このendifはsessionのやつ -->
      <?php endif; ?>
      <hr>
    </button>  

  <?php endforeach; ?>

<script src="post.js"></script>
<!-- <script>

  function get_param(name, url){
    if(!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
    if(!results) return null;
    if(!results[2]) return false;
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  $(document).on('click','.favorite_btn',function(e){
    e.stopPropagation();
    var $this = $(this),
    page_id = get_param('page_id'),
    post_id = get_param('procode'); //これなんなのかよくわからない。
  $.ajax({
    type: 'POST',
    url: 'ajax_post_favorite_process.php',
    dataType: 'json',
    data: {page_id: page_id,  //page_idはユーザーIDらしい
           post_id: post_id}
  }).done(function(data){
      location.reload();
  }).fail(function() {
      location.reload();
  });
  })
</script> -->
</body>
</html>