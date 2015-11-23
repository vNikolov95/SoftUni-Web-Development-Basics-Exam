<?php require '/Views/header.php' //$_SERVER['DOCUMENT_ROOT'] . '/Views/header.php' ?>
<?php $model->type('Framework\ViewModels\User\GetProfileViewModel') ?>

<div class='col-lg-12'>
  <?= $model->success ? $model->ListSuccessMessages() : ''; ?>
</div>

<div class='col-lg-7'>
<h2>Welcome, <?= $model->username ?> </h2>


</div>

<div class='errors-box col-lg-4 pull-right'>
<?= $model->error ? $model->ListErrors() : ''; ?>
</div>

<?php require '/Views/footer.php' //$_SERVER['DOCUMENT_ROOT'] .'/Views/footer.php' ?>