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
					<div class="card bg-light text-center mx-auto createStudent">
						<div class="card-body">
							<form action=<?php echo site_url('create/createStudent')?> method="POST" id="loginForm">
								<div class="form-group">
									<div class="card-header">
										<h4>Create Student</h4>
									</div>
									<div class="row">
										<div class="col">
											<label for="firstName">First Name</label>
											<input type="text" name="firstName" id="firstName" class="form-control" placeholder="First name" required>
										</div>
										<div class="col">
											<label for="lastName">Last Name</label>
											<input type="text" name="lastName" id="lastName" class="form-control" placeholder="Last name" required>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label id="ageLabel" for="age">Age: </label>
									<input type="range" min="5" max="15" value="1" class="slider" id="age" name="age" required>
								</div>
								<script>
									var slider = document.getElementById("age");
									var output = document.getElementById("ageLabel");
									output.innerHTML = "Age: " + slider.value;

									slider.oninput = function() {
										output.innerHTML = "Age: " + this.value;
									}
								</script>
								<button type="submit" id="submit" class="btn btn-dark">Submit</button>
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="card">
						<div class="card-body">
							<div class="card-header">
								<h3>All Your Students</h3>
							</div>
							<ul>
								<?php foreach($this->data['studentList'] as $row){
									echo "<li> " . $row->firstName . " " . $row->lastName . "</li>";
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
