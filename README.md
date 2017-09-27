Lab Assistant Manager
============

## Overview

Provides a web-based interface for managing lab assistants. Allows TAs to configure seections for lab assistants to sign up for along with facilitating the check in process. 


## Installation

1. Set up VirtualBox, Vagrant & Homestead

    Download and install virtual box:
    https://www.virtualbox.org/wiki/Downloads
 
    Download and install vagrant:
    https://www.vagrantup.com/downloads.html

    Install the Homestead Vagrant Box:
    `vagrant box add laravel/homestead`
    
    Install Homstead
    
    ```
    cd ~
    git clone https://github.com/laravel/homestead.git Homestead
    git checkout git checkout v6.1.0
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
3. Use composer to install dependencies

    `composer install`

4. Run the migrations

    `php artisan migrate`

5. Seed the database with the proper first few values

    `php artisan db:seed --class DefaultSettingsSeeder`

6. Launch Vagrant:
    `vagrant up`
    
    If you change the sites or maps inside of `Homestead.yaml` you will need to run the following to update Vagrant:
    `vagrant reload --provision`
    
    
6. Point your browser to http://localhost:5000.  (This might take a while.)

## Deployment

First point a git remote to the Dokku server:

    git remote add dokku dokku@app.cs61a.org:la

To deploy from master:

    git push dokku master

Deploy from another branch:

    git push dokku my_branch:master

### First Time Deployment

Tip:  add `alias dokku="ssh -t dokku@app.cs61a.org"` to your aliases file (e.g. `~/.bashrc`).

    dokku apps:create app-name
    git remote add online dokku@app.cs61a.org:la
    dokku mysql:create db-name
    dokku mysql:link db-name app-name
    # Set DNS record
    dokku domains:add app-name name.cs61a.org

    dokku config:set app-name APP_KEY=<SECRET> APP_ENV=prod OK_COURSE_OFFERING="cal/cs61a/fa17" APP_OAUTH_KEY=<SECRET>
    dokku letsencrypt app-name
    # Change OK OAuth to support the domain


