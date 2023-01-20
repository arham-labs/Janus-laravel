<?php

namespace Arhamlabs\Authentication\Interfaces;

interface AuthRegistrationALInterface
{
    //User registration using email,password
    public function register($request);

}
