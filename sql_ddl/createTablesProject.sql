Create Table CasinoBranch(
name VARCHAR(30) PRIMARY KEY,
balance INTEGER);

CREATE TABLE Room (
roomNum INTEGER,
nightlyCost INTEGER,
branchName VARCHAR(30) NOT NULL,
PRIMARY KEY (roomNum),
FOREIGN KEY (branchName) REFERENCES CasinoBranch(name)
ON DELETE CASCADE);


CREATE TABLE CasinoTable(
tableNum INTEGER,
Capacity INTEGER,
branchName VARCHAR(30),
PRIMARY KEY(tableNum, branchName),
FOREIGN KEY (branchName) REFERENCES CasinoBranch(name) ON
 DELETE CASCADE);

CREATE TABLE Game(
name VARCHAR(30),
variant VARCHAR(30),
numPlayers INTEGER,
PRIMARY KEY(name, variant));

CREATE TABLE Dealer(
dealerID INTEGER PRIMARY KEY,
name VARCHAR(30),
homeAddress VARCHAR(30),
wage INTEGER,
UNIQUE(name, homeAddress));

CREATE TABLE Party(
partyID INTEGER PRIMARY KEY,
accountNum INTEGER NOT NULL,
balance INTEGER,
roomNum INTEGER,
partySize INTEGER DEFAULT 0,
startDate DATE,
endDate DATE,
UNIQUE(accountNum),
FOREIGN KEY (roomNum) REFERENCES Room(roomNum) ON DELETE
 SET NULL);

CREATE TABLE Patron(
patronID INTEGER PRIMARY KEY,
name VARCHAR(30),
homeAddress VARCHAR(50),
partyID INTEGER NOT NULL,
UNIQUE(name, homeAddress),
FOREIGN KEY (partyID) REFERENCES Party(partyID) ON DELETE
 CASCADE);


CREATE TABLE GameSession (
	sessionID INTEGER PRIMARY KEY,
	startTime DATE,
	endTime DATE,
	gameName VARCHAR(30) NOT NULL,
	gameVariant VARCHAR(30) NOT NULL,
	dealerID INTEGER,
	branchName VARCHAR(30) NOT NULL,
	tableNum INTEGER NOT NULL,
	casinoGain INTEGER,
	FOREIGN KEY (gameName, gameVariant) REFERENCES Game(name,
 		variant) ON DELETE CASCADE,
	FOREIGN KEY (dealerID) REFERENCES Dealer(dealerID) ON
 		DELETE CASCADE,
	FOREIGN KEY (tableNum, branchName) REFERENCES 
		CasinoTable(tableNum, branchName) ON DELETE CASCADE
);


CREATE TABLE CasinoServes(
partyID INTEGER,
branchName VARCHAR(30),
PRIMARY KEY(partyID, branchName),
FOREIGN KEY (branchName) REFERENCES CasinoBranch(name) ON
 DELETE CASCADE,
FOREIGN KEY (partyID) REFERENCES Party(partyID) ON DELETE 
CASCADE);

CREATE TABLE PatronPlays(
sessionID INTEGER,
patronID INTEGER,
playerGain INTEGER,
PRIMARY KEY(sessionID, patronID),
FOREIGN KEY (sessionID) REFERENCES GameSession(sessionID)
 ON DELETE CASCADE,
FOREIGN KEY (patronID) REFERENCES Patron(patronID) ON
DELETE CASCADE);
