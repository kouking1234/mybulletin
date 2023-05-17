<?php require('function.php'); ?>
<!-- cb=contribution=投稿 -->

<?php
session_start();
$cb_message = get_message($_GET['cb_id']);
?>
<?php foreach($cb_message as $value): ?>
  <article>
      <img src="../image/<?= $value['profile']; ?>">
    <div>
      <label for="">[user]</label>
      <?php echo $value['view_name']; ?><br>
      <label for="">[Message]</label>
      <p><?php echo nl2br($value['message']); ?></p>
      <label for="">[Time]</label>
      <time><?php echo $value['post_date']; ?></time><br>
      id:<?php echo $value['id']; ?>
    </div>
  </article>
  <?php $message_id[]=$value['id']; ?>
  <?php if(isset($_SESSION['id'])): ?>
  <a class="comment_pro" href="comment_ad.php?message_id=<?php echo $value['id']; ?>">comment</a>
  <a href="user_delete.php?message_id=<?= $value['id']; ?>">delete</a>
  <?php endif; ?>
<?php endforeach; ?>
<a href="index.php">cancel</a>

<hr>


<!-- 返信元のIDを持った返信投稿を取り出す -->
<?php $comments=get_post($_GET['cb_id']); ?>
    <?php if(!empty($comments)): ?>
          <?php foreach($comments as $come): ?>
                <button onclick="location.href='comment_detail.php?comment_id=<?= $come['id']; ?>'">
                      <h3>COMMENT</h3>
                      <img src="../image/<?= $come['profile']; ?>">
                      [USER NAME]
                      <?= $come['view_name']; ?>
                      [COMMENT]
                      <?= $come['message']; ?><br>
                      COMMENT AT <?= $come['post_date']; ?>
                      id:<?= $come['id']; ?>
                      <?php if(isset($_SESSION['id'])): ?>
                           <a href="comment_ad.php?message_id=<?= $come['id']; ?>">comment_rep</a>     
                      <?php endif; ?>
                      <hr>
                </button>
          <?php endforeach; ?>
    <?php elseif(empty($comments)): ?>
          <!-- コメントするボタンを押すとモーダルウィンドでコメントできるようにする。 -->
    <?php endif; ?>

    
    