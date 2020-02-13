<?= $this->extend('Templates/Base') ?>

<?= $this->section('content') ?>
  <div class="card">
	<div class="card-header"><?=lang('Account.editHeader')?></div>
	<div class="card-body">
	  <?= form_open('account/edit') ?>
				<?php if (isset($errors)):?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif;?>
				<?php if (isset($infos)):?>
					<div class="alert alert-success"><?=$infos?></div>
				<?php endif;?>
		<div class="form-group">
		  <div class="form-label-group">
						<input type="email" name="email" id="inputEmail" class="form-control" placeholder="<?=lang('Account.editLabelEmail')?>" autofocus>
						<label for="inputEmail"><?=lang('Account.editLabelEmail')?></label>
		  </div>
		</div>
		<div class="form-group">
		  <div class="form-label-group">
						<input type="text" name="username" id="inputUsername" class="form-control" placeholder="<?=lang('Account.editLabelUsername')?>">
						<label for="inputUsername"><?=lang('Account.editLabelUsername')?></label>
		  </div>
		</div>
		<div class="form-group">
		  <div class="form-label-group">
						<input type="password" name="password" id="inputPassword" class="form-control" placeholder="<?=lang('Account.editLabelPassword')?>">
						<label for="inputPassword"><?=lang('Account.editLabelPassword')?></label>
		  </div>
		</div>
				<button class="btn btn-primary btn-block" type="submit"><?=lang('Account.editLabelSubmit')?></button>
		<?= form_close() ?>
		<?php if (isset($providers)): ?>
			  <div class="text-center">&mdash;</div>
			<?php foreach ($providers as $provider => $state): ?>
				<?php if ($state === false): ?>
							<a href="<?= site_url('/account/social/connect/' . $provider) ?>" class="btn btn-secondary btn-block"><?=lang('Account.editLabelSocialConnect') . $provider ?></a>
						<?php else: ?>
							<a href="<?= site_url('/account/social/disconnect/' . $provider) ?>" class="btn btn-warning btn-block"><?=lang('Account.editLabelSocialDisconnect') . $provider ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
	</div>
  </div>
<?= $this->endSection() ?>
