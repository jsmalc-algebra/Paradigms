<?php
    require_once '../User.php';
interface UserBuilder
{
    public function reset();
    public function setEmail($username);
    public function setPassword($password);
    public function setUsername($username);
    public function setUserTier($userTier);
    public function setUserRole($userRole);
    public function setProfilePicture($profilePicture);
    public function setWeeklyPicturesUploaded($weeklyPicturesUploaded);
    public function setUserType($userType);

}