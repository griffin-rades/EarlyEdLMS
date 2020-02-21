<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Teacher Home</title>
		<?php include('navigation.php'); ?>
	</head>
	<body>
		<main>
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-sm-3">
						<div class="card accountDetailsHome";">
							<div class="card-body">
								<div class="card-header">
									<h3>Account Details</h3>
								</div>
								<div class="card-body">
									<p>Hello: <?php echo $this->data['user']['username'];?></p>
									<p>Edit Account</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="card studentDetailsHome";">
							<div class="card-body">
								<div class="card-header">
									<h3>All <?php foreach($this->data['teacherName'] as $row){echo $row->firstName ." ". $row->lastName;}?> Student Grades</h3>
								</div>
								<ol>
									<?php foreach($this->data['studentGradeList'] as $row){
										echo "<li>";
										echo $row->studentFirstName . $row->studentLastName . $row->grade;
										echo "</li>";
									}
									?>
								</ol>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="card classesHome";">
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
