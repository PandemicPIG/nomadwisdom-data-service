<!DOCTYPE html>
<html>
<head>
	<title>deploy</title>
</head>
<body>
	<h3>Deploy</h3>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<button type="submit" name="script1" value="FE">FE App</button>
		<button type="submit" name="script2" value="BE">BE Services</button>
	</form>

	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (isset($_POST["script1"])) {
				$output = shell_exec("./build.sh fe");
				echo "<pre>$output</pre>";
			} elseif (isset($_POST["script2"])) {
				$output = shell_exec("./build.sh be");
				echo "<pre>$output</pre>";
			}
		}
	?>
</body>
</html>
