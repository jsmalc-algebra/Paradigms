<?php

class AuthorizationDecorator{
    private $service;
    private $userRole;

    public function __construct($service, $userRole) {
        $this->service = $service;
        $this->userRole = $userRole;
    }

    public function dashboard_acess(){
        return $this->userRole ==1;
    }
}