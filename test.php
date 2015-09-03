<?php
	include 'assets/php/classContestant.php' ;
	include 'assets/php/classDBhandler.php' ;
	
	$teststring = '12' ;
?>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="assets/css/stylesheet.css" />
	</head>
	<body>
		<div class="tiers" id="tier1">
	<?php
		DBhandler::getTierMatches(1) ;
	?>
		</div>
<!--		<div class="tiers" id="tier2">
	<?php
//		DBhandler::getTierMatches(2, false) ;
	?>
		</div>
		<div class="tiers" id="tier2">
	<?php
//		DBhandler::getTierMatches(3, false, 2) ;
	?>
		</div>
		<div class="tiers" id="tier3">
	<?php
//		DBhandler::getTierMatches(4, false, 2) ;
	?>-->
		</div>
	</body>
</html>
