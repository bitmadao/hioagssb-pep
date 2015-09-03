DROP TABLE IF EXISTS ContestantBrawl ;
DROP TABLE IF EXISTS Brawl ;
DROP TABLE IF EXISTS Contestant ;
DROP TABLE IF EXISTS SSBChar ;
CREATE TABLE SSBChar (
	SSBCharID INT,
	EngName VARCHAR(50),
	JapName VARCHAR(50),
	SSBWiki VARCHAR(20),
	PRIMARY KEY (SSBCharID))ENGINE = InnoDB DEFAULT CHARSET="utf8";
CREATE TABLE Brawl (
	BrawlID INT AUTO_INCREMENT,
	Tier INT,
	Caption VARCHAR(20),
	PRIMARY KEY (BrawlID))ENGINE = InnoDB DEFAULT CHARSET="utf8";
CREATE TABLE Contestant (
	ContestantID INT PRIMARY KEY AUTO_INCREMENT, 
	Nick VARCHAR(20),
	Email VARCHAR(20),
	SSBChar INT,
	CONSTRAINT fk_Contestant_I
		FOREIGN KEY (SSBChar)
		REFERENCES SSBChar (SSBCharID))ENGINE = InnoDB DEFAULT CHARSET="utf8";
CREATE TABLE ContestantBrawl (
	BrawlID INT NOT NULL,
	ContestantID INT NOT NULL,
	Ranking INT,
	PRIMARY KEY (ContestantID, BrawlID),
	CONSTRAINT fk_ContestantBrawl_I
		FOREIGN KEY (ContestantID)
		REFERENCES Contestant (ContestantID),
	CONSTRAINT fk_ContestantBrawl_II
		FOREIGN KEY (BrawlID)
		REFERENCES Brawl (BrawlID))ENGINE = InnoDB;
INSERT INTO SSBChar (SSBCharID, EngName, JapName, SSBWiki) VALUES
	("101", "Bowser", "Koopa/クッパ", "Bowser"),
	("102", "Bowser Jr.", "Koopa Jr./クッパJr.", "Bowser_Jr."),
	("103", "Captain Falcon", "キャプテン・ファルコン", "Captain_Falcon"),
	("104", "Charizard", "Lizardon/リザードン", "Charizard"),
	("105", "Dark Pit", "Black Pit/ブラックピット", "Dark_Pit"),
	("106", "Diddy Kong", "ディディーコング", "Diddy_Kong"),
	("107", "Donkey Kong","ドンキーコング", "Donkey_Kong"),
	("108", "Dr. Mario", "ドクターマリオ", "Dr._Mario"),
	("109", "Duck Hunt", "ダックハント", "Duck_Hunt"),
	("110", "Falco", "ファルコ", "Falco"),
	("111", "Fox", "フォックス", "Fox"),
	("112", "Ganondorf", "ガノンドロフ", "Ganondorf"),
	("113", "Greninja", "Gekkouga/ゲッコウガ", "Greninja"),
	("114", "Ike", "アイク", "Ike"),
	("115", "Jigglypuff", "Purin/プリン", "Jigglypuff"),
	("116", "King Dedede", "Dedede/デデデ", "Dedede"),
	("117", "Kirby", "カービィ", "Kirby"),
	("118", "Link", "リンク", "Link"),
	("119", "Little Mac", "リトル・マック", "Little_Mac"),
	("120", "Lucario", "ルカリオ", "Lucario"),
	("121", "Lucina", "ルキナ", "Lucina"),
	("122", "Luigi", "ルイージ", "Luigi"),
	("123", "Mario", "マリオ", "Mario"),
	("124", "Marth", "マルス", "Marth"),
	("125", "Mega Man", "ロックマン", "Mega_Man"),
	("126", "Meta Knight", "メタナイト", "Meta_Knight"),
	("127", "Mr. Game & Watch", "Mr.ゲーム＆ウォッチ", "Mr._Game_%26_Watch"),
	("128", "Ness", "ネス", "Ness"),
	("129", "Pac-Man", "パックマン", "Pac-Man"),
	("130", "Palutena", "パルテナ", "Palutena"),
	("131", "Peach", "ピーチ", "Princess_Peach"),
	("132", "Pikachu", "ピカチュウ", "Pikachu"),
	("133", "Pikmin & Olimar", "ピクミン＆オリマー", "Captain_Olimar"),
	("134", "Pit", "ピット", "Pit"), 
	("135", "R.O.B.", "Robot/ロボット", "R.O.B."),
	("136", "Robin", "Reflet/ルフレ", "Robin"),
	("137", "Rosalina and Luma", "Rosetta & Chiko/ロゼッタ＆チコ", "Rosalina"),
	("138", "Samus", "サムス", "Samus"),
	("139", "Sheik", "シーク", "Sheik"),
	("140", "Shulk", "シュルク", "Shulk"),
	("141", "Sonic", "ソニック", "Sonic"),
	("142", "Toon Link", "トゥーンリンク", "Toon_Link"),
	("143", "Villager", "むらびと", "Villager"),
	("144", "Wario", "ワリオ", "Wario"),
	("145", "Wii Fit Trainer", "Wii Fit トレーナー", "Wii_Fit_Trainer"),
	("146", "Yoshi", "ヨッシー", "Yoshi"),
	("147", "Zelda", "ゼルダ", "Zelda"),
	("148", "Zero Suit Samus", "ゼロスーツサムス", "Zero_Suit_Samus") ;
