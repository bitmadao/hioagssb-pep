DROP TABLE IF EXISTS ContestantBrawl ;
DROP TABLE IF EXISTS Brawl ;
CREATE TABLE Brawl (
	BrawlID INT AUTO_INCREMENT,
	Tier INT,
	Caption VARCHAR(20),
	PRIMARY KEY (BrawlID))ENGINE = InnoDB DEFAULT CHARSET="utf8";
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
