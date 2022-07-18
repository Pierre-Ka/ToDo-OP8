# ToDo&Co Application
***
Analyse CodeClimate :
https://codeclimate.com/github/Pierre-Ka/ToDo-OP8

Repository GitHub :
https://github.com/Pierre-Ka/ToDo-OP8
***
To run the project you will need to have :
* apache
* php 8
* mysql
* phpMyAdmin
* composer
* symfony 6

Optionnally you can have :
* make
* blackfire
* xdebug
***
## Installation
1. Create a new projet and Clone this repository :
```
    git clone https://github.com/Pierre-Ka/ToDo-OP8.git
    cd ToDo-OP8/
```
2. Configure Database :
* Configure your DATABASE_URL. If you have mysql DATABASE_URL="mysql://username:password@127.0.0.1:3306/dbname" in the .env files then open Apache server
3. Install the dependencies :
```
    composer install
    php bin/console cache:clear
```
3. Run command :
```
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
```
or if you have make
```
    make fixtures
```

4. Run server :
```
    symfony server:start
```

Look which port the web server is using locally,
you can now connect to the application at the following URL and enjoy its features.

5. Read the doc :

Optionnally your can read the documentation
![Smile](https://www.freepngimg.com/download/face/73751-emoticon-smiley-face-wink-mouth-smile.png)
