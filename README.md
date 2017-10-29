This is the repo for the old version of SpaceX Stats. To access the new repo, see [spacexstats-react](https://github.com/r-spacex/spacexstats-react).


# SpaceX Stats
A fan website for the company SpaceX originally created by Luke Davia ([Twitter](https://twitter.com/lukealization), [Reddit](https://reddit.com/u/EchoLogic)).

## How to contribute
### Automated setup (recommended)

The project will be installed on a Vagrant virtual machine. All the dev tools will be install inside it.

#### Prereqs
- [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
- [Vagrant](https://www.vagrantup.com/downloads.html)

#### Setup instructions

1. In the project directory, run the `vagrant up` command.
2. Import a copy of the database in your project folder (you can get one by asking the admins). Then get into your Vagrant machine with `vagrant ssh`. Finally, import the database backup with `mysql -u spacexstats -pspacexstats spacexstats < database.sql`.
3. Add this line to your hostfile (`/etc/hosts` on Linux/Mac, `C:\Windows\system32\drivers\etc\hosts` on Windows): `33.33.33.33 spacexstats.dev`. The project is now accessible by going to `http://spacexstats.dev`!

Remember, when you stop working on the project you can stop the Vagrant box with `vagrant halt`. You can delete it with `vagrant destroy`.

### Working on the project

When editing JS, CSS, etc files, you'll use gulp to compile these assets. On your Vagrant machine, you just have to run `npm start`.

`php artisan cache:clear` clears the laravel cache, useful after db changes that you want to see reflected immediately. May affect site performance as cache is rebuilt. `php artisan view:clear` clears the cache for the views.

### Deploying

Deployment is done via SFTP using WinSCP (only works from windows currently).

1. Modify **sftp_deploy_example.txt** with your SFTP connection info (username, password, rsa key) and save the file as **sftp_deploy.txt**.
2. In the command prompt, simply type `deploy` and all files will be copied to the server

### Manual setup How-to
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

## License
<img src="https://licensebuttons.net/l/by-nc-sa/3.0/88x31.png" />

This work is licensed under the [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](http://creativecommons.org/licenses/by-nc-sa/4.0/) license.
