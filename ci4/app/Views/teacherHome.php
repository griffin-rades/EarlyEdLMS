<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Teacher Home</title>
		<?php include('navigation.php'); ?>
	</head>
	<body>
		<main>
			<?php echo $this->data['user']['username'];?>
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-sm">
						<div class="card teacherHomeCard";">
							<div class="card-body">
								<div class="card-header">
									<h3>Account Details</h3>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm">
						<div class="card teacherHomeCard";">
							<div class="card-body">
								<div class="card-header">
									<h3>Student Grades</h3>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm">
						<div class="card teacherHomeCard";">
							<div class="card-body">
								<div class="card-header">
									<h3>Classes</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</body>
</html>
