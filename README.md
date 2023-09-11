# NutriMind


### Introduction
Recognizing the significant impact of eating disorders on individuals, we embarked on a project
to provide education and support for those in need. Our approach involved developing a website
to raise awareness of eating disorders and creating an application that enables users to track
their eating habits and mood. In addition to these features, we designed a secure platform for
doctors to monitor patient progress and incorporate functionalities that allow patients to
conveniently schedule appointments and make payments.

### Technologies
1. Laravel 10.x
2. PHP 8.1.2
3. phpMyAdmin

---


### Packages
1. Laravel Passport
2. Laravel Socialite
3. Laravel Pusher
4. Laravel Cashier

---


## Installation

##### Clone the repository
```
git clone git@github.com/Euphoria-Team-Nutri-Mind-App-Website/NutriMind-Backend.git

```

##### Install all the dependencies using composer


```
composer install

```

##### Generate a new Passport authentication secret key


```
php artisan passport:client --personal

```

##### Run the database migrations


```
php artisan migrate

```

##### Run the database migrations


```
php artisan migrate

```

##### Run the database seeder


```
php artisan db:seed

```

## API

#### Request headers

| Required | Key | Value |
|----------|----------|----------|
| Yes    | Content-Type    | application/json |
| Optional    | Authorization     | Bearer {token} |

## Authentication
This applications uses Laravel Passport to handle authentication. The token is passed with each request using the Authorization header with Token scheme. The Passport authentication middleware handles the validation and authentication of the token. Please check the following sources to learn more about Laravel Passport.
[GitHub][https://laravel.com/docs/10.x/passport]
