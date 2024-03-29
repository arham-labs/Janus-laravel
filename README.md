# Laravel Authentication Package

This package will provide APIs for user authentication that is registration and login APIs with routes.

## Installation

In order to install the package use the command specified below - 

```bash
composer require arhamlabs/authenticator

```


## Required Packages
While installing package you have enable sodium extension in PHP ini file.

## Configuration

Get inside the **config/app.php** file then add socialite services in providers

```bash
'providers' => [
    ....
    .... 
    Arhamlabs\Authentication\AuthenticationServiceProvider::class

],

```
The defaults configuration settings are set in the **config/al_auth_config.php** file. Copy this file to your own config directory to modify the values or you can publish the config using this command:

```bash

php artisan vendor:publish --provider="Arhamlabs\Authentication\AuthenticationServiceProvider"

```
Finally, you should run your database migrations. This package will create following tables into database:

1.temp_registrations 
2.auth_settings
3.temp_otp

Also for mobile otp authentication one more migration is used.Which will add columns into the user table.

**Command:**

```bash
php artisan migrate

```

Laravel provide default migration for sanctum name as create_personal_access_tokens_table.Simply edit or create new migration for alter personal_access_tokens table.Add following column name as "expires_at".


    $table->timestamp('expires_at')->nullable();



**Sanctum Token Ability Middleware Setup:**

Sanctum also includes two middleware that may be used to verify that an incoming request is authenticated with a token that has been granted a given ability. To get started, add the following middleware to the $routeMiddleware property of your application's **app/Http/Kernel.php** file:

```bash
'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class

```


**Sanctum Token authentication exception handling on route:**

To handle default exception on api routes such as AuthenticationException/AccessDeniedHttpException add following code into the register function of your application's **app/Exception/Handler.php** file:

```bash

        $this->renderable(function (AuthenticationException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                $customUserMessageTitle = 'Sorry, we were unable to authenticate your request';
                $errorResponse->setCustomResponse($customUserMessageTitle);
                return $errorResponse->getResponse(401, []);
            }
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                return $errorResponse->getResponse(403, []);
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            $errorResponse = new ApiResponse;
            if ($request->is('api/*')) {
                return $errorResponse->getResponse(404, []);
            }
        });


```


**Update default App\Models\User.php:**

```bash

    use Arhamlabs\Authentication\Models\AuthSetting;

    protected $fillable = [
            'uuid',
            'name',
            'sso_type',
            'first_name',
            'last_name',
            'username',
            'email',
            'mobile',
            'country_code',
            'password',
            'email_verified_at',
        ];

    public function settings()
    {
        return $this->hasOne(AuthSetting::class, 'model_id', 'id')->where('model_name', 'App\Models\User');
    }

```

## API Reference 

## *Registration*

#### User registration

- Users can register using email and password.Data will be saved in a temporary table.
- Next If the config flag email_verification=false then details will be saved in the main table i.e users table as well as user_setting table will be updated. 
- If the config set to email_verification=true then verification mail will send to users email.

```bash
  POST /api/package/auth/register

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `first_name` | `string` ||
| `last_name` | `string` ||
| `user_type` | `string` ||
| `username` | `string` ||
| `email`    | `string` | *Required*|
| `password` | `string` |*Required*|
| `mobile`   | `number` ||  
| `country_code` | `number` ||
| `user_type` | `string` ||

---


### User Register with Mobile and OTP
- Users can register using mobile and otp. OTP will be sent to the user via sms. 
- Once otp send temp_otp table will be used for maintaining the verification details.
- If mobile number already register then it will not allow to user register with same mobile number.



```bash
  POST /api/package/auth/mobile-register
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `first_name` | `string` ||
| `last_name` | `string` ||
| `user_type` | `string` ||
| `username` | `string` ||
| `email`    | `string` ||
| `password` | `string` ||
| `mobile`   | `number` |*Required*|  
| `country_code` | `number` |*Required*|
| `user_type` | `string` ||



---


### Mobile OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a sanctum token will be generated.
- If mobile number already register then it will not allow to user register with same mobile number.



```bash
  POST /api/package/auth/mobile-register-verify-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `mobile`    | `string` | *Required*|
| `country_code`    | `string` | *Required*|



---


# *User Login*

### User Login using Username and Password
- Users can login with Email/Mobile/Username and password.
- Once user gets authenticated then laravel sanctum token will be generated.


```bash
  POST /api/package/auth/login
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `username`    | `string` | *Required*|
| `password` | `string` |*Required*|



---

### User Login with Email and OTP
- Users can login using email and otp. OTP will be sent to the user via email. 
- Once otp send temp_otp table will be used for maintaining the verification details.



```bash
  POST /api/package/auth/sent-email-otp

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|



---

### Mail OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.



```bash
  POST /api/package/auth/mail-verify-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `email`    | `string` | *Required*|



---

