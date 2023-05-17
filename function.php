<?php

// function get_post($id){
//   try{
//     $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');
//     $sql="SELECT * FROM message where id = :id";
//     $stmt=$pdo->prepare($sql);
//     $stmt->execute(array(':id'=>$id));
//     return $stmt->fetchAll();
// }catch(PDOException $e){
//   $error_message[]=$e->getMessage();
//   }
// }

function count_likes($post_id){
  $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
  $user='root';
  $password='kouking2001';
  $dbh=new PDO($dsn,$user,$password);
  $sql="SELECT COUNT(fav_post_id)
        FROM favorite
        WHERE fav_post_id = :post_id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(array(':post_id' => $post_id));
  return $stmt->fetch(); 
}

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

function get_like($like_user_id){
  $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
  $user='root';
  $password='kouking2001';
  $dbh=new PDO($dsn,$user,$password);
  $sql = "SELECT *
          FROM favorite
          WHERE id = :id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(array(':id' => $like_user_id));
  return $stmt->fetch(); 
}

function get_user($user_id){
      $dsn='mysql:dbname=board4;host=localhost;charset=utf8';
      $user='root';
      $password='kouking2001';
      $dbh=new PDO($dsn,$user,$password);
      $sql = "SELECT * 
              FROM users
              WHERE id = :id ";
      $stmt = $dbh->prepare($sql);
      $stmt->execute(array(':id' => $user_id));
      return $stmt->fetch();
}

function get_message($id){
    try{
      $dbc=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');
  $stmt=$dbc->prepare("SELECT * FROM message where id=:id");
  $stmt->bindValue(':id',$id,PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
  }catch(PDOException $e){
    $dbc->rollBack();
    $error_message[]='select not';
  }
}

    function get_post($post_id){
      try{
        $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');
        $sql="SELECT * FROM message where post_id = :post_id order by post_date desc";
        $stmt=$pdo->prepare($sql);
        $stmt->execute(array(':post_id'=>$post_id));
        return $stmt->fetchAll(); 
    }catch(PDOException $e){
      $error_message[]=$e->getMessage();
      }
    }

    function get_comment($id){
      try{
        $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');

        $sql="SELECT * from message where id = :id";
        $stmt=$pdo->prepare($sql);
        $stmt->execute(array(':id'=>$id));
        return $stmt->fetchAll();
  }catch(PDOException $e){
    $error_message[]=$e->getMessage();
    }
  }

  function get_my_post($view_name){
    try{
      $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');

      $sql="SELECT * from message where view_name = :view_name";
      $stmt=$pdo->prepare($sql);
      $stmt->execute(array(':view_name'=>$view_name));
      return $stmt->fetchAll();
}catch(PDOException $e){
  $error_message[]=$e->getMessage();
  }
  }

  function get_maxid(){
    $pdo=new PDO('mysql:charset=UTF8;dbname=board4;host=localhost','root','kouking2001');
    $sql ="SELECT MAX(id) FROM message";
    $stmt=$pdo->query($sql);
    return $stmt->fetch();
  }
?>
