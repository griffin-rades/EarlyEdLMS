<?= $this->extend('Templates/Blank') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="loginStyle.css">
<div class="container">
  <div class="card card-login mx-auto mt-5">
    <div class="card-header"><?=lang('Account.loginHeader')?></div>
    <div class="card-body">
      <?= form_open('account/login') ?>
      <?php if (isset($errors)):?>
        <div class="alert alert-danger"><?=$errors?></div>
      <?php endif;?>
      <div class="form-group">
        <div class="form-label-group">
          <?php if ($useUsername):?>
            <input type="text" name="username" id="inputUsername" class="form-control" placeholder="<?=lang('Account.loginLabelUsername')?>" required autofocus>
            <label for="inputUsername"><?=lang('Account.loginLabelUsername')?></label>
          <?php else:?>
            <input type="email" name="email" id="inputEmail" class="form-control" placeholder="<?=lang('Account.loginLabelEmail')?>" required autofocus>
            <label for="inputEmail"><?=lang('Account.loginLabelEmail')?></label>
          <?php endif;?>
        </div>
      </div>
      <div class="form-group">
        <div class="form-label-group">
          <input type="password" name="password" id="inputPassword" class="form-control" placeholder="<?=lang('Account.loginLabelPassword')?>" required>
          <label for="inputPassword"><?=lang('Account.loginLabelPassword')?></label>
        </div>
      </div>
      <div class="form-group">
        <div class="checkbox">
          <label>
            <input type="checkbox" name="remember" value="true">
            <?=lang('Account.loginLabelRemember')?>
          </label>
        </div>
      </div>
      <button class="btn btn-primary btn-block" type="submit">sub</button>
      <?= form_close() ?>
      <?php if (isset($providers) && $providers): ?>
        <div class="text-center">&mdash;</div>
        <?php foreach ($providers as $provider): ?>
          <a href="<?= site_url('account/social/login/' . $provider) ?>" class="btn btn-info btn-block" type="submit">subm,it</a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="card-footer">
      <div class="row">
        <div class="col-6">
          <a class="d-block small" href="<?=site_url('account/remind_password')?>"><?=lang('Account.linkRemindPassword')?></a>
        </div>
        <div class="col-6 text-right">
          <a class="d-block small" href="<?=site_url('account/register')?>"><?=lang('Account.linkRegister')?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
