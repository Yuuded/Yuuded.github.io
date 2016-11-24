USE id193030_smdatabase;

select * FROM testUsers1;
select * FROM testServers1;
select * FROM testUsersServers1;
select * FROM testInvites1;
select * FROM testRequests1;

DROP TABLE testUsersServers1;
DROP TABLE testInvites1;
DROP TABLE testRequests1;
DROP TABLE testServers1;

select * from testUsers1 where email != "yo@gmail.com";

Select id_server from testUsersServers1 where id_user=1;

UPDATE testServers1 SET isOnline = 0, name = "LocalServer2", nUsers = 0 , onlineSince = 3429843289, lastActiveUser = 3429843289 WHERE id_server = 48
SELECT email FROM testsServers1, testsUsersServers1 WHERE id_server = "49";

UPDATE testServers1 SET isOnline = 1 WHERE id_server = "10";

UPDATE testServers1 SET nUsers = 10000 WHERE id_server = "49";

UPDATE testServers1 SET nUsers = 29000 WHERE id_server = "32";