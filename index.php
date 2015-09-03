<?php
	require_once 'assets/php/classDBhandler.php' ;
	require_once 'assets/php/classContestant.php' ;
	$formNickFeedback = '' ;
	$formNickValue = '' ;
	$formEmailFeedback = '' ;
	$formEmailValue = '' ;
	$formSSBCharFeedback = '' ;
	$formTOCFeedback = '' ;
	$antallPaameldte = DBhandler::numberOfBrawlers() ;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>OTSGaming - Registreringsside for SuperSmashBrosBrawl compo 2015-04-11</title>
		<link type="text/css" rel="stylesheet" href="assets/css/stylesheet.css" />
	</head>
	<body>
		<h1>Registreringsside for SuperSmashBrosBrawl compo 2015-04-11</h1>
		
		<p id="antallPaameldteBar">Antall påmeldte: <?php echo $antallPaameldte ; ?> (32 plasser tilgjengelig)</p>
	<?php
		if($antallPaameldte > 31){
			echo '<p>Takk for interessen.. Vi har oppnådd 32 påmeldte, og stenger derfor registreringen.</p>' ;
			echo '<p>Hvis du ikke kom med i konkurransen denne gangen, så ønsker håper vi du vil delta i senere konkurranser</p>' ;
			die() ;
		}//slutt
		if(isset($_POST['formSubmit'])){
			$datasetValidert = true ;
			$nick = trim($_POST['formNick']) ;
			if(!preg_match('/^[A-ZÆØÅa-zæøå][A-ZÆØÅa-zæøå0-9_]{1,19}$/', $nick)){
				$formNickFeedback = '<span class="warning">Første tegn må være en bokstav, nicket må være mellom 2 og 20 tegn. Følgende tegn tillatt: "<em>A-å, 0-9, _</em>"</span>' ;
				$datasetValidert = false ;
			}else{
				$nickExists = DBhandler::brawlerExists('Nick', $nick) ;
				if($nickExists){
					$formNickFeedback = '<span class="warning">Nicket "<em>' . $nick . '</em>" er tatt!</span>' ;
					$datasetValidert = false ;
				}else{
					$formNickValue = $nick ;
				}//slutt if $nickExists
			}//slutt preg_match nick
			
			$email = trim($_POST['formEmail']) ;
			if(!preg_match('/^[s][1-9][0-9]{5}+@stud\.hioa\.no$/', $email)){
				$formEmailFeedback = '<span class="warning">Kun HiOA-studenteposter tillatt</span>' ;
				$datasetValidert = false ;
			}else{
				$emailExists = DBhandler::brawlerExists('Email', $email) ;
				if($emailExists){
					$formEmailFeedback = '<span class="warning">Epostadressen "<em>' . $email . '</em>" er allerede registrert!</span>' ;
					$datasetValidert = false ;
				}else{
					$formEmailValue = $email ;
				}//slutt if $emailExists ;
			}//slutt preg_match email
			
			$charno = $_POST['formSSBChar'] ;
			if(!ctype_digit($charno)){
				$formSSBCharFeedback = '<span class="warning">Du må velge en karakter</span>' ;
				$datasetValidert = false ;
			}//slutt ctype_digit($charno)
			
			if(!isset($_POST['formTOC'])){
				$formTOCFeedback = '<span class="warning">Du må godta brukeravtalen for å kunne registrere deg.</span>' ;
				$datasetValidert = false ;
			}//slutt !isset formTOC
			
			if(!$datasetValidert){
				goto start;
			}else{
				$newContestant = new Contestant() ;
				$newContestant->setNick($nick) ;
				$newContestant->setEmail($email) ;
				$newContestant->setSSBChar($charno) ;
				
				$regSuccess = DBhandler::regBrawler($newContestant) ;
				if($regSuccess){
					$characterObject = DBhandler::retrieveCharacterById($newContestant->getSSBChar()) ;
				?>
					<h2>Takk for påmeldingen, <?php echo $newContestant->getNick() ; ?>!</h2>
					<p>Du er påmeldt SmashBros konkurransen med følgende info:</p>
					<table>
						<tr>
							<th>Nick:</th><td><?php echo $newContestant->getNick() ; ?></td>
						</tr>
						<tr>
							<th>Epost:</th><td><?php echo $newContestant->getEmail() ; ?></td>
						</tr>
						<tr>
							<th>Karakter: </th><td>Engelsk navn: <em><?php echo $characterObject->EngName ; ?></em>, Japansk navn: <em><?php echo $characterObject->JapName ; ?></em></td>
						</tr>
					</table>
					<p>Du kan lese mer om karakteren du har valgt på <a href="http://www.ssbwiki.com/<?php echo $characterObject->SSBWiki ; ?>" title="SmashWiki" target="_blank">http://www.ssbwiki.com/<?php echo $characterObject->SSBWiki ; ?></a></p>
					<p>OTS Gaming ønsker deg lykke til på kampdagen!</p>
				
			<?php
				}else{
					echo 'noe' ;
				}
			}//slutt !datasetValidert
				
		}else{	
			start:
			$wikiList = '' ;
	?>
		<table border="1">
		<form id="formContestant" action="" method="post">
			<tr>
				<td>
					<label for="formNick">Nick:</label>
				</td>
				<td>
					<input id="formNick" name="formNick" tabindex="1" value="<?php echo $formNickValue ; ?>" />
				</td>
			</tr>
			<tr>
				<td id="formNickFeedback" colspan="3"><?php echo $formNickFeedback ; ?></td>
			</tr>
			<tr>
				<td>
					<label for="formEmail">Epost:</label>
				</td>
				<td>
					<input id="formEmail" name="formEmail" value="<?php echo $formEmailValue ; ?>" tabindex="2" />
				</td>
			</tr>
			<tr>
				<td id="formEmailFeedback" colspan="3"><?php echo $formEmailFeedback ; ?></td>
			</tr>
			<tr>
				<td>
					<label for="formSSBChar">Karakter:</label>
				</td>
				<td>
					<select id="formSSBChar" name="formSSBChar" tabindex="3">
						<option value="default">Velg en karakter</option>
						<?php
							DBhandler::characterOptions($wikiList) ;
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td id="formSSBCharFeedback" colspan="2"><?php echo $formSSBCharFeedback ; ?></td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="formTOC" name="formTOC" /> Jeg godtar <a href="brukeravtale.html" title="Brukeravtale for SuperSmashBrosBrawl compo 2015-04-11" target="_blank">brukeravtalen</a></td>
			</tr>
			<tr>
				<td id="formTOCFeedback" colspan="2"><?php echo $formTOCFeedback ; ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="submit" name="formSubmit" value="Meld på konkurranse" tabindex="4" />
				</td>
			</tr>
		</form>
		</table>
		<?php
		echo $wikiList ;
		?>
		<script src='assets/js/contestantreg.js'></script>
		<?php
		}
		?>
	</body>
</html>
