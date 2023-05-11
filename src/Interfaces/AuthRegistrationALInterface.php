<?php

namespace Arhamlabs\Authentication\Interfaces;

interface AuthRegistrationALInterface
{
    //User registration using email,password
    public function register($request);

    //update user registration in temporary table to verify
    public function verifyTemporaryRegistration($request);
}
