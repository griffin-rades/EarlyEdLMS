<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="style.css">
	<script src="script.js"></script>
	<title>Calculator</title>
</head>
<body>
	<header>
		<h1 > A Simple Calculator </h1>
	</header>
	<main>
		<div class = "container">
			<div class="row justify-content-center">
				<div class="col-8">
					<p id="calcDisplay">0</p>
				</div>
			</div>
			<div class="calcButtons">
			<div class = "row justify-content-center">
				<div class = "col-2">
					<button type="button" value="clear" class="btn btn-danger btn-lg btn-block" >Clear</button>
				</div>
				<div class = "col-2">
					<button type="button" value="negate" class="btn btn-primary btn-lg btn-block operator">Negate</button>
				</div>
				<div class = "col-2">
					<button type="button" value="%" class="btn btn-primary btn-lg btn-block operator">%</button>
				</div>
				<div class = "col-2">
					<button type="button" value="÷" class="btn btn-primary btn-lg btn-block operator">÷</button>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-2">
					<button type="button" value="7" class="btn btn-secondary btn-lg btn-block number">7</button>
				</div>
				<div class="col-2">
					<button type="button" value="8" class="btn btn-secondary btn-lg btn-block number">8</button>
				</div>
				<div class="col-2">
					<button type="button" value="9" class="btn btn-secondary btn-lg btn-block number">9</button>
				</div>
				<div class="col-2">
					<button type="button" value="×"class="btn btn-primary btn-lg btn-block operator">⨯</button>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-2">
					<button type="button" value="4" class="btn btn-secondary btn-lg btn-block number">4</button>
				</div>
				<div class="col-2">
					<button type="button" value="5" class="btn btn-secondary btn-lg btn-block number">5</button>
				</div>
				<div class="col-2">
					<button type="button" value="6" class="btn btn-secondary btn-lg btn-block number">6</button>
				</div>
				<div class="col-2">
					<button type="button" value="−" class="btn btn-primary btn-lg btn-block operator">−</button>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-2">
					<button type="button" value="1" class="btn btn-secondary btn-lg btn-block number">1</button>
				</div>
				<div class="col-2">
					<button type="button" value="2" class="btn btn-secondary btn-lg btn-block number">2</button>
				</div>
				<div class="col-2">
					<button type="button" value="3" class="btn btn-secondary btn-lg btn-block number">3</button>
				</div>
				<div class="col-2">
					<button type="button" value="+" class="btn btn-primary btn-lg btn-block operator">+</button>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-4">
					<button type="button" value="0" class="btn btn-secondary btn-lg btn-block number">0</button>
				</div>
				<div class="col-2">
					<button type="button" value="." class="btn btn-secondary btn-lg btn-block">.</button>
				</div>
				<div class="col-2">
					<button type="button" value="=" class="btn btn-danger btn-lg btn-block operator">=</button>
				</div>
			</div>
		</div>
		</div>
	</main>
	<footer>
		<h6>Created By: Griffin Rades</h6>
	</footer>
</body>
</html>
