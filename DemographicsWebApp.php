<?php
	$cookie_name = "vote";
	$cookie_value = false;
	setcookie($cookie_name, $cookie_value);
?>
<!DOCTYPE html>
<html>
<head>
<meta name = "viewport" content = "width=device-width, initial-scale=1.0">
<link rel = "stylesheet" type = "text/css" href = "style.css" />
<script src = "scripts.js"></script>
</head>
<body>
<?php include 'connect.php'; 
$data = false;
	if ($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_POST["vote"])){
			$age = $_POST["voter_age"];
			$gender = $_POST["gender"];
			$party = $_POST["party"];
			$income = $_POST["voter_income"];
			
			$check_votes = "SELECT party FROM info";
			$check_votes_result =  mysqli_query($conn, $check_votes);
			if(mysqli_num_rows($check_votes_result) > 100){
				echo '<script type = "text/javascript">alert("Votes have exceeded 100. Try another time. You can check the results by clicking <<View current results>> button");</script>';
				$vote_party_1 = $vote_party_2 = $vote_party_3 = 0;
				while($row = mysqli_fetch_assoc($check_votes_result)){
					if($row["party"] == "Party 1"){
						$vote_party_1++;
					}
					elseif($row["party"] == "Party 2"){
						$vote_party_2++;
					}
					else{
						$vote_party_3++;
					}
				}
			}
			else{
				$insert_data = "INSERT INTO info(age, gender, avg_income, party) VALUES ('$age', '$gender', '$income', '$party')";
				if(mysqli_query($conn, $insert_data)){
					echo '<script type = "text/javascript">alert("Your vote is successfully cast!");</script>';
				}
				
				$vote_party_1 = $vote_party_2 = $vote_party_3 = 0;
				$select_parties = "SELECT party FROM info";
				$select_parties_result = mysqli_query($conn, $select_parties);
				while($row = mysqli_fetch_assoc($select_parties_result)){
					if($row["party"] == "Party 1"){
						$vote_party_1++;
					}
					elseif($row["party"] == "Party 2"){
						$vote_party_2++;
					}
					else{
						$vote_party_3++;
					}
				}
			}	
		}
		elseif(isset($_POST["show_results"])){
			$age = $_POST["age_poll"];
			$gender = $_POST["gender_poll"];
			$income = $_POST["income_poll"];

			$select_data = "SELECT party FROM info WHERE age = '$age' AND gender = '$gender' AND avg_income = '$income'";
			$select_data_result = mysqli_query($conn, $select_data);
			
			$vote_party_1 = $vote_party_2 = $vote_party_3 = 0;
			while($row = mysqli_fetch_assoc($select_data_result)){
				if($row["party"] == "Party 1"){
					$vote_party_1++;
				}
				elseif($row["party"] == "Party 2"){
					$vote_party_2++;
				}
				else{
					$vote_party_3++;
				}
			}
			$data = true;
		}
		else{echo '<script type = "text/javascript">alert("Please do not alert the elements!");</script>';}
	}
	mysqli_close($conn);
?>
	<form id = "form_1" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "post">
	<div id = "main_container">
		<div id = "left_div">
			<h2>Demographics</h2>
			<div id = "demographics_container">
				<div>
					<label for = "age">Age</label><br/>
					<label for = "genders">Gender</label><br/>
					<label for = "income">Average income</label>
				</div>
				<div>
					<select id ="age" name = "voter_age" required onclick = "addValue('input_age', this.value);">
						<option value = "18-35">18-35</option>
						<option value = "36-60">36-60</option>
					</select><br/>
					<span id = "genders" required>
						<input type = "radio" id = "male" name = "gender" value = "Male" onclick = "addValue('input_genders', this.value);" required /><label for = "male">Male</label>
						<input type = "radio" id = "female" name = "gender" value = "Female" onclick = "addValue('input_genders', this.value);" /><label for = "female">Female</label>
						<input type = "radio" id = "other" name = "gender" value = "Other" onclick = "addValue('input_genders', this.value);" /><label for = "other">Other</label>
					</span><br/>
					<input type = "number" name = "voter_income" min = "0" step = "5000" onchange = "addValue('input_income', this.value);" required></input>
				</div>
			</div><br/>
			<h2>Vote</h2>
			<span id = "parties">
				<input type = "radio" id = "party1" name = "party" value = "Party 1" onclick = "addValue('input_party', this.value);" required >Party 1</input><br/>
				<input type = "radio" id = "party2" name = "party" value = "Party 2" onclick = "addValue('input_party', this.value);" >Party 2</input><br/>
				<input type = "radio" id = "party3" name = "party" value = "Party 3" onclick = "addValue('input_party', this.value);" >Party 3</input><br/>
			</span><br/>
			<input type = "button" value = "Vote" onclick = "changeVisibility('right_div');" />
			<input type = "button" value = "View current results" onclick = "changeVisibility('secondary_container')"/>
		</div>
		<div id = "right_div">
			<h3 id = "drc_header">Your input</h3>
			<div id = "demographics_result_container">
				<div>
					<label for = "input_age">Age:</label><br/>
					<label for = "input_genders">Gender:</label><br/>
					<label for = "input_income">Average income:</label>
				</div>
				<div>
					<div id = "input_age"  class = "dynamic_cont">-</div>
					<div id = "input_genders" class = "dynamic_cont">-</div>
					<div id = "input_income" class = "dynamic_cont">-</div>
				</div>
			</div><br/>
			<label id = "vote_label" for = "input_party">Vote: </label><h3 id = "input_party" class = "dynamic_cont">-</h3><br/><br/>
			<input id = "vote_cast" type = "submit" name = "vote" form = "form_1" value = "Cast your vote!"/>
		</div>
	</div>
	</form>
	
	<form id = "form_2" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "post">
	<div id = "secondary_container">
		<h2>Poll Results</h2>
		<h3>Criteria</h3>
		<div id = "poll_results_container">
			<div>
				<label for = "poll_age">Age</label><br/>
				<label for = "poll_genders">Gender</label><br/>
				<label for = "poll_income">Average income</label>
			</div>
			<div>
				<select id ="poll_age" name= "age_poll" required>
					<option value = "18-35">18-35</option>
					<option value = "36-60">36-60</option>
				</select><br/>
				<span id = "poll_genders">
					<input type = "radio" id = "male_poll" name = "gender_poll" value = "Male" required /><label for = "male_poll">Male</label>
					<input type = "radio" id = "female_poll" name = "gender_poll" value = "Female"/><label for = "female_poll">Female</label>
					<input type = "radio" id = "other_poll" name = "gender_poll" value = "Other"/><label for = "other_poll">Other</label>
				</span><br/>
				<input type = "number" id = "poll_income" name = "income_poll" min = "0" step = "5000" required></input>
			</div>
		</div><br/>
		<input type = "submit" name = "show_results" form = "form_2" value = "Show Results"/>
		
		<div id = "parties">
			<h3>Current Results</h3>
				<label for = "result_party1"><strong>Party 1</strong></label><progress id = "result_party1" min = "0" max = "100" value = "<?php echo $vote_party_1 ?>"></progress></br>
				<label for = "result_party2"><strong>Party 2</strong></label><progress id = "result_party2" min = "0" max = "100" value = "<?php echo $vote_party_2 ?>"></progress></br>
				<label for = "result_party3"><strong>Party 3</strong></label><progress id = "result_party3" min = "0" max = "100" value = "<?php echo $vote_party_3 ?>"></progress>
		</div>
	</div>
	</form>
	<script>if(<?php if($data){echo 1;}else{echo 0;} ?>){changeVisibility('secondary_container');}</script>
</body>
</html>