var elForm = document.getElementById('formContestant') ;

var elNick = document.getElementById('formNick') ;
var elNickMsg = document.getElementById('formNickFeedback') ;

var elEmail = document.getElementById('formEmail') ;
var elEmailMsg = document.getElementById('formEmailFeedback') ;

var elSSBChar = document.getElementById('formSSBChar') ;
var elSSBCharMsg = document.getElementById('formSSBCharFeedback') ;

var elWikiList = document.getElementById('wikiList') ;
var elWikiListLength = elWikiList.getElementsByTagName('li').length ;
var wikiArray = [] ;
for(i = 0 ; i < elWikiListLength ; i ++){
	var wikiId = 'wl' + (i + 101) ;
	wikiArray[i] = document.getElementById(wikiId).innerHTML ;
}
function wikiURL(){
	var wikiArrayElement = elSSBChar.value - 101 ;
		elSSBCharMsg.innerHTML = 'Info om karakteren her: <a href="http://www.ssbwiki.com/' + wikiArray[wikiArrayElement] + '" target="_blank">' + wikiArray[wikiArrayElement] +'@SSBWiki.com</a>' ;
}

var elTOC = document.getElementById('formTOC') ;
var elTOCMsg = document.getElementById('formTOCFeedback') ;

function nickTip(){
	elNickMsg.className = 'tip' ;
	elNickMsg.innerHTML = 'Bruk "A-å", "0-9" og "_". Første tegn må være en bokstav.' ;
}
function emailTip(){
	elEmailMsg.className = 'tip' ;
	elEmailMsg.innerHTML = 'Bruk din HiOA-studentepost, dvs. s123456@stud.hioa.no' ;
}
function charTip(){
	elSSBCharMsg.className = 'tip' ;
	if(elSSBChar.value != 'default'){
		wikiURL() ;
	}else{
		elSSBCharMsg.innerHTML = 'Engelsk navn står først, japansk navn oppgis også i bokstaver om det er forskjell på navnene' ;
	}
}

function checkNick(){
	var regExp = new RegExp('^[A-ZÆØÅa-zæøå][A-ZÆØÅa-zæøå0-9_]{1,19}$') ;
	var test = regExp.test(elNick.value) ;
	if(!test){
		elNickMsg.className ='warning' ;
		elNickMsg.innerHTML = 'Første tegn må være en bokstav, nicket må være mellom 2 og 20 tegn. Følgende tegn tillatt: \"<em>A-å, 0-9, _</em>\"' ;
		return false ;
	}else{
		elNickMsg.innerHTML = '' ;
		return true ;
	}
}

function checkEmail(){
	var regExp = new RegExp('^[s][1-9][0-9]{5}@stud\.hioa\.no$') ;
	var test = regExp.test(elEmail.value) ;
	if(!test){
		elEmailMsg.className = 'warning' ;
		elEmailMsg.innerHTML = 'Kun HiOA-studenteposter tillatt' ;
		return false ;
	}else{
		elEmailMsg.innerHTML = '' ;
		return true ;
	}
}

function checkSSBChar(){
	if(elSSBChar.value === 'default'){
		elSSBCharMsg.className = 'warning' ;
		elSSBCharMsg.innerHTML = 'Du må velge en karakter' ;
		return false ;
	}else{
		wikiURL() ;
		return true ;
	}
}

function readyToSubmit(event){
	var readyToSubmit = true ;
	
	var readyNick = checkNick() ;
	if(!readyNick){
		readyToSubmit = false ;
	}
	var readyEmail = checkEmail() ;
	if(!readyEmail){
		readyToSubmit = false ;
	}
	var readySSBChar = checkSSBChar() ;
	if(!readySSBChar){
		readyToSubmit = false ;
	}
	if(!elTOC.checked){
		elTOCMsg.className = 'warning' ;
		elTOCMsg.innerHTML = 'Du må godta brukeravtalen for å kunne registrere deg.' ;
		readyToSubmit = false ;
	}
	if(!readyToSubmit){
		if(event.preventDefault){
			event.preventDefault() ;
		}else{
			event.returnValue = false ;
		}
	}
}

if(elNick.addEventListener){
	elNick.addEventListener('focus', nickTip, false) ;
	elNick.addEventListener('blur', checkNick, false) ;
}else{
	elNick.attachEvent('onfocus', nickTip) ;
	elNick.attachEvent('onblur', checkNick) ;
}

if(elEmail.addEventListener){
	elEmail.addEventListener('focus', emailTip, false) ;
	elEmail.addEventListener('blur', checkEmail, false) ;
}else{
	elEmail.attachEvent('onfocus', emailTip) ;
	elEmail.attachEvent('onblur', checkEmail) ;
}
if(elSSBChar.addEventListener){
	elSSBChar.addEventListener('focus', charTip, false) ;
	elSSBChar.addEventListener('change', checkSSBChar, false) ;
}else{
	elSSBChar.attachEvent('onfocus', charTip) ;
	elSSBChar.attachEvent('onchange', checkSSBChar) ;
}
if(elForm.addEventListener){
	elForm.addEventListener('submit', readyToSubmit, false) ;
}else{
	elForm.attachEvent('onsubmit', readyToSubmit) ;
}
