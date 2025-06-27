<?php
require "BrandNewLocalUser.php";
require "LoginLocalUser.php";
require "ShowcasedUser.php";


class UserBuilderFactory
{
    public static function createBrandNewLocalUserBuilder(): BrandNewLocalUser
    {
        return new BrandNewLocalUser();
    }

    public static function createLoginUserBuilder(): LoginLocalUser
    {
        return new LoginLocalUser();
    }

    public static function createShowcasedUserBuilder(): ShowcasedUser
    {
        return new ShowcasedUser();
    }
}