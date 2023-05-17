<?php session_start(); ?>
<?php require('function.php'); ?>


<?php $reps = get_post($_GET['comment_id']);?>
      <?php foreach($reps as $rep): ?>
        <button>
            <h4>comment rep</h4>
            <img src="../image/<?= $rep['profile']; ?>">
            username:
            <?= $rep['view_name']; ?>
            comment:
            <?= $rep['message']; ?>
            comment at:
            <?= $rep['post_date']; ?>
            <?php if(isset($_SESSION['id'])): ?>
              <a href="">comment_rep まだできない</a>
            <?php endif; ?>
            <hr>
        </button>
      <?php endforeach; ?>  
