<!DOCTYPE html>
<html>
<head>
	<title>PHP電卓</title>
</head>
<body>
	<form method="post">
		<input type="text" name="num1" placeholder="数字1">
		<select name="operator">
			<option value="+">+</option>
			<option value="-">-</option>
			<option value="*">*</option>
			<option value="/">/</option>
		</select>
		<input type="text" name="num2" placeholder="数字2">
		<input type="submit" name="submit" value="計算する">
	</form>
	<?php
	if(isset($_POST['submit'])){
		$num1 = $_POST['num1'];
		$num2 = $_POST['num2'];
		$operator = $_POST['operator'];
		$result = '';
		switch ($operator) {
			case '+':
				$result = $num1 + $num2;
				break;
			case '-':
				$result = $num1 - $num2;
				break;
			case '*':
				$result = $num1 * $num2;
				break;
			case '/':
				if($num2 != 0){
					$result = $num1 / $num2;
				}else{
					$result = "割る数には0以外を入力してください。";
				}
				break;
			default:
				$result = "演算子を正しく選択してください。";
				break;
		}
		echo "答え：" . $result;
	}
	?>
</body>
</html>