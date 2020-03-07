<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Create Account</title>
	<?php include('navigation.php'); ?>
</head>
<body>
<main>
	<?php if($this->data['errors']):?>
		<div class="card bg-danger text-center mx-auto errorCard">
			<div class="card-body">
				<?php echo $this->data['errors'];?>
			</div>
		</div>
	<?php endif;?>
	<div class="card bg-light text-center mx-auto newUserForm">
		<div class="card-body">
			<form action=<?php echo site_url('create/newUser')?> method="POST" id="newUserForm">
				<div class="form-group">
					<label for="userEmail">Email address</label>
					<input type="email" name="userEmail" class="form-control" id="userEmail" placeholder="Enter email" required>
				</div>
				<div class="form-group">
					<label for="userPassword">Password</label>
					<input type="password" name="userPassword" class="form-control" id="userPassword" placeholder="Password" required>
				</div>
				<div class="form-group">
					<label for="userName">Username</label>
					<input type="text" name="userName" class="form-control" id="userName" placeholder="UserName" required>
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
				<button type="submit" id="createAccountButton" class="btn btn-dark">Create Account</button>
			</form>
		</div>
</main>
</body>
</html>
