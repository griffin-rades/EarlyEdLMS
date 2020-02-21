<!DOCTYPE html>
<html lang="en">
<head>
	<title>Early Ed. LMS</title>
	<?php include('navigation.php'); ?>
</head>
<body>
    <main>
		<?php if($this->data['error']):?>
		<div class="card bg-danger text-center mx-auto errorCard">
			<div class="card-body">
				<?php echo $this->data['errors'];?>
			</div>
		</div>
		<?php elseif ($this->data['success']):?>
			<div class="card bg-danger text-center mx-auto errorCard">
				<div class="card-body">
					<?php echo $this->data['success'];?>
					<p>Please Login</p>
				</div>
			</div>
		<?php endif;?>
        <div class="card bg-light text-center mx-auto loginCard">
            <div class="card-body">
                <form action=<?php echo site_url('login/loginUser')?> method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="userEmail">Email address</label>
                        <input type="email" name="userEmail" class="form-control" id="userEmail" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" name="userPassword" class="form-control" id="userPassword" placeholder="Password">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" name="rememberMe" for="rememberMe">Remember Me</label>
                    </div>
                    <button type="submit" id="loginButton" class="btn btn-dark">Login</button>
                </form>
            </div>
            <div class="card-footer">
                <a href="<?php echo site_url('create')?>">Create Account</a>
            </div>
        </div>
    </main>
    <footer>
		<h6>Created By: Griffin Rades</h6>
	</footer>
</body>
</html>
