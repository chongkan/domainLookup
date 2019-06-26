# domainLookup-laravel-vue

## Prerequisites

- Laravel Homestead

- Reference:  https://laravel.com/docs/5.8/homestead#installation-and-setup

## Project Installation. 

- Clone this Repo inside your Homestead Folder

- Configure your homestead.yaml file according to your local config, to map your local folders to this repo.

## Database: 

- There are migrations in place for you to manually create the DB, but due to time constraints and the nature of this project, I have simply removed the dbs from the gitignore file and added them both to the repo. 

- Verify your hosts file, add the vagrant IP to your hosts files if not present. 

- Navigate to http://chongkan.src (or the domain you condigured) in your browser. 

## Unit Testing

- For testing with your IDE or PHPUnit: Use a .env.testing file and specify the custom path to the testing.sqlite database. 

- Note: I have also included my .env files, for easy of deployment. They should not be removed from gitignore in normal projects. No sensitive data has been shared anyway. 

## FrontEnd

- It was made using Vue.js.

- Includes FE validation as well. 

- AJAX was handled with Axios. 


## Screenshots: 

- Welcome Form: Index: https://screencast.com/t/pluisWXu 

- List of valid domains: https://screencast.com/t/ohFmOdlJAzaS 

- List including an invalid domain:  https://screencast.com/t/V3XgoDRLU 

- Unite Tests: https://screencast.com/t/MDk1JSNBNYnb 