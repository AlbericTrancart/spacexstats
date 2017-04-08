# SpaceX Stats
A fan website for the company SpaceX originally created by Luke Davia ([Twitter](https://twitter.com/lukealization), [Reddit](https://reddit.com/u/EchoLogic)).

### Setup How-to
#### Prereqs
- PHP 5.3 or above
- MySQL server
- Redis (if running locally, installation depends on your OS. Google is your friend)
- Composer [installation instructions here](https://getcomposer.org/doc/00-intro.md)
- Laravel [installation instructions here](https://laravel.com/docs/5.3/installation)

#### Setup instructions
1. Make sure all prereqs are installed / available. In the case of MySQL, just make sure you have access to a server if you don't have one installed locally.
2. Open command prompt and navigate to the code folder. run the command `composer install`. This will install all dependecies listed in the composer.json file.
3. Make a copy of the ".env.example" file and name it ".env". This file handles some config values.
4. In the .env file:
  * set your MySQL connection info (host, username, password). 
  * set your Redis host and port (in the case of a local redis install, you can use 127.0.0.1 and port 6379).
5. Now we need to seed the database. To do so, run this command: `php artisan migrate:refresh --seed`. You will get a few prompts asking if you're really sure about this, answer yes to all.
6. If all went well, you should now be able to run the site. If you're already running IIS or Apache, you can access the site locally like you would access any normal site. If not, you can run the command `php artisan serve` to spin up a local server on port 8000.

#### Deploying
(only works from windows currently)

Deployment is done via SFTP using WinSCP.

1. Modify **sftp_deploy_example.txt** with your SFTP connection info (username, password, rsa key) and save the file as **sftp_deploy.txt**.
2. In the command prompt, simply type `deploy` and all files will be copied to the server


##### Useful commands
`php artisan cache:clear` or on linux `php artisan cache:clear` - clears the laravel cache, useful after db changes that you want to see reflected immediately. May affect site performance as cache is rebuilt.

## License
<img src="https://licensebuttons.net/l/by-nc-sa/3.0/88x31.png" />

This work is licensed under the [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](http://creativecommons.org/licenses/by-nc-sa/4.0/) license.