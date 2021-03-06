<!DOCTYPE HTML>
<html lang="en">
	<head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<link rel="stylesheet" href="http://cs.smumn.edu/student/grades/ci4/public/teacherStyle.css">
	</head>
	<body>
		<main>
			<nav class="navbar navbar-expand-md navbar-light bg-light">
			<a class="navbar-brand">Early LMS</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
					<?php if(!$this->data['aauth']->isLoggedin()):?>
					<li class="nav-item active">
						<a class="nav-link" href=<?php echo site_url('/home');?>>Login <span class="sr-only">(current)</span></a>
					</li>
					<?php else: ?>
					<li class="nav-item">
						<a class="nav-link" href=<?php echo site_url('/homePage');?>>Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php echo site_url('/editGrades');?>>Grades</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php echo site_url('/studentPage');?>>Students</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php echo site_url('/parentInfo');?>>Parents</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-danger" href=<?php echo site_url('/logout');?>>Logout</a>
					</li>
					<?php endif;?>
				</ul>
			</div>
			</nav>
		</main>
	</body>
</html>
