<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title><?= (isset($title) ? $title : '') ?></title>
		<link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
		<link href="/assets/css/sb-admin.min.css" rel="stylesheet">
		<?php if (isset($cssFiles)): ?>
			<?php foreach ($cssFiles as $cssFile): ?>
		<link href="<?= $cssFile; ?>" rel="stylesheet">
		<?php endforeach; ?>
		<?php endif; ?>
	</head>
	<body id="page-top">
		<nav class="navbar navbar-expand navbar-dark bg-dark static-top">
			<a class="navbar-brand mr-1" href="<?= site_url() ?>">Aauth</a>
			<ul class="navbar-nav ml-4">
				<li class="nav-item">
					<a href="<?= site_url() ?>">Home</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<?php if (is_loggedin()): ?>
					<li class="nav-item dropdown no-arrow">
						<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-user-circle fa-fw"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
						<?php if (is_admin()): ?>
							<a class="dropdown-item" href="<?= site_url('admin') ?>">Admin Area</a>
							<div class="dropdown-divider"></div>
						<?php endif; ?>
							<a class="dropdown-item <?= uri_string() === 'account' ? 'active' : '' ?>" href="<?= site_url('account') ?>">Profile</a>
							<a class="dropdown-item <?= uri_string() === 'account/edit' ? 'active' : '' ?>" href="<?= site_url('account/edit') ?>">Edit Profile</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?= site_url('account/logout') ?>">Logout</a>
						</div>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
		<div class="m-5">
		<?= $this->renderSection('content') ?>
	</div>
	<a class="scroll-to-top rounded" href="#page-top">
	  <i class="fas fa-angle-up"></i>
	</a>

	<script src="/assets/vendor/jquery/jquery.min.js"></script>
	<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
	<script src="/assets/js/sb-admin.min.js"></script>
	<?php  if (isset($jsFiles)): ?>
		<?php  foreach ($jsFiles as $jsFiles): ?>
	  <script type="text/javascript" src="<?= $jsFile; ?>"></script>
	<?php  endforeach; ?>
	<?php  endif; ?>
	  </body>
</html>
