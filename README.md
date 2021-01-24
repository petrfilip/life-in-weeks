# Life In Weeks

Simple LIW implementation in PHP.
The application allows insert the most common and the most important event of the week which should not be forgotten.
It includes simple photo gallery for each week. 

In `config.php` is possible to configure password and  
```
define( 'PASSWORD', 'secret' ); // <-- change this!!
define( 'DEFAULT_YEAR', '1990' ); // <-- change this!!
```

## Development
The application is built on top of PHP and JavaScript. Data is stored to json files. Uploaded images are optimized.

Dockerized development PHP server is bundled in this repository. 
So, for code improving it is possible to use it (docker-compose up --build)


## TODO: 
- [X] add security
- [ ] add webP conversion support
- [ ] image preloading
- [x] default year
- [ ] multiuser support
- [ ] remove image
- [ ] remove event


![Life In Weeks](./uploads/2021-01-24-10-19-07_2021-01-24_11-18.png)