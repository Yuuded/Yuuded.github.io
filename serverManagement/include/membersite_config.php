<?PHP
require_once("./include/fg_membersite.php");

$fgmembersite = new FGMembersite();

//Provide your site name here
$fgmembersite->SetWebsiteName('serverManagement');

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail('user11@user11.com');

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time
//Note: table name is just here for compatability of this code with the original src (for testing purposes)
$fgmembersite->InitDB(/*hostname*/'localhost',
                      /*username*/'id193030_rr',
                      /*password*/'bestPassword2016IPM',
                      /*database name*/'id193030_smdatabase',
                      /*table name*/'testUsers1');

//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it here
$fgmembersite->SetRandomKey('IkPLM8EoIlDxdtCMpEjl');

//Our methods

//Sets the name of the table that stores the users
$fgmembersite->SetUserTableName('testUsers1');

//Sets the name of the table that stores the servers
$fgmembersite->SetServersTableName('testServers1');

//Sets the name of the table that contains the relations user-server
$fgmembersite->SetUserServerTableName('testUsersServers1');

//Sets the name of the table that contains the admin invites (to other servers) that each user has pending
$fgmembersite->SetInvitesTableName('testInvites1');

//Sets the name of the table that contains the admin requests (to other server) that each user has pending
$fgmembersite->setRequestsTableName('testRequests1');

?>