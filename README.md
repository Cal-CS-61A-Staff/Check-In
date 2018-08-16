Lab Assistant Manager
============

## Overview

Provides a web-based interface for managing lab assistants. Allows TAs to configure seections for lab assistants to sign up for along with facilitating the check in process. 


## Branches Organization:
* ***master*** - is the main branch, storing the current code all non-course specific features should be branched from.
* ***cs61a*** - is the master branch for the CS61A course lab assistant manager instance.
* ***cs61b*** - is the master branch for the CS61B course lab assistant manager instance.
* ***data8*** - is the master branch for the DATA8 course lab assistant manager instance.
* ***data100*** - is the master branch for the DATA100 course lab assistant manager instance.

## How to contribute:
* Fork the repository. If you are not used to github have a look at [fork a repository](https://help.github.com/fork-a-repo)
* Create a branch either from master (or a specific course branch if this is a 1 course only feature).
* Add your features and commit your desired changes.
* Create a pull request detailing your changes [creating pull requests](https://help.github.com/articles/about-pull-requests/).

## Installation

1. Set up VirtualBox, Vagrant & Homestead

    Download and install virtual box:
    https://www.virtualbox.org/wiki/Downloads
 
    Download and install vagrant:
    https://www.vagrantup.com/downloads.html

    Install the Homestead Vagrant Box:
    `vagrant box add laravel/homestead`
    
    Install Homestead
    
    ```
    cd ~
    git clone https://github.com/laravel/homestead.git Homestead
    // Clone the desired release...
    git checkout v6.1.0
    
    // If on Mac / Linux...
    bash init.sh

    // If on Windows...
    init.bat
    ```
    
    Update homestead.yaml to point to where you are cloning this repo. E.g:
    
    ```
    folders:
        - map: ~/Projects/Check-In
        to: /home/vagrant/Code/Check-In

    sites:
        - map: la.app
        to: /home/vagrant/Code/Check-In/public
        php: "5.6"
    ```
    
2. The Hosts File

    You must add the "domains" for your Nginx sites to the hosts file on your machine. The hosts file will redirect requests for your Homestead sites into your Homestead machine. On Mac and Linux, this file is located at `/etc/hosts`. On Windows, it is located at `C:\Windows\System32\drivers\etc\hosts`. The lines you add to this file will look like the following:
    ```
    192.168.10.10  la.app
    ```
    Make sure the IP address listed is the one set in your Homestead.yaml file. Once you have added the domain to your hosts file and launched the Vagrant box you will be able to access the site via your web browser:

    http://la.app
3. Go to your Homestead directory. Bring up Vagrant

    ```
    cd ~/Homestead
    vagrant up
    ```
    
4. SSH Into Vagrant Box
    ```
    vagrant ssh
    // Set working directory to be project location
    cd Code/Check-In
    ```
5. Via SSH install dependencies through the PHP package manager `composer`. 

    `composer install`

6. Via SSH run the database migrations

    `php artisan migrate`

7. Via SSH Seed the database with the proper first few values

    `php artisan db:seed --class DefaultSettingsSeeder`

8. Set up environment variables
    ```
    // Copy the example environment variable file to .env
    cp env.example .env
    ```
9. Via SSH generate an application key

    `php artisan key:generate`

10. Launch Vagrant:
    `vagrant up`
    
    If you change the sites or maps inside of `Homestead.yaml` you will need to run the following to update Vagrant:
    `vagrant reload --provision`
    
    

11. Point your browser to http://la.app. 


## Deployment

First point a git remote to the Dokku server (the example below is CS61A specific):
    
    git remote add dokku dokku@app.cs61a.org:la

To deploy from master:

    git push dokku master

Deploy from another branch:

    git push dokku my_branch:master
    

### First Time Deployment

Note: These are deployment instructions to the CS61A managed Dokku platform. They should be generic enough to follow to deploy to your own Dokku instance however.

Tip:  add `alias dokku="ssh -t dokku@app.cs61a.org"` to your aliases file (e.g. `~/.bashrc`).

    dokku apps:create app-name
    git remote add online dokku@app.cs61a.org:<app_name>
    dokku mysql:create db-name
    dokku mysql:link db-name app-name
    # Set DNS record
    dokku domains:add app-name name.cs61a.org

    # Get the APP KEY
    dokku enter <app_name> web php artisan key:generate

    dokku config:set app-name APP_KEY=<KEY COPIED FROM key:generate command> APP_ENV=prod OK_COURSE_OFFERING="cal/cs61a/fa17" APP_OAUTH_KEY=<SECRET> APP_URL="https://mydomain.com" FORCE_HTTPS=true MAIL_DRIVER=mailgun MAIL_GUN_DOMAIN=<MAIL_GUN_DOMAIN> MAIL_GUN_SECRET=<MAIL_GUN_SECRET>
    MAIL_HOST=<MAIL_HOST> MAIL_PORT=<MAIL_PORT> MAIL_USERNAME=<MAIL_USERNAME> MAIL_PASSWORD=<MAIL_SECRET> MAIL_ENCRYPTION=null
    dokku letsencrypt app-name
    # Change OK OAuth to support the domain
    
    # Run migrations after following steps in Deployment
    dokku enter <app_name> web php artisan migrate --force
    dokku enter <app_name> web php artisan db:seed --class=DefaultSettingsSeeder --force

    



