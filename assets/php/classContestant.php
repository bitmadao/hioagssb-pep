<?php
	class Contestant{
		public $ID ;
		private $Nick ;
		private $Email ;
		private $SSBChar ;
		
		function setID($innID){
			$this->ID = $innID ;
		}// slutt setID
		function getID(){
			return $this->ID ;
		}// slutt getID
		
		function setNick($innNick){
			$this->Nick = $innNick ;
		}// slutt setNick
		function getNick(){
			return $this->Nick ;
		}//slutt getNick
		
		function setEmail($innEmail){
			$this->Email = $innEmail ;
		}//slutt setEmail
		function getEmail(){
			return $this->Email ;
		}//slutt getEmail
		
		function setSSBChar($innChar){
			$this->SSBChar = $innChar ;
		}//slutt setSSBChar
		function getSSBChar(){
			return $this->SSBChar ;
		}//slutt getSSBChar 
	}
?>
