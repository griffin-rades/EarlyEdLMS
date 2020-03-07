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
					<div class="card text-center mx-auto createStudent">
						<div class="card-body">
							<div class="card-header">
								<h4>Create Student</h4>
							</div>
							<form action=<?php echo site_url('create/createStudent')?> method="POST" id="createStudent">
								<div class="form-group">
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
						<div class="card-header">
							<h3>All Your Students</h3>
						</div>
						<div class="card-body">
							<div class="container-fluid studentList">
								<div class="row">
									<div class="col-6">
										<h5 style="text-decoration: underline">Name</h5>
									</div>
									<div class="col-6">
										<h5 style="text-decoration: underline">Note</h5>
									</div>
								</div>
								<?php foreach($this->data['studentList'] as $row){
									echo "<div class='row'>";
									echo "<div class='col-6'> " . $row->firstName . " " . $row->lastName . "</div><div class='col-6'>" . $row->info . "</div>";
									echo "</div>";
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="card text-center">
						<div class="card-body">
							<div class="card-header">
								<h4>Student Note</h4>
							</div>
							<form action=<?php echo site_url('studentPage/studentNote')?> method="POST" id="studentText">
								<div class="form-group">
									<label for="studentInfo">Choose a student:</label>
									<select id="studentInfo" size="1" name="studentInfo">
										<?php foreach($this->data['studentList'] as $row){
											echo "<option value='$row->id'> " . $row->firstName . " " . $row->lastName . "</option>";
										}?>
									</select>
								</div>
								<div class="form-group">
									<label for="textInfo">Student Info</label>
									<textarea name="textInfo" maxlength="200" rows="5" cols="50" id="textInfo" class="form-control" required></textarea>
								</div>
								<button type="submit" id="save" class="btn btn-dark">Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
