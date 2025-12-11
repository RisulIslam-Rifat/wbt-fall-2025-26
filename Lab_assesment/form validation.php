<!DOCTYPE html>
<html>
<head>
<title>PHP Validation Lab</title>
</head>
<body>
 
<h2>1. Name Validation</h2>
<form method="post">
Name: <input type="text" name="name">
<input type="submit" name="check_name" value="Check"><br>
 
<?php
if (isset($_POST['check_name'])) {
$name = trim($_POST['name']);
 
if (empty($name)) {
echo "❌ Name cannot be empty";
} elseif (str_word_count($name) < 2) {
echo "❌ Must contain at least two words";
} elseif (!ctype_alpha($name[0])) {
echo "❌ Must start with a letter";
} elseif (!preg_match("/^[a-zA-Z .-]*$/", $name)) {
echo "❌ Only letters, period & dash allowed";
} else {
echo "✅ Valid Name";
}
}
?>
</form>
 
 
<hr>
 
<h2>2. Email Validation</h2>
<form method="post">
Email: <input type="text" name="email">
<input type="submit" name="check_email" value="Check"><br>
 
<?php
if (isset($_POST['check_email'])) {
$email = trim($_POST['email']);
 
if (empty($email)) {
echo "❌ Email cannot be empty";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
echo "❌ Invalid Email Format";
} else {
echo "✅ Valid Email";
}
}
?>
</form>
 
 
<hr>
 
<h2>3. Date of Birth Validation</h2>
<form method="post">
DD: <input type="text" name="dd" size="3">
MM: <input type="text" name="mm" size="3">
YYYY: <input type="text" name="yyyy" size="5">
<input type="submit" name="check_dob" value="Check"><br>
 
<?php
if (isset($_POST['check_dob'])) {
$dd = $_POST['dd'];
$mm = $_POST['mm'];
$yyyy = $_POST['yyyy'];
 
if (empty($dd) || empty($mm) || empty($yyyy)) {
echo "❌ All fields required";
} elseif (!is_numeric($dd) || !is_numeric($mm) || !is_numeric($yyyy)) {
echo "❌ Must be numbers";
} elseif ($dd < 1 || $dd > 31 || $mm < 1 || $mm > 12 || $yyyy < 1953 || $yyyy > 1998) {
echo "❌ Invalid date range";
} else {
echo "✅ Valid Date of Birth";
}
}
?>
</form>
 
 
<hr>
 
<h2>4. Gender Validation</h2>
<form method="post">
<input type="radio" name="gender" value="Male"> Male
<input type="radio" name="gender" value="Female"> Female
<input type="radio" name="gender" value="Other"> Other
<br>
<input type="submit" name="check_gender" value="Check">
 
<?php
if (isset($_POST['check_gender'])) {
if (!isset($_POST['gender'])) {
echo "<br>❌ At least one must be selected";
} else {
echo "<br>✅ Selected: " . $_POST['gender'];
}
}
?>
</form>
 
 
<hr>
 
<h2>5. Degree Validation</h2>
<form method="post">
<input type="checkbox" name="degree[]" value="SSC"> SSC
<input type="checkbox" name="degree[]" value="HSC"> HSC
<input type="checkbox" name="degree[]" value="BSc"> BSc
<input type="checkbox" name="degree[]" value="MSc"> MSc
<br>
<input type="submit" name="check_degree" value="Check">
 
<?php
if (isset($_POST['check_degree'])) {
if (empty($_POST['degree']) || count($_POST['degree']) < 2) {
echo "<br>❌ At least two must be selected";
} else {
echo "<br>✅ Selected Degrees: " . implode(", ", $_POST['degree']);
}
}
?>
</form>
 
 
<hr>
 
<h2>6. Blood Group Validation</h2>
<form method="post">
<select name="blood">
<option value="">Select</option>
<option>A+</option><option>A-</option>
<option>B+</option><option>B-</option>
<option>O+</option><option>O-</option>
<option>AB+</option><option>AB-</option>
</select>
<input type="submit" name="check_blood" value="Check">
 
<?php
if (isset($_POST['check_blood'])) {
if (empty($_POST['blood'])) {
echo "<br>❌ Must be selected";
} else {
echo "<br>✅ Blood Group: " . $_POST['blood'];
}
}
?>
</form>
 
</body>
</html>