<?php
	session_start() ;
	error_reporting(0) ;
	include 'assets/php/classDBhandler.php' ;
	include 'assets/php/classContestant.php' ;
	if(isset($_POST['loginKnapp'])){
		$brukernavn = 'FapLichter2015' ;
		$passord = 'D@796iTw(xPUDBN' ;
		$credentialsOK = true ;
		$validerBrukernavn = htmlentities($_POST['brukernavn']) ;
		$validerPassord = htmlentities($_POST['passord']) ;
		if($brukernavn != $validerBrukernavn){
			$credentialsOK = false ;
		}//slutt if !=
		if($passord != $validerPassord){			
			$credentialsOK = false ;
		}//slutt if !=
		if($credentialsOK){
			$_SESSION['innlogget'] = true ;
		}else{
		?>
		<h1>Admin-Innlogging - OTSGaming - SuperSmashBrosBrawl compo 2015-04-11</h1>
		<h3>Hei-hå!</h3>
		<form action="" method="post">
			<input type="username" name="brukernavn" placeholder="Brukernavn" /><br />
			<input type="password" name="passord" placeholder="Passord" /><br />
			<input type="submit" name="loginKnapp" value="Login" />
		</form>
		<?php
				die() ;
		}
		
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard - OTSGaming - SuperSmashBrosBrawl compo 2015-04-11</title>
		<link type="text/css" rel="stylesheet" href="assets/css/stylesheet.css" />
		<meta charset="utf8" />
	</head>
	<body>
		<?php
			if(($_SESSION['innlogget'])){
				$numberOfBrawlers = DBhandler::numberOfBrawlers() ;
		?>
		<h1>Dashboard - OTSGaming - SuperSmashBrosBrawl compo 2015-04-11</h1>
		<table border="1">
			<tr>
				<th colspan="2">Diverse info:</th>
			</tr>
			<tr>
				<th>Antall Påmeldte: </th><td><?php echo $numberOfBrawlers ; ?></td>
			</tr>
			<tr>
				<th>Mest valgte Karakter:</th><td><?php echo DBhandler::charaNinki() ; ?></td>
			</tr>
			<tr>
				<th>Siste påmelding:</th><td><?php echo DBhandler::lastOneIn() ; ?></td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<td>
					<form action="" method="post">
						<input type="submit" name="visAntallPaameldteSortert" value="Vis påmeldte, sortert på karakter" />
					</form>
				</td>
			</tr>
			<?php
				$seriesCheckOK = DBhandler::seriesCreated() ;
				if(!$seriesCheckOK){
			?>
			<tr>
				<td>
					Serieopprettelse:
				</td>
				<td>
					<form action="" method="post">
						<select name="occupancy" required>
							<option value="">Velg noe</option>
							<option value="">16</option>
							<option value="32">32</option>
							<option value="">64</option>
						</select>
						<input type="submit" name="createSeries" value="Opprett Serie" />
					</form>
				</td>
			</tr>
			<?php
			}//slutt !seriesCheck
			?>
			<tr>
				<td>
					Etterfylling med bots:
				</td>
				<td>
					<form action="" method="post">
						<select name="occupancyGoal" required>
							<option value="">Velg noe</option>
							<option value="">16</option>
							<option value="32">32</option>
							<option value="">64</option>
						</select>
						<input type="submit" name="needBots" value="etterfyll med bots!" />
					</form>
				</td>
			</tr>
			<tr>
				<td>
					Populer tier 1:
				</td>
				<td>
					<form action="" method="post">
						<select name="occupancy" required>
							<option value="">Velg noe</option>
							<option value="">16</option>
							<option value="32">32</option>
							<option value="">64</option>
						</select>
						<input type="submit" name="populateTierOne" value="Sett opp runde 1" />
					</form>
				</td>
			</tr>
		</table>
		<?php
			if(isset($_POST['populateTierOne'])){
				if(isset($_SESSION['visAntallPaameldteSortert'])){
					unset($_SESSION['visAntallPaameldteSortert']) ;
				}//slutt isset visAntall.........
				$truePopulate = DBhandler::populateTierOne($_POST['occupancy']) ;
				if($truePopulate){
					echo 'Fikk populert tier 1<br />' ;
					echo '<a href="tierOne.php" target="_self">Ny side med tier 1 brackets</a>' ;
					DBhandler::getTierMatches(1) ;
				}else{
					echo 'Fikk ikke populert tier 1' ;
				}//slutt truePopulate ;
			}elseif(isset($_POST['needBots'])){
				if(isset($_SESSION['visAntallPaameldteSortert'])){
					unset($_SESSION['visAntallPaameldteSortert']) ;
				}//slutt isset visAntall.........
				$trueBots = DBhandler::needMoreBrawlers($_POST['occupancyGoal']) ;
				if($trueBots){
					echo 'La til bots!' ;
				}else{
					echo 'La ikke til bots!' ;
				}//slutt 
			}elseif(isset($_POST['createSeries'])){
				if(isset($_SESSION['visAntallPaameldteSortert'])){
					unset($_SESSION['visAntallPaameldteSortert']) ;
				}//slutt isset visAntall.........
				$createSeriesOK = DBhandler::createSeries($_POST['occupancy']) ;
				if($createSeriesOK){
					echo 'Serien ble opprettet i databasen!' ;
				}else{
					echo 'Kunne ikke opprette serien i databasen' ;
				}//slutt
				
			}elseif(isset($_POST['visAntallPaameldteSortert'])){
		?>
		<h3>Sorterte påmeldte etter SmashBrosKarakter:</h3>
		<?php 
				echo DBhandler::tableOfBrawlers() ; 
			}//slutt isset
			
			if((isset($_POST['visAntallPaameldteSortert'])) || $_SESSION['visAntallPaameldteSortert']){
				$_SESSION['visAntallPaameldteSortert'] = true ;
		?>
		
		<?php
			}//slutt isset
		}
	
		?>
	</body>
</html>
