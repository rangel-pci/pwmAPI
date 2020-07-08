<h1 align="center"><img src="https://github.com/rangel-pci/pwmAPI/blob/master/logo.svg" /></h1>

<h2>Index</h2>
<ul>
<li><a href="#About">About</a></li>
<li><a href="#ImplementedTechnologies">Implemented Technologies</a></li>
<li><a href="#Documentation">Documentation</a></li>
<li><a href="#HowToRun">How to Run</a></li>
<li><a href="#HowToContribute">How to Contribute</a></li>
<li><a href="#License">License</a></li>
</ul>

<h2>About</h2>

pwmAPI is a RESTful api made with PHP in the MVC standard, which implements the http GET, POST, PUT and DELETE methods.
Basically works as a social network for people who play eletronic games, where the users can add and remove games from a list and interact with each other, 
check the <a href="https://documenter.getpostman.com/view/11970203/T17J9muk?version=latest">documentation</a> to see all possible actions.

<h6 align="center"><kbd><img src="https://github.com/rangel-pci/pwmAPI/blob/master/demo.png" /></kbd></h6>

<h2 id="implementedTechnologies">implemented Technologies</h2>

- <a href="https://getcomposer.org/">Composer</a> - package manager
- <a href="https://www.php.net/">PHP7</a> - server/controllers/models
- <a href="https://github.com/PHPMailer/PHPMailer">PHPMailer</a> - send keys for account activation by email
- <a href="https://jwt.io/">JWT method</a> (JSON Web Token) - user auth
- <a href="https://github.com/lcobucci/jwt">lcobucci/jwt</a> - lib to work with JWT
- <a href="https://www.mysql.com/">MySQL</a> - game & user information storage
- <a href="https://api.rawg.io/docs/">RAWG Video Games Database API</a> - game data

<h2>Documentation</h2>

Documentation available at https://documenter.getpostman.com/view/11970203/T17J9muk?version=latest.

<h2 id="HowToRun">How to Run</h2>

> The application was made using Windows OS with the Xampp package, so the step by step too.

```bash
#Create a folder named 'backend' on the server root (htdocs)
$ mkdir backend
$ cd backend

#Clone the repository
$ git clone https://github.com/rangel-pci/pwmAPI.git

#*it is important that the folder scheme looks like this -> xampp/htdocs/backend/api profile_image temp create_tables.sql insert_games.sql
#now in backend/api/v1/inc install the dependencies
$ cd api/v1/inc
$ composer update
```
<h3>You can mount the database via MySQL command line or via PHPmyAdmin</h3>
<h4>Via the MySQL command line</h4>

```bash
#in /backend mount the database
$ mysql -u root -p
$ create database pwm_db;
$ use pwm_db
$ source create_tables.sql;
$ source insert_games.sql;
```

<h3>Configuring the email script</h3>

Now that the database has ben set up, change the send_mail.php file that is in backend/api/v1/inc/send_mail.php.
Enter the credentials of the email to be used to send the account activation keys.

<h4>Example</h4>

<code>$your_email = "the_email_that_will_send_the_keys@gmail.com"</code>
<code>$your_passowrd = "the_password_of_the_email_that_will_send_the_keys"</code>

<h2 id="HowToContribute">How to Contribute</h2>

- Fork the repository
- Create a branch with your feature: <code>git checkout -b my-feature</code>
- Commit your changes: <code>git commit -m 'feat: My new feature'</code>
- Push your branch: <code>git push origin my-feature</code>

<h2>License</h2>

This project is under the MIT license, more information at <a href="https://github.com/rangel-pci/pwmAPI/blob/master/LICENSE">LICENSE</a>.
