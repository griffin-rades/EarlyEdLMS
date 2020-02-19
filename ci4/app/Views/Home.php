<?= $this->extend('Templates/Base') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="loginStyle.css">
<div class="card bg-light text-center mx-auto">
	<div class="card-body">
		<h1 class="mb-5">Welcome To Early Education LMS</h1>
		<p class="lead">Early Education LMS is a software designed to give teachers of elementry grade students the ability to track and record infromation on their students.</p>
		<?php if(is_loggedin()): ?>
			<p>You are logged in</p>
			<a href="<?= site_url('account/home') ?>" class="btn btn-primary px-5">Account Home</a>
		<?php else: ?>
			<p>If you are a teacher please click login</p>
			<a href="<?= site_url('account/login') ?>" class="btn btn-primary px-5">Login</a>
		<?php endif; ?>
	</div>
</div>
<?= $this->endSection() ?>
