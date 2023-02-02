
## API Reference 

## *Registration*

#### User registration

- Users can register using email and password.Data will be saved in a temporary table.
- Next If the config flag email_required=false then details will be saved in the main table i.e users table as well as user_setting table will be updated. 
- If the config set to email_required=true and email_verification_send=true then verification mail will send to users email.

```http
  POST /api/package/auth/register

```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `firstName` | `string` ||
| `lastName` | `string` ||
| `username` | `string` ||
| `email`    | `string` | *Required*|
| `password` | `string` |*Required*|
| `mobile`   | `number` ||  
| `countryCode` | `number` ||

---
---
---
---
---
---
---
---
---
---
---
---
---
---
---
---
---
---


# *User Login*

### User Login using Username and Password
- Users can login with Email/Mobile/Username and password.
- Once user gets authenticated then laravel sanctum token will be generated.


```http
  POST /api/package/auth/login
```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `username`    | `string` | *Required*|
| `password` | `string` |*Required*|



---
---
---
---
---
---

### User Login with Email and OTP
- Users can login using email and otp. OTP will be sent to the user via email. 
- Once otp send temp_otp table will be used for maintaining the verification details.



```http
  POST /api/package/auth/sent-email-otp

```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `email`    | `string` | *Required*|



---
---
---
---
---
---

### OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.



```http
  POST /api/package/auth/verify-otp
```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `email`    | `string` | *Required*|



---
---
---
---
---
---

### User Login with Mobile and OTP
- Users can login using sms and otp. OTP will be sent to the user via sms. 
- Once otp send temp_otp table will be used for maintaining the verification details.



```http
  POST /api/package/auth/sent-mobile-otp
```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `mobile`    | `number` | *Required*|
| `country_code`    | `number` | *Required*|



---
---
---
---
---
---


### OTP Verification
- This api is used to verify OTP. 
- Once OTP gets verified a token will be generated. Number of attempts will be added to the function.



```http
  POST /api/package/auth/verify-otp
```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Content-Type`    | `application/json` | |



### Request Body
| Parameter   | Type     | Description                |
| :--------   | :------- | :------------------------- |
| `otp`    | `number` | *Required*|
| `mobile`    | `number` | *Required*|
| `country_code`    | `number` | *Required*|



---
---
---
---
---
---

### Logout
- This api is used to logout user and it will destroy sanctum token of that user.



```http
  POST /api/package/auth/logout

```

### Request Headers
| Parameter   | Type     | Description                
| :--------   | :------- | :------------------------- 
| `Authorization`    | `Bearer {Token}` | |



---
---
---
---
---
---