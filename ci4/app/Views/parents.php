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
						<h4>Create Parent</h4>
					</div>
					<div class="card-body">
						<form action=<?php echo site_url('create/createParent')?> method="POST" id="createParent">
							<div class="form-group">
								<label for="parentEmail">Parent Email</label>
								<input type="email" name="parentEmail" id="parentEmail" class="form-control" placeholder="Email" required>
							</div>
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
								<label for="studentInfo">Choose a student:</label>
								<select id="studentInfo" size="1" name="studentInfo">
									<?php foreach($this->data['studentList'] as $row){
										echo "<option value='$row->id'> " . $row->firstName . " " . $row->lastName . "</option>";
									}?>
								</select>
							</div>
							<button type="submit" id="submit" class="btn btn-dark">Submit</button>
						</form>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card text-center mx-auto">
					<div class="card-header">
						<h4>Send Email</h4>
					</div>
					<div class="card-body">

					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="card text-center mx-auto">
					<div class="card-header">
						<h4>Parent Student List</h4>
					</div>
					<div class="card-body">
						<div class="container-fluid studentList">
							<div class="row">
								<div class="col-6">
									<h5 style="text-decoration: underline">Parents List</h5>
								</div>
								<div class="col-6">
									<h5 style="text-decoration: underline">Student Name</h5>
								</div>
							</div>
							<?php foreach($this->data['parentList'] as $row){
								echo "<div class='row'>";
								echo "<div class='col-6'> " . $row->firstNameP . $row->lastNameP . "</div><div class='col-6'>" . $row->firstNameS . $row->lastNameS . "</div>";
								echo "</div>";
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
</body>
</html>
