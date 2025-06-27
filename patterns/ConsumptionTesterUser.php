<?php
require_once "UserBuilder.php";
class ConsumptionTesterUser implements UserBuilder
{

    private User $user;

    public function getUser(): User
    {
        $new_user = $this->user;
        $this->reset();
        return $new_user;
    }

    /**
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->user = new User();
        return $this;
    }

    public function setEmail($username)
    {
        // Intentionally empty
    }

    public function setPassword($password)
    {
        // Intentionally empty
    }

    public function setUsername($username)
    {
        // Intentionally empty
    }

    public function setUserTier($userTier)
    {
        $this->user->setUserTier($userTier);
        return $this;
    }

    public function setUserRole($userRole)
    {
        // Intentionally empty
    }

    public function setProfilePicture($profilePicture)
    {
        // Intentionally empty
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