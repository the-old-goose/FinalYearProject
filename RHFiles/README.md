RHFiles
=============
Version 1.0

> "Local Exchange Trade Systems are by the community for the community"


![](https://i.ibb.co/b6ypDnQ/home-splash-image.png)

![](https://i.ibb.co/hftxZVH/page5.png)

Features
=============

- Connect with members of the community and discover new people on campus

- Learn new skills by trading your own

- Totally free no money involved

- Easy to use interface 

-------------

Set up
=============


Setting up the system is easy:

1.Ensure LAMPP, XAMPP, WAMPP stack or other PHP server is installed and running

2.Pull from master renaming the parent directory : rhfiles and copy the directory into htdocs folder located in the running server

3.Set up the database by opening phpmyAdmin and creating a database called RHFILES. Click on import in the tab bar and navigate to dbconfig/RHFILES.sql and import the sql dump

4.Ensure the following configuration parameters are set:

-application/config/database.php 79,80

    <?php
        $db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost', //Your hostname
		'username' => 'root',     //Your db username
		'password' => '',	  //Your db password
    ?>

5. The system should work as intended, a username and password is provided hardcoded into the login screen

NOTE: Registering an account may fail if smtp port 475 is blocked, similarly the verification email may end up in your spam box.
If this is the case and you intend to keep using the application then navigate to the users table and set the confirmed bit from 0 to 1 and then log in as usual.





