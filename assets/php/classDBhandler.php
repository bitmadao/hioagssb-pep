<?php
	class DBhandler{
		static function logDBerror($error){
			$message = date("d-m-Y H:i") . ' ' . $error . "\n" ; 
			error_log($message, 3, 'log/mysqlerrorlog.txt') ;
		}//slutt logDBerror
		
		static function dbConnect(){
			$Link = new mysqli('localhost', 'root', 'Password.', 'defaultDB') ;
			if($Link->connect_error){
				self::logDBerror($Link->connect_error) ;
				die('Får ikke kontakt med server, sorry brwaa') ;
			}//slutt !connect_error  
			$Link->set_charset('utf8') ;
			return $Link ;
		}//slutt dbConnect()
		static function createSeries($occupancy){
			switch($occupancy){
				case 16:
					$brawlCount = 15 ;
					$tierCount = 4 ;
					$participantsPerBrawl = 2 ;
					$ppbReductionAt = null ;
					break ;
				case 32: 
					$brawlCount = 19 ;
					$tierCount = 5 ;
					$participantsPerBrawl = 4 ;
					$ppbReductionAt = 3 ;
					break ;
				case 64:
					$brawlCount = 32 ;
					$tierCount = 6 ;
					$participantsPerBrawl = 4 ;
					$ppbReductionAt = 5 ;
				break ;
			}//slutt switch
			$katakanaArray = array('カ','キ','ク','ケ', 'コ', 'サ', 'シ', 'ス', 'セ', 'ソ', 'タ', 'チ', 'ツ', 'テ', 'ト', 'ナ', 'ニ', 'ヌ', 'ネ', 'ノ', 'ハ', 'ヒ', 'フ', 'ヘ', 'ホ', 'マ', 'ミ', 'ム', 'メ', 'モ','ヤ', 'ユ', 'ヨ','ラ', 'リ', 'ル', 'レ', 'ロ', 'ワ', 'ヲ') ;

			$brawlersLeftStanding = $occupancy ;
			$currentTierBrawls = '' ;
			$eliminationRatio = '' ;
			$insertSQL = 'INSERT INTO Brawl (Tier, Caption) VALUES' ;
			for($i = 1 ; $i <= $tierCount ; $i ++){
				if($occupancy > 16){
					if($i == $ppbReductionAt){
						$participantsPerBrawl = 2 ;
					}//slutt if
				}//slutt if
				$currentTierBrawls = $brawlersLeftStanding / $participantsPerBrawl ;
				for($j = 0 ; $j < $currentTierBrawls ; $j ++){
					$insertSQL.= '("' . $i . '", "Tier ' . $i ; 
					if($currentTierBrawls == 1){
						$insertSQL.= ', ファイナル！！！")' ;
					}else{
						$insertSQL.= ', Brawl-' . $katakanaArray[$j] . '")' ;
					}//slutt if finale
					if($j <= ($currentTierBrawls - 2)){
						$insertSQL.=', ' ;
					}//slutt if 
				}//slutt for
				if($i != $tierCount){
					$insertSQL.= ', ' ;
				}//slutt if
				$brawlersLeftStanding = $brawlersLeftStanding / 2 ; 
			}//slutt for
			$insertBrawlsOK = true ;
			$insertBrawlsLink = self::dbConnect() ;
			$insertBrawlsLink->autocommit(false) ;
			$insertBrawlsResult = $insertBrawlsLink->query($insertSQL) ;
			if(!$insertBrawlsResult){
				self::logDBerror($insertBrawlsLink->error) ;
				echo 'Insert Trøbbellll, zomg' ;
				$insertBrawlsLink->rollback() ;
				$insertBrawlsOK = false ;
			}else{
				$insertBrawlsLink->commit() ;
			}//slutt ifelse
			$insertBrawlsLink->close() ;
			return $insertBrawlsOK ;
		}//slutt createSeries()
		
		static function needMoreBrawlers($occupancyGoal){
			$countBrawlers = self::numberOfBrawlers() ;
			$neededBrawlers = $occupancyGoal - $countBrawlers ;
			if($neededBrawlers <= 0){
				return false ;
			}//slutt 
			$insertBotsSQL = 'INSERT INTO Contestant (Nick, Email, SSBChar) VALUES' . "\n";
			for($i = 1 ; $i <= $neededBrawlers ; $i ++){
				$insertBotsSQL.= '("SmashBot #' . $i .'", "botmail' . $i . '@notlive.com", "999")' ;
				if($i < ($neededBrawlers)){
					$insertBotsSQL.= ', ' ;
				}//if
				$insertBotsSQL.= "\n" ;
			}//for $i
			$link = self::dbConnect() ;
			$link->autocommit(false) ;
			$insertBotsResult = $link->query($insertBotsSQL) ;
			if(!$insertBotsResult){
				self::logDBerror($link->error) ;
				$link->rollback() ;
				$link->close() ;
				die('result ; Fikk ikke lagt inn bots') ;
			}//slutt
			if($link->affected_rows != $neededBrawlers){
				$link->rollback() ;
				$link->close() ;
				die('affected_rows ; Fikk ikke lagt inn bots') ;
			}//slutt
			
			$link->commit() ;
			$link->close() ;
			return true ;
		}//slutt needMoreBrawlers($occupancyGoal)
		
		static function populateTierOne($occupancy){
			$countBrawlers = self::numberOfBrawlers() ;
			$brawlerNeeded = $occupancy - $countBrawlers ;
			if($brawlerNeeded != 0){
				$brawlerNeeded = self::needMoreBrawlers($occupancy) ;
				$countBrawlers = self::numberOfBrawlers() ;
			}//slutt if
				
			$getBrawlersSQL = 'SELECT ContestantID, SSBChar FROM Contestant' ;
			$getBrawlsSQL = 'SELECT BrawlID FROM Brawl WHERE Tier = "1"' ;
			$insertContestantBrawlSQL = 'INSERT INTO ContestantBrawl (BrawlID, ContestantID) VALUES' ."\n" ;
	
			$link = self::dbConnect() ;			
			$getBrawlerResult = $link->query($getBrawlersSQL);
			if(!$getBrawlerResult){
				self::logDBerror($link->error) ;
				die('Gikk ikke å hente deltagere') ;
			}//slutt
			
			$brawlerIDArray = array() ;
			$brawlerCharArray = array() ;
			$brawlerArray = array() ;
			$candidateNumbersUsed = array() ;
			$ssbCharUsed = array() ;
			$brawlerAffectedRows = $link->affected_rows ;
			for($i = 0 ; $i < $brawlerAffectedRows ; $i ++){
				$brawlerObject = $getBrawlerResult->fetch_object() ;
				$brawlerArray[$i] = new Contestant() ;
				$brawlerArray[$i]->setID($brawlerObject->ContestantID) ;
				$brawlerArray[$i]->setSSBChar($brawlerObject->SSBChar) ;
				$ssbCharUsed[$brawlerObject->SSBChar] = 0 ;
			}//slutt
			
			$brawlerIndex = (count($brawlerArray)-1) ;
			$getBrawlResult = $link->query($getBrawlsSQL) ;
			if(!$getBrawlResult){
				self::logDBerror($link->error) ;
				die('Gikk ikke å hente kamper') ;
			}//slutt
			$brawlArray = array() ;
			$affectedRows = $link->affected_rows ;
			for($i = 0 ; $i < $affectedRows ; $i ++){
				$brawlObject = $getBrawlResult->fetch_object() ;
				$brawlArray[$brawlObject->BrawlID]['brawlers'] = array()  ;
			}//slutt for 

			foreach($brawlArray as $key=>$brawlID){
				$k = 0 ; 
				while($k < 4){
					newCandidateNumber:
					$candidateNumber = rand(0, $brawlerIndex) ;
					$useThisCandidateNumber = true ;
					if(!in_array($candidateNumber,$candidateNumbersUsed)){
						foreach($brawlArray[$key]['brawlers'] as $compareObject){
							$compareChar = $compareObject->getSSBChar() ;
							$candidateChar = $brawlerArray[$candidateNumber]->getSSBChar() ;
							if($compareChar == $candidateChar){
/*								if($ssbCharUsed[$candidateChar] < 8){
									$useThisCandidateNumber = false ;
							}//slutt charUsed ;
*/							}//slutt if compareChar
						}//slutt foreach $compareObject
					}else{
						$useThisCandidateNumber = false ;
					}//slutt ifelse !array_search
					if($useThisCandidateNumber){
						$candidateChar = $brawlerArray[$candidateNumber] ;
						$ssbCharUsed[$candidateChar->getSSBChar()] = ($ssbCharUsed[$candidateChar->getSSBChar()] + 1); 
						$candidateNumbersUsed[] = $candidateNumber ;
/*						echo '<h1>Tildeler nå følgende kandidat!</h1>' ;
						echo '<pre>', var_dump($brawlerArray[$candidateNumber]), '</pre>' ;
*/						
						$brawlArray[$key]['brawlers'][$k]= $candidateChar ;
						 
					}else{
						goto newCandidateNumber ;
					}//slutt ifelse useThisCandidateNumber	
					$insertContestantBrawlSQL .= '("' . $key . '", "' . $candidateChar->getID() . '")' ;
					if(count($candidateNumbersUsed) <= 31){
						$insertContestantBrawlSQL.= ', ' ;
					}//slutt
					$insertContestantBrawlSQL.= "\n" ;
					$k ++ ;
				}//slutt while
			}//slutt foreach
			$link->autocommit(false) ;
			$insertContestantBrawlResult = $link->query($insertContestantBrawlSQL) ;
			
			if(!$insertContestantBrawlResult){
				self::logDBerror($link->error) ;
				$link->rollback() ;
				$link->close() ;
				return false ;
			}//if !result
			
			$link->commit() ;
			$link->close() ;
			return true ; 
/*			var_dump($ssbCharUsed) ;
			echo '<h1>Antall påmeldte tildelt kamper: ', count($candidateNumbersUsed), '</h1>' ;
			echo '<pre>', var_dump($brawlArray),'</pre>' ;
*/		}//slutt populateTierOne()

		static function numberOfTierMatches($Tier){
			$countSQL = 'SELECT COUNT(*) AS "Brawls" FROM Brawl WHERE Tier ="' . $Tier .'"' ;
			$link = self::dbConnect() ;
			$countResult = $link->query($countSQL) ;
			if(!$countResult){
				self::logDBerror($link->error) ;
			}//slutt !countResult
			
			$countObject = $countResult->fetch_object() ;
			return $countObject->Brawls ;
		}//slutt numberOfTierMatches
		
		static function getTierMatches($tier, $populated = true, $spaces = 4){
			$link = self::dbConnect() ;
			
			$selectTierMatchesSQL = 'SELECT BrawlID, Caption FROM Brawl WHERE Tier = "'. $tier .'"' ;
			$selectTierMatchesResult = $link->query($selectTierMatchesSQL) ;
			if(!$selectTierMatchesResult){
				self::logDBerror($link->error) ;
				die('Kunne ikke hente matchinfo') ;
			}//slutt
			$selectTierMatchesAffectedRows = $link->affected_rows ;
			$tierMatch = array() ;
			for($i = 0 ; $i < $selectTierMatchesAffectedRows ; $i ++){
				$forObject = $selectTierMatchesResult->fetch_object() ;
				$tierMatch[$forObject->BrawlID] = $forObject->Caption  ;
			}//slutt for $i
			$table = '' ;
			foreach($tierMatch as $matchNumber=>$caption){
				$table.= '<table border="1">' ;
				$table.= '<caption>' . $caption . '</caption>' ;
				$table.= '<tr>' ;
				$table.= '<th>Nick</th><th>Character</th><th>Character in Moonspeak</th>' ;
				$table.= '</tr>' ;
				if($populated){
					$selectTierBrawlersSQL = 'SELECT C.Nick, S.EngName, S.JapName ' ; 
					$selectTierBrawlersSQL.= 'FROM Contestant AS C JOIN SSBChar AS S ON C.SSBChar = S.SSBCharID ' ;
					$selectTierBrawlersSQL.= 'WHERE C.ContestantID IN (SELECT ContestantID FROM ContestantBrawl WHERE BrawlID = "' .$matchNumber .'")' ; 
					$selectTierBrawlersResult = $link->query($selectTierBrawlersSQL) ;
					if(!$selectTierBrawlersResult){
						self::logDBerror($link->error) ;
						die('Får ikke hentet drittsekker') ;
					}//slutt !result
					$selectTierBrawlersAffectedRows = $link->affected_rows ;
					for($j = 0 ; $j < $selectTierBrawlersAffectedRows ; $j ++){
						$forObject = $selectTierBrawlersResult->fetch_object() ;
						$table.= '<tr>' ;
						$table.= '<td>' . $forObject->Nick . '</td><td>' . $forObject->EngName . '</td><td>' . $forObject->JapName . '</td>' ; 
						$table.= '</tr>' ;
					}//for $j
				}else{
					$previousTier = $tier - 1 ;
					for($j = 0 ; $j < $spaces ; $j ++){
						$table.= '<tr>' ;
						$table.= '<td colspan="3">Tier ' . $previousTier . ' Winner/Runnerup</td>' ; 
						$table.= '</tr>' ;
					}//for $j
				}//slutt ifelse populated
				$table.= '</table>' ;
			}//slutt foreach
			echo $table ;
			$link->close() ;
		}//slutt getTierMatches
		
		static function lastOneIn(){
			$lastOneInSQL = 'SELECT C.ContestantID AS "BrawlerID", S.EngName AS "BrawlerChar", C.Nick AS "BrawlerNick" ' ;
			$lastOneInSQL.= 'FROM Contestant AS C JOIN SSBChar AS S ' ;
			$lastOneInSQL.= 'ON C.SSBChar = S.SSBCharID ' ;
			$lastOneInSQL.= 'ORDER BY BrawlerID DESC ' ;
			$lastOneInSQL.= 'LIMIT 1' ;
			
			$lastOneInLink = self::dbConnect() ;
			
			$lastOneInResult = $lastOneInLink->query($lastOneInSQL) ;
			
			if(!$lastOneInResult){
				self::logDBerror($lastOneInLink->error) ;
			}else{
				$lastOneInObject = $lastOneInResult->fetch_object() ;
				$returnOne = $lastOneInObject->BrawlerNick . ' (' . $lastOneInObject->BrawlerChar . ')' ;
				return $returnOne ;
			}//slutt ifelse !lastOneInResult
		}//slutt lastOneIn()
		
		static function charaNinki(){
			$charaNinkiSQL = 'SELECT S.EngName AS "EngName", COUNT(*) AS "BrawlerCount" ' ;
			$charaNinkiSQL.= 'FROM Contestant AS C JOIN SSBChar AS S ' ;
			$charaNinkiSQL.= 'ON C.SSBChar = S.SSBCharID ' ;
			$charaNinkiSQL.= 'GROUP BY S.EngName ' ;
			$charaNinkiSQL.= 'ORDER BY BrawlerCount DESC ' ;
			$charaNinkiSQL.= 'LIMIT 1' ;
			
			$charaNinkiLink = self::dbConnect() ;
			
			$charaNinkiResult = $charaNinkiLink->query($charaNinkiSQL) ;
			
			if(!$charaNinkiResult){
				self::logDBerror($charaNinkiLink->error) ;
			}else{
				$charaNinkiObject = $charaNinkiResult->fetch_object() ;
				return $returnNinki = $charaNinkiObject->EngName . ' (' . $charaNinkiObject->BrawlerCount . ')' ;
			}//slutt !charaNinkiResult
		}//slutt charaNinki()
		
		static function numberOfBrawlers(){
			$countBrawlersSQL = 'SELECT COUNT(*) AS "Brawlers" FROM Contestant' ;
			$countLink = self::dbConnect() ;
			$countResult = $countLink->query($countBrawlersSQL) ;
			if(!$countResult){
				self::logDBerror($countLink->error) ;
			}else{
				$object = $countResult->fetch_object() ;
				return $object->Brawlers ;
			}//slutt !countResult()
		}//slutt numberOfBrawlers()
		static function seriesCreated(){
			$seriesCheckOK = false ;
			$seriesCheckSQL = 'SELECT COUNT(*) FROM Brawl' ;
			
			$link =self::dbConnect() ;
			$seriesCheckResult = $link->query($seriesCheckSQL) ;
			if($link->affected_rows > 0){
				$seriesCheckOK = true ;
			}//slutt
			return $seriesCheckOK ;
		}//slutt
				
		static function tableOfBrawlers(){
			$tableOfBrawlersSQL = 'SELECT S.EngName AS "EngName", S.JapName AS "JapName", S.SSBWiki AS "SSBWiki", COUNT(*) AS "BrawlerCount" ' ;
			$tableOfBrawlersSQL.= 'FROM Contestant AS C JOIN SSBChar AS S ' ;
			$tableOfBrawlersSQL.= 'ON C.SSBChar = S.SSBCharID ' ;
			$tableOfBrawlersSQL.= 'GROUP BY S.EngName' ;
			$tableOfBrawlers = '<table border="1">' . "\n" ;
			$tableOfBrawlers.= '<tr>' . "\n" ;
			$tableOfBrawlers.= '<th>Karakternavn</th><th>Japansknavn</th><th>SSBWiki</th><th>Påmeldte</th>' . "\n" ;
			$tableOfBrawlers.= '</tr>' . "\n" ;
			$tableOfBrawlersLink = self::dbConnect() ;
			$tableOfBrawlersResult = $tableOfBrawlersLink->query($tableOfBrawlersSQL) ;
			
			if(!$tableOfBrawlersResult){
				self::logDBerror($tableOfBrawlersLink->error) ;
				$tableOfBrawlers.= '<tr>' ."\n" ;
				$tableOfBrawlers.= '<td colspan="4">Fikk en feilmelding!</td>' . "\n" ;
				$tableOfBrawlers.= '</tr>' . "\n" ;
			}else{
				$affectedRows = $tableOfBrawlersLink->affected_rows ;
				if($affectedRows > 0){
					$brawlerCount = 0 ;
					for($i = 0 ; $i < $affectedRows ; $i ++){
						$tableObject = $tableOfBrawlersResult->fetch_object() ;
						$brawlerCount += $tableObject->BrawlerCount ;
						$tableOfBrawlers.= '<tr>' . "\n" ;
						$tableOfBrawlers.= '<td>' . $tableObject->EngName . '</td><td>' . $tableObject->JapName . '</td>' ;
						$tableOfBrawlers.= '<td><a href="http://www.ssbwiki.com/' . $tableObject->SSBWiki . '" target="_blank">' . $tableObject->SSBWiki . '</a></td>' ;
						$tableOfBrawlers.= '<td>' . $tableObject->BrawlerCount . '</td>' . "\n" ;
						$tableOfBrawlers.= '</tr>' . "\n" ;
					}//slutt for
					$tableOfBrawlers.= '<tr>' . "\n" ;
					$tableOfBrawlers.= '<th colspan="3">Totalt antall påmeldte:</th><th>' . $brawlerCount . '</th>' . "\n" ;
					$tableOfBrawlers.= '</tr>' . "\n" ;
				}else{
					$tableOfBrawlers.= '<tr>' ."\n" ;
					$tableOfBrawlers.= '<td colspan="4">Fant ingen påmeldte!</td>' . "\n" ;
					$tableOfBrawlers.= '</tr>' . "\n" ;
				}//slutt ifelse affectedRows
			}//slutt !tableOfBrawlersResult
			$tableOfBrawlers.= '</table>' . "\n" ;
			return $tableOfBrawlers ;
		}//slutt tableOfBrawlers()
		static function adminTierOptions($Tier){
			$tierOptionSQL = 'SELECT BrawlID, Caption FROM Brawl WHERE Tier = "' . $Tier .'"' ;
			$link = self::dbConnect() ;
			$tierOptionResult = $link->query($tierOptionSQL) ;
			if(!$tierOptionResult){
				self::logDBerror($link->error) ;
				die('trøbbel med å laste kamper her azzzz') ;
			}//slutt !result
			$affectedRows = $link->affected_rows ;
			if($affectedRows == 0){
				echo '<option>Fant ingen brawls</option>' ;
			}else{
				for($i = 0 ; $i < $link->affected_rows ; $i ++){
					$rowObject = $tierOptionResult->fetch_object() ;
					echo '<option value="' . $rowObject->BrawlID . '">' . $rowObject->Caption . '</option>' ;
				}//slutt for $i
			}//slutt else	
			$link->close() ;
		}//slutt adminTierOptions()
		static function characterOptions(&$wikiList = null){
			$selectCharSQL = 'SELECT SSBCharID, EngName, JapName, SSBWiki FROM SSBChar' ;
			$link = self::dbConnect() ;

			$selectCharResult = $link->query($selectCharSQL) ;
			if(!$selectCharResult){
				self::logDBerror($link->error) ;
			}else{
				$affectedRows = $link->affected_rows ;
				if($affectedRows == 0){
					echo '<option>Finner ingen karakterer, prøve igjen senere</option>' ;
				}else{
					$list = '<ul class="hidden" id="wikiList">' . "\n" ;
					for($i = 0 ; $i < $affectedRows ; $i ++){
						$rowObject = $selectCharResult->fetch_object() ;
						if($rowObject->SSBCharID != 999){
							echo '<option value="', $rowObject->SSBCharID, '">', $rowObject->EngName, ', (', $rowObject->JapName, ')</option>', "\n" ;
							$list.= '<li id="wl' . $rowObject->SSBCharID . '">' . $rowObject->SSBWiki . '</li>' . "\n" ;
						}//slutt if != 999	
					}//slutt for
					$list.= '</ul>' . "\n" ;
				}//slutt if/else $affectedRows
			}//slutt if/else !selectCharResult
			$link->close() ;
			$wikiList = $list ;
		}//slutt characterOptions
		
		static function retrieveCharacterById($ID){
			$selectCharByIdSQL = 'SELECT EngName, JapName, SSBWiki FROM SSBChar WHERE SSBCharID = "' . $ID . '"' ;
			$link = self::dbConnect() ;
			$selectCharByIdResult = $link->query($selectCharByIdSQL) ;
			$link->close() ;
			if(!$selectCharByIdResult){
				self::logDBerror($link->error) ;
			}else{
				$characterObject = $selectCharByIdResult->fetch_object() ;
				return $characterObject ;
			}//slutt if/else !selectCharByIdResult	
		}//slutt retrieveCharacterById
		
		static function regBrawler($contestantObject){
			$insertSQL = 'INSERT INTO Contestant (Nick, Email, SSBChar) VALUES ("' . $contestantObject->getNick() . '", "' . $contestantObject->getEmail() . '", "' . $contestantObject->getSSBChar() . '")' ;
			$ok = true ;
			$link = self::dbConnect() ;
		
			$result = $link->query($insertSQL) ;
			if($link->error){
				self::logDBerror($link->error) ;
				$ok = false ;
			}
			$link->close() ;
			return $ok ;
		}//slutt regBrawler
		
		static function brawlerExists($column, $value){
			$brawlerExists = false ;
			$brawlerExistsSQL = 'SELECT ContestantID FROM Contestant WHERE ' ;
			switch($column){
				case 'Nick':
					$brawlerExistsSQL.= 'Nick ' ;
					break ;
				case 'Email':
					$brawlerExistsSQL.= 'Email ' ;
					break ;
			}//slutt switch
			$brawlerExistsSQL.= '= "' . $value . '"' ;
			$link = self::dbConnect() ;
			$brawlerExistsResult = $link->query($brawlerExistsSQL) ;
			if(!$brawlerExistsResult){
				self::logDBerror($link->error) ;
				echo 'Vi har problemer med databasegreiene våre azzz!' ;
			}else{
				$affectedRows = $link->affected_rows ;
				if($affectedRows > 0){
					$brawlerExists = true ;
				}//slutt if affectedRows
			}//slutt if/else !brawlerExistsResult
			$link->close() ;
			return $brawlerExists ;
		}//slutt brawlerExists() 
	}//slutt DBhandler
?>
