<?php require '/Views/header.php' //$_SERVER['DOCUMENT_ROOT'] . '/Views/header.php' ?>
<?php $model->type('Framework\ViewModels\User\GetRegisterViewModel') ?>

<div class='col-lg-12'>
  <?= $model->success ? $model->ListSuccessMessages() : ''; ?>
</div>

<?php if(!$model->success): ?>
<div class='col-lg-7'>
<h3>Register</h3>

<form action="" method="post">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" name="username" id="username" placeholder="Username">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="confirmPassword">Confirm Password</label>
    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">
  </div>

  <input type='hidden' value= <?php 
                    \Framework\Core\Csrf::generate();
                    echo \Framework\Core\Csrf::getToken();
                  ?> name= <?php echo \Framework\Config\Config::ACSRF_FIELD_NAME; ?> />

  <button type="submit" class="btn btn-primary">Register</button>
</form>
</div>

<div class='errors-box col-lg-4 pull-right'>
<?= $model->error ? $model->ListErrors() : ''; ?>
</div>

<?php endif; ?>

<?php require '/Views/footer.php' //$_SERVER['DOCUMENT_ROOT'] .'/Views/footer.php' ?>