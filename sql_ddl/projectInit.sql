DROP TABLE PatronPlays;
DROP TABLE CasinoServes;
DROP TABLE GameSession;
DROP TABLE Patron;
DROP TABLE Party;
DROP TABLE Dealer;
DROP TABLE Game;
DROP TABLE CasinoTable;
DROP TABLE Room;
DROP TABLE CasinoBranch;

DROP SEQUENCE dealer_id;
DROP SEQUENCE party_id;
DROP SEQUENCE patron_id;
DROP SEQUENCE account_id;
DROP SEQUENCE session_id;

CREATE SEQUENCE dealer_id START WITH 1;
CREATE SEQUENCE party_id START WITH 1;
CREATE SEQUENCE patron_id START WITH 1;
CREATE SEQUENCE account_id START WITH 1;
CREATE SEQUENCE session_id START WITH 1;

CREATE TABLE CasinoBranch(
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
dealerID INTEGER DEFAULT dealer_id.nextval,
name VARCHAR(30),
homeAddress VARCHAR(30),
wage INTEGER,
PRIMARY KEY(dealerID),
UNIQUE(name, homeAddress));

CREATE TABLE Party(
partyID INTEGER DEFAULT party_id.nextval,
accountNum INTEGER DEFAULT account_id.nextval,
balance INTEGER,
roomNum INTEGER,
partySize INTEGER DEFAULT 0,
startDate DATE,
endDate DATE,
PRIMARY KEY(partyID),
UNIQUE(accountNum),
FOREIGN KEY (roomNum) REFERENCES Room(roomNum) ON DELETE
 SET NULL);

CREATE TABLE Patron(
patronID INTEGER DEFAULT patron_id.nextval,
name VARCHAR(30),
homeAddress VARCHAR(50),
partyID INTEGER NOT NULL,
PRIMARY KEY(patronID),
UNIQUE(name, homeAddress),
FOREIGN KEY (partyID) REFERENCES Party(partyID) ON DELETE
 CASCADE);


CREATE TABLE GameSession (
	sessionID INTEGER DEFAULT session_id.nextval,
	startTime DATE,
	endTime DATE,
	gameName VARCHAR(30) NOT NULL,
	gameVariant VARCHAR(30) NOT NULL,
	dealerID INTEGER,
	branchName VARCHAR(30) NOT NULL,
	tableNum INTEGER NOT NULL,
	casinoGain INTEGER,
    PRIMARY KEY(sessionID),
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


--INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES();
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(dealer_id.nextval, 'Jack Black', '1300 10th Avenue', 78000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(dealer_id.nextval, 'Saul Goodman', '123 Main Street', 75000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(dealer_id.nextval, 'Jane Etor', '5846 84 Lane', 36000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(dealer_id.nextval, 'Stuart Maxwell', '846 Haney Road', 74684);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(dealer_id.nextval, 'Petra Douglas', '68464 Reed Road', 34884);

--INSERT INTO Game(name, variant, numPlayers) VALUES();
INSERT INTO Game(name, variant, numPlayers) VALUES('Poker', 'Texas Holdem', 8);
INSERT INTO Game(name, variant, numPlayers) VALUES('Poker', 'Base', 8);
INSERT INTO Game(name, variant, numPlayers) VALUES('Blackjack', 'Base', 8);
INSERT INTO Game(name, variant, numPlayers) VALUES('LuckySlots', 'Base', 1);
INSERT INTO Game(name, variant, numPlayers) VALUES('Roulette', 'Base', 100);
INSERT INTO Game(name, variant, numPlayers) VALUES('Roulette', 'Russian', 8);

--INSERT INTO CasinoBranch(name, balance) VALUES();
INSERT INTO CasinoBranch(name, balance) VALUES('River Rock Richmond', 18000000);
INSERT INTO CasinoBranch(name, balance) VALUES('North Shore Gaming', 3000000);
INSERT INTO CasinoBranch(name, balance) VALUES('Ace of Spades', 80000000);
INSERT INTO CasinoBranch(name, balance) VALUES('Vegas of the North', 136000000);
INSERT INTO CasinoBranch(name, balance) VALUES('Downtown Eastside', 36000);

--INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES();
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(1, 20, 'River Rock Richmond');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(2, 15, 'River Rock Richmond');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(1, 4, 'North Shore Gaming');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(1, 9, 'Ace of Spades');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(1, 13, 'Vegas of the North');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(2, 15, 'Vegas of the North');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(3, 18, 'Vegas of the North');
INSERT INTO CasinoTable(tableNum, capacity, branchName) VALUES(1, 1, 'Downtown Eastside');

--INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES();
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(105, 95, 'River Rock Richmond');
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(106, 110, 'River Rock Richmond');
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(201, 77, 'North Shore Gaming');
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(301, 90, 'Ace of Spades');
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(401, 78, 'Vegas of the North');
INSERT INTO Room(roomNum, nightlyCost, branchName) VALUES(501, 45, 'Downtown Eastside');

--INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) VALUES();
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(party_id.nextval, 3, account_id.nextval, 200, 105, TO_DATE('14-SEP-2020', 'DD-MON-YYYY'), TO_DATE('15-SEP-2020', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(party_id.nextval, 2, account_id.nextval, 68, 106, TO_DATE('15-SEP-2020', 'DD-MON-YYYY'), TO_DATE('20-SEP-2020', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(party_id.nextval, 1, account_id.nextval, 235, 201, TO_DATE('12-JAN-2021', 'DD-MON-YYYY'), TO_DATE('14-JAN-2021', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(party_id.nextval, 1, account_id.nextval, 864, 401, TO_DATE('8-AUG-2022', 'DD-MON-YYYY'), TO_DATE('12-AUG-2022', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(party_id.nextval, 1, account_id.nextval, 5465, 501, TO_DATE('3-DEC-2022', 'DD-MON-YYYY'), TO_DATE('8-DEC-2022', 'DD-MON-YYYY'));

	--INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES ();
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Ice Spice', '846 88 Street', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Angela Zeigler', '52 34 Avenue', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Adam Morrison', '86464 Place Place', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Robyn Lorant', '8984 Park Avenue', 2);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Carson Buruda', '454 Board Stroll', 2);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Simon Clark', '84 86846 55 Street', 3);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Ryan Todd', '468 General Avenue', 4);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (patron_id.nextval, 'Mai Li', '98765 Avenue Boulevard', 5);

--INSERT INTO GameSession(sessioID, startTime, endTime, gameName, gameVariant, dealerID, branchName, tableNum, casinoGain) VALUES();
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('15-SEP-2020 21:00:00', 'DD-MON-YYYY HH24:MI:SS'), TO_DATE('16-SEP-2020 00:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Base', 1, 'River Rock Richmond', 1, 200);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('15-SEP-2020 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('15-SEP-2020 14:30:00', 'DD-MON-YYYY hh24:mi:ss'), 'Blackjack', 'Base', 2, 'River Rock Richmond', 2, -80);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('14-JAN-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-JAN-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Texas Holdem', 4, 'North Shore Gaming', 1, 10000);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('11-AUG-2022 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('11-AUG-2022 14:30:00', 'DD-MON-YYYY hh24:mi:ss'), 'LuckySlots', 'Base', 1, 'Vegas of the North', 1, 5000);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('10-DEC-2022 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('10-DEC-2022 12:15:00', 'DD-MON-YYYY hh24:mi:ss'), 'Blackjack', 'Base', 2, 'Downtown Eastside', 1, 0);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('10-DEC-2022 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('10-DEC-2022 12:15:00', 'DD-MON-YYYY hh24:mi:ss'), 'LuckySlots', 'Base', 1, 'Downtown Eastside', 1, 50);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('14-JAN-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-JAN-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Roulette', 'Russian', 1, 'North Shore Gaming', 1, 30);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('14-FEB-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-MAR-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Base', 1, 'Ace of Spades', 1, 500);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('15-MAR-2022 23:20:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('15-MAR-2021 23:25:00', 'DD-MON-YYYY hh24:mi:ss'), 'Roulette', 'Russian', 2, 'Downtown Eastside', 1, -2500);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('22-APR-2020 12:20:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('24-APR-2020 23:15:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Base', 2, 'Ace of Spades', 1, 5);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('15-AUG-2022 15:15:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('15-AUG-2022 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Texas Holdem', 2, 'Vegas of the North', 2, 10);
INSERT INTO GameSession
	VALUES(session_id.nextval, TO_DATE('03-MAR-2023 23:20:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('04-MAR-2023 01:20:00', 'DD-MON-YYYY hh24:mi:ss'), 'Roulette', 'Base', 2, 'North Shore Gaming', 1, 23);
	

--INSERT INTO CasinoServes(partyID, branchName) VALUES();
INSERT INTO CasinoServes(partyID, branchName) VALUES(1, 'River Rock Richmond');
INSERT INTO CasinoServes(partyID, branchName) VALUES(2, 'River Rock Richmond');
INSERT INTO CasinoServes(partyID, branchName) VALUES(3, 'North Shore Gaming');
INSERT INTO CasinoServes(partyID, branchName) VALUES(4, 'Vegas of the North');
INSERT INTO CasinoServes(partyID, branchName) VALUES(5, 'Downtown Eastside');

--INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES();
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(1, 4, 25);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(1, 5, -44);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(2, 1, 33);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(2, 2, 868);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(2, 3, -1000);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(3, 6, 88);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(4, 7, 789);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(5, 5, -1000);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(8, 6, 1500);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(7, 5, 500);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(6, 1, 2000);
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(9, 5, 7000);

COMMIT;