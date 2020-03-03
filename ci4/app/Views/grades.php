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
					<div class="card-header">
						<h4>Create Assignment</h4>
					</div>
					<div class="card-body">
						<form action=<?php echo site_url('create/createAssignment')?> method="POST" id="createAssignment">
							<div class="form-group">
								<label for="assignTitle">Assignment Title</label>
								<input type="text" name="assignTitle" id="assignTitle" class="form-control" placeholder="Title" required>
							</div>
							<div class="form-group">
								<label for="assignDesc">Assignment Description</label>
								<input type="text" name="assignDesc" id="assignDesc" class="form-control" placeholder="A short Description" required>
							</div>
							<div class="form-group">
								<label id="points" for="pointSlider">Max Points: </label>
								<input type="range" min="1" max="100" value="1" class="slider" id="pointSlider" name="pointSlider" required>
							</div>
							<button type="submit" id="submit" class="btn btn-dark">Submit</button>
						</form>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card text-center mx-auto">
					<div class="card-header">
						<h4>All assignments</h4>
					</div>
					<div class="card-body">
						<div class="container-fluid studentList">
							<div class="row">
								<div class="col-6">
									<h5 style="text-decoration: underline">Assignment Title</h5>
								</div>
								<div class="col-6">
									<h5 style="text-decoration: underline">Description</h5>
								</div>
							</div>
							<?php foreach($this->data['assignList'] as $row){
								echo "<div class='row'>";
								echo "<div class='col-6'> " . $row->title . "</div><div class='col-6'>" . $row->description . "</div>";
								echo "</div>";
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="card text-center mx-auto">
					<div class="card-header">
						<h4>Edit Grades</h4>
					</div>
					<div class="card-body">
						<form action=<?php echo site_url('editGrades/gradeStudent')?> method="POST" id="editGrades">
							<div class="form-group">
								<label for="studentInfo">Choose a student:</label>
								<select id="studentInfo" size="1" name="studentInfo">
									<?php foreach($this->data['studentList'] as $row){
										echo "<option value='$row->id'> " . $row->firstName . " " . $row->lastName . "</option>";
									}?>
								</select>
							</div>
							<div class="form-group">
								<label for="assign">Choose an assignment:</label>
								<select id="assign" size="1" name="assign">
									<?php foreach($this->data['assignList'] as $row){
										echo "<option value='$row->id'> " .  $row->title . "</option>";
									}?>
								</select>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label id="points2" for="pointSlider2">Points: </label>
									<input type="range" min="1" max="100" value="1" class="slider" id="pointSlider2" name="pointSlider2" required>
								</div>
								<script>
									var slider2 = document.getElementById("pointSlider2");
									var output2 = document.getElementById("points2");

									var slider = document.getElementById("pointSlider");
									var output = document.getElementById("points");

									output2.innerHTML = "Points: " + slider2.value;
									output.innerHTML = "Max Points: " + slider.value;

									slider2.oninput = function() {
										output2.innerHTML = "Points: " + this.value;
									}

									slider.oninput = function() {
										output.innerHTML = "Max Points: " + this.value;
									}
								</script>
							</div>
							<button type="submit" id="save" class="btn btn-dark">Save</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-sm-6">
				<div class="card text-center mx-auto">
					<div class="card-header">
						<h4>Student Grades</h4>
					</div>
					<div class="card-body">
						<dl>
							<?php foreach($this->data['assignList'] as $row){
								$assignStudent = $this->data['db']->query('SELECT points, firstName, lastName FROM assignGrade JOIN lms_students ON lms_students.id = assignGrade.studentID WHERE assignGrade.assignmentID = ' . $row->id);
								$assignGrade = $assignStudent->getResult();

								echo "<dt> " .  $row->title . " " . "Max Points: " . $row->maxPoints . "</dt>";
									foreach($assignGrade as $row2){
										echo "<dd>" . $row2->firstName . " " . $row2->lastName . " " . "Points: " . $row2->points . " " . "Percent: " . round((($row2->points / $row->maxPoints) * 100), 2) ."</dd>";
									}
							}?>
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
</body>
</html>