### User Register/Login with Mobile and OTP with same end point
- Users can login using sms and otp. OTP will be sent to the user via sms. 
- Once otp send temp_otp table will be used for maintaining the verification details.
- If mobile number already register then it will allow user to login
- If mobile number is not register then it will allow user to register and then login.



```bash
  POST /api/package/auth/sent-mobile-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `mobile`    | `string` | *Required*|
| `country_code`    | `string` | *Required*|



---


### Mobile OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.
- In config file if allow_login_or_registration_through_mobile_number flag set to be true then following scenario will be applied. 
- If mobile number already register then it will allow user to login
- If mobile number is not register then it will allow user to register and then login.



```bash
  POST /api/package/auth/sms-verify-otp
```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `mobile`    | `string` | *Required*|
| `country_code`    | `string` | *Required*|



---

### Forgot Password
- Users can reset password using email. Reset link will be sent to the user via email.User can change his password via reset link.
- For blocked user package will not generate reset link. 



```bash
  POST /api/package/auth/forgot-password

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|



---
### Logout
- This api is used to logout user and it will destroy sanctum token of that user.



```bash
  POST /api/package/auth/logout

```

### Set/Change Password
- Users can set/change password using sanctum token. If user login via sso or otp then user can set their password for first time.For next time user have to provide current password to change his password. 



```bash
  POST /api/package/auth/update-password

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `password`    | `string` | *Required*|
| `password_confirmation`    | `string` | *Required*|
| `current_password`    | `string` | *Required* (for set password)|



---



# *Social Media Login/Registration*


### Google
- Users can login via google account using id token.For google account validation package will validate id token and aud(client id)  using [Google Client](https://packagist.org/packages/google/apiclient) package.




```bash
  POST /api/package/auth/sso-login

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|
| `idToken`    | `string` | *Required*|
| `sso_type`    | `string` | *Required*|
| `aud`    | `string` | *Required*|




### Linkedin- For Mobile
- Users can login via linkedin account using id token.Package will validate id token.
- *sso_type must* be 'linkedin-mobile'


```bash
  POST /api/package/auth/sso-login

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|
| `idToken`    | `string` | *Required*|
| `sso_type`    | `string` | *Required*|


### Linkedin- For Web
- Users can login via linkedin account using authorization code.Package will validate authorization code and will fetch access token from linked in server.
- Package will update user details by fetching details from linkedin server.
- *sso_type must* be 'linkedin-web'
- *idToken* must be authorization code.


```bash
  POST /api/package/auth/sso-login

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|
| `idToken`    | `string` | *Required*|
| `sso_type`    | `string` | *Required*|




### Apple 
- Users can login via apple account using id token.For linkedin account validation package will validate id token.



```bash
  POST /api/package/auth/sso-login

```

| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|
| `idToken`    | `string` | *Required*|
| `sso_type`    | `string` | *Required*|




# *Package config file*


### al_auth_config.php
- Developer can change configuration using al_auth_config.php file as follows.



```bash

    
    //check email verification requirement
    'email_verification' => true,
    
    //check mobile verification requirement
    'mobile_verification' => true,

    /* If this flag is set true then user can login or register using the same endpoint based on the below scenario  
        1. If the user is registered initially and the api is fired then he will be logged in.
        2. If the user is not registered initially and the api is fired then he will be registered.
    */

    'allow_login_or_registration_through_mobile_number' => false,

    //length for otp 
    'otp_length' => 4,

    //otp expire in minutes
    'otp_expire' => 5,
    
    //SMS OTP Configuration
    'sms' => [
        'delay' => 60, //in seconds
        'per_day_count' => 5 //per day sms limit for user
    ],

    //allow multi login with same credentials
    'user_multi_login' => true,

    //default user type
    'user_Type' => 'app_user',

    //if true then it will check user block status
    'is_check_user_block' => true,

    //email verification mail expiry in hours
    'email_verification_mail_expiry' => 48,

    //forgot password mail expiry in hours
    'forgot_password_mail_expiry' => 48,

    //email link encryption key
    'email_encryption_key' => env('EMAIL_ENCRYPTION_KEY', 'ALAUTH'),

    //social media login linkedin config setup
    'linkedin' => [
        'LINKEDIN_REDIRECT_URI' => env('LINKEDIN_REDIRECT_URI'),
        'LINKEDIN_CLIENT_ID' => env('LINKEDIN_CLIENT_ID'),
        'LINKEDIN_CLIENT_SECRET' => env('LINKEDIN_CLIENT_SECRET'),
        'CURLOPT_SSL_VERIFYPEER' => env('CURLOPT_SSL_VERIFYPEER')
    ],

    //social media login apple config setup
    'apple' => [
        'TOKEN_ISS' => env('TOKEN_ISS', "https://appleid.apple.com"),
        'TOKEN_AUD' => env('TOKEN_AUD', "com.example.co.app"),
    ]



```