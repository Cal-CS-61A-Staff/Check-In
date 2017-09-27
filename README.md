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
    - map: ~/Code
      to: ~/Projects

    sites:
    - map: la.app
      to: ~/Projects/Check-In/public
    ```
    

2. Use composer to install dependencies

    `composer install`

3. Run the migrations

    `php artisan migrate`

4. Seed the database with the proper first few values

    `php artisan db:seed --class DefaultSettingsSeeder`
    
5. Point your browser to http://localhost:5000.  (This might take a while.)

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


