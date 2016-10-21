# ForceDownload
A pretty lightweight PHP script to force download files from approved sources, track downloads in a small database and log errors using built in error_log.

Originally developed for my podcast website to allow users to download mp3 files, but I also wanted to track downloads for analytics purposes.

### Dependencies
Github [PHP Dotenv](https://github.com/vlucas/phpdotenv)

Using Composer
````json
"vlucas/phpdotenv": "2.4.0"
````

### Using .env
I have included the .env in this repo, but this is not recommended.

**PLEASE DO NOT COMMIT YOUR .env FILE, EVER**

This is a file that cannot be accessed by a simple http request so its perfect for hiding your DB credentials in it and not hard coding them into a script.

````
DBHOST=localhost
DBNAME=placeholder
DBUSER=placeholder
DBPASS=placeholder
````
You would obviously use your own server's database configuration here.

### Analytics database
This is a piece that I needed for my requirements. You can disable this by taking out the logger on the build method. As of 1.1 you can choose to have this turned off by passing `false` when you call the build method. 

Included you will find the structure of the database I used. I will trust you to set up what you need.

### Using the script
For a file you want to the download, link to the download.php script

`http://localhost/path/to/file/download.php?postid=12&link=http://files.domain.com/thing.mp3`

*
postid* is used on the analytics to track what file is being downloaded.


*link* is the URL path to the file you want to force download to the user upon clicking the link.


The link must be in the whitelist, which is configured in the download.php file.


### Whitelisted sources
This really isnt magic, or extensive in any way. I thought it would be a quick and effective way to prevent just any file to be thrown through your script. Since I built this script for my new podcast site, I wanted to allow only files from my audio host to be downloaded.


Basically, if the whitelist item matches the link, then it will return true. So by default you can see `media.zencast`. So any url that matches that format would be approved. Anything that does not match that, or others in the array will return false, causing no download to happen.


````php
$whitelist = array(
  'media.zencast',
  'google',
  'yoursite.com/files/specials/'
);
````

### Open source
Please keep the credit at the top of the file. If you have suggestions or improvements, fork and submit a Pull Request. Thanks!
