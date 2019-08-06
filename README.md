RHFiles: Update 06/08/2019
=============

This is my final year project for university built focusing mainly on back-end development skills.The application was built using PHP and the CodeIgniter framework (MVC architecture) with restAPI integration. The front end is HTML and vanilla CSS with a simple boilerplate as I wanted to strenghten my CSS skills.

The application was tested using PHPUnit which comes packaged with CodeIgniter and also has a full documentation generated using PHPDOX.

I have removed some sensitive information from the SMTP mailing service as it used an email of mine to send notifications to users therefore the application will need tweaking to work as intended:

-FinalYearProject\RHFiles\application\libraries\Verify_User.php   98,117

    <?php
        $config= array(
            'protocol'=>'smtp',
            'smtp_host'=>'ssl://smtp.googlemail.com',
            'smtp_port'=>465,
            'smtp_user'=>'HIDDEN',
            'smtp_pass'=>'HIDDEN',
            'charset'   => 'iso-8859-1',
            'mailtype'  => 'html',

        );
    ?>
    
As most of the pictures of the application are contained within my thesis , I have taken a few snapshots and will link them below so you can get a feel for the application.



RHFiles
=============
Version 1.0

> "Local Exchange Trade Systems are by the community for the community"


![](https://i.ibb.co/b6ypDnQ/home-splash-image.png)

![](https://i.ibb.co/hftxZVH/page5.png)

![](https://i.ibb.co/jGLdTX1/Capture-6.png)

![](https://i.ibb.co/SXcXkKT/Capture-5.png)

![](https://i.ibb.co/zsRB94F/Capture-3.png)

![](https://i.ibb.co/yq1k8KF/Capture-2.png)

![](https://i.ibb.co/RTh5KQD/Capture.png)

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
If this is the case and you intend to keep using the application, navigate to the users table and set the confirmed bit from 0 to 1 and then log in as usual.





