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
									<p>Hello: <?php echo $this->data['teacherName'];?></p>
									<p>Edit Account</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="card studentDetailsHome">
							<div class="card-body">
								<div class="card-header">
									<h3>All <?php echo $this->data['teacherName'];?> Student Grades</h3>
								</div>
								<ol>
									<div class="container-fluid studentList">
									<?php foreach($this->data['studentGradeList'] as $row){
										echo "<div class='row'>";
										echo "<div class='col-6'> " . $row->studentFirstName . " " . $row->studentLastName . "</div><div class='col-6'>" . $row->grade . "</div>";
										echo "</div>";
									}
									?>
									</div>
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
