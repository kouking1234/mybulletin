<script src="../jquery-3.6.0.min.js"></script>

<?php
session_start();
session_regenerate_id(true);

function check_favorite_duplicate($user_id,$post_id){
  $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
  $user='root';
  $password='kouking2001';
  $dbh=new PDO($dsn,$user,$password);
  $sql="SELECT * 
        FROM favorite
        WHERE fav_user_id = :user_id AND fav_post_id = :post_id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(array(':user_id' => $user_id,
                       ':post_id' => $post_id));
  $favorite = $stmt->fetch();
  return $favorite;
}

if(isset($_POST)){
  $current_user = get_user($_SESSION['id']);
  //index.phpのajaxからポストを受ける
  $page_id = $_POST['page_id'];
  $post_id = $_POST['post_id'];

  $profile_user_id = $_POST['page_id'] ?: $current_user['user_id'];

    if(check_favorite_duplicate($current_user['id'],$post_id)){
      $action = '解除';
      $sql = "DELETE
              FROM favorite
              WHERE :fav_user_id = user_id AND :fav_post_id = post_id";
    }else{
        $action = '登録';
        $sql= "INSERT INTO favorite(user_id,post_id)
               VALUES(:user_id,:post_id)";
    }

    try{
      $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
      $user='root';
      $password='kouking2001';
      $dbh=new PDO($dsn,$user,$password);
      $stmt = $dbh->prepare($sql);
      $stmt->execute(array(':user_id' =>$current_user['id'] ,':post_id' => $post_id ));
    }catch (\Exception $e){
      error_log('エラー発生:'. $e->getMessage());
      echo json_encode("error");
    }

}

?>