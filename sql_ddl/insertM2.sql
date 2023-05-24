
--INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES();
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(1, 'Jack Black', '1300 10th Avenue', 78000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(2, 'Saul Goodman', '123 Main Street', 75000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(4, 'Jane Etor', '5846 84 Lane', 36000);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(5, 'Stuart Maxwell', '846 Haney Road', 74684);
INSERT INTO Dealer(dealerID, name, homeAddress, wage) VALUES(6, 'Petra Douglas', '68464 Reed Road', 34884);

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
	VALUES(1, 3, 1, 200, 105, TO_DATE('14-SEP-2020', 'DD-MON-YYYY'), TO_DATE('15-SEP-2020', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(2, 2, 2, 68, 106, TO_DATE('15-SEP-2020', 'DD-MON-YYYY'), TO_DATE('20-SEP-2020', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(3, 1, 3, 235, 201, TO_DATE('12-JAN-2021', 'DD-MON-YYYY'), TO_DATE('14-JAN-2021', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(4, 1, 4, 864, 401, TO_DATE('8-AUG-2022', 'DD-MON-YYYY'), TO_DATE('12-AUG-2022', 'DD-MON-YYYY'));
INSERT INTO Party(partyID, partySize, accountNum, balance, roomNum, startDate, endDate) 
	VALUES(5, 1, 5, 5465, 501, TO_DATE('3-DEC-2022', 'DD-MON-YYYY'), TO_DATE('8-DEC-2022', 'DD-MON-YYYY'));

	--INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES ();
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (1, 'Ice Spice', '846 88 Street', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (2, 'Angela Zeigler', '52 34 Avenue', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (3, 'Adam Morrison', '86464 Place Place', 1);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (4, 'Robyn Lorant', '8984 Park Avenue', 2);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (5, 'Carson Buruda', '454 Board Stroll', 2);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (6, 'Simon Clark', '84 86846 55 Street', 3);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (7, 'Ryan Todd', '468 General Avenue', 4);
INSERT INTO Patron(patronID, name, homeAddress, partyID) VALUES (8, 'Mai Li', '98765 Avenue Boulevard', 5);

--INSERT INTO GameSession(sessioID, startTime, endTime, gameName, gameVariant, dealerID, branchName, tableNum, casinoGain) VALUES();
INSERT INTO GameSession
	VALUES(1, TO_DATE('15-SEP-2020 21:00:00', 'DD-MON-YYYY HH24:MI:SS'), TO_DATE('16-SEP-2020 00:00:00', 'DD-MON-YYYY hh24:mi:ss'), 'Poker', 'Base', 1, 'River Rock Richmond', 1, 200);
INSERT INTO GameSession
	VALUES(2, TO_DATE('15-SEP-2020 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('15-SEP-2020 14:30:00', 'DD-MON-YYYY hh24:mi:ss'), 'Blackjack', 'Base', 5, 'River Rock Richmond', 2, -80);
INSERT INTO GameSession
	VALUES(3, TO_DATE('14-JAN-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-JAN-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 4, 'Poker', 'Texas Holdem', 'North Shore Gaming', 1, 10000);
INSERT INTO GameSession
	VALUES(4, TO_DATE('11-AUG-2022 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('11-AUG-2022 14:30:00', 'DD-MON-YYYY hh24:mi:ss'), 1, 'LuckySlots', 'Base', 'Vegas of the North', 1, 5000);
INSERT INTO GameSession
	VALUES(5, TO_DATE('10-DEC-2022 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('10-DEC-2022 12:15:00', 'DD-MON-YYYY hh24:mi:ss'), 2, 'Blackjack', 'Base', 'Downtown Eastside', 1, 0);
INSERT INTO GameSession
	VALUES(6, TO_DATE('10-DEC-2022 12:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('10-DEC-2022 12:15:00', 'DD-MON-YYYY hh24:mi:ss'), 1, 'LuckySlots', 'Base', 'Downtown Eastside', 1, 50);
INSERT INTO GameSession
	VALUES(7, TO_DATE('14-JAN-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-JAN-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 1, 'Roulette', 'Russian', 'North Shore Gaming', 1, 30);
INSERT INTO GameSession
	VALUES(8, TO_DATE('14-FEB-2021 13:00:00', 'DD-MON-YYYY hh24:mi:ss'), TO_DATE('14-MAR-2021 17:00:00', 'DD-MON-YYYY hh24:mi:ss'), 1, 'Poker', 'Base', 'Ace of Spades', 1, 500);

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
INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(5, 5, -10000000000);

