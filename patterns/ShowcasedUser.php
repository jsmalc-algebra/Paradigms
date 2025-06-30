<?php
require_once "UserBuilder.php";

class ShowcasedUser implements UserBuilder
{
    private User $user;

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->user = new User();
        return $this;
    }

    public function getUser(): User
    {
        $new_user = $this->user;
        $this->reset();
        return $new_user;
    }

    public function setEmail($username)
    {
        //Intentionally empty, email should not be used past login
    }

    public function setPassword($password)
    {
        //Intentionally empty as once logged in password is not necessary
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

    public function setUserRole($userRole)
    {
        //Only non admins can be showcased, no need for role
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
        // user types currently not implemented beyond local
    }
}