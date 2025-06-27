<?php
require_once "User.php";
require_once "UserBuilder.php";
class BrandNewLocalUser implements UserBuilder
{
    private User $user;

    public function getUser(): User
    {
        $new_user = $this->user;
        $this->reset();
        return $new_user;
    }

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->user = new User();
        return $this;
    }

    public function setEmail($email)
    {
        $this->user->setEmail($email);
        return $this;
    }

    public function setPassword($password)
    {
        $this->user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        return $this;
    }

    public function setUsername($username)
    {
        $this->user->setUsername($username);
        return $this;
    }

    public function setUserTier($userTier)
    {
        $this->user->setUserTier($userTier);
        return $this;
    }

    public function setProfilePicture($profilePicture)
    {
        $this->user->setPfp($profilePicture);
        return $this;
    }

    public function setWeeklyPicturesUploaded($weeklyPicturesUploaded)
    {
        $this->user->setWeeklyPicturesUploaded($weeklyPicturesUploaded);
        return $this;
    }

    public function setUserType($userType)
    {
        $this->user->setUserType($userType);
        return $this;
    }

    public function setUserRole($userRole)
    {
        $this->user->setUserRole($userRole);
        return $this;
    }
}