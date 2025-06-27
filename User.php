<?php
require_once "ConnectToDatabase.php";
require_once "patterns/LogableActions.php";
require_once "patterns/LoggingActions.php";

class User
{
    private $email;
    private $password;
    private $username;
    private $user_tier;
    private $user_role;
    private $pfp;
    private $weekly_pictures_uploaded;
    private $user_type;

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->user_type;
    }

    /**
     * @param mixed $user_type
     */
    public function setUserType($user_type): void
    {
        $this->user_type = $user_type;
    }

    public function __construct() {
//        $this->email = $email;
//        $this->username = $username;
//        $this->user_tier = $user_tier;
//        $this->user_role = $user_role;
//        $this->password = password_hash($password,CRYPT_BLOWFISH);
//        $this->weekly_pictures_uploaded = $weekly_pictures_uploaded;
//        $this->pfp = $pfp;
//        $this->creration_date = $creration_date;
//        $this->user_type = $type;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return false|string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param false|string|null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUserTier()
    {
        return $this->user_tier;
    }

    /**
     * @param mixed $user_tier
     */
    public function setUserTier($user_tier)
    {
        $this->user_tier = $user_tier;
    }

    /**
     * @return mixed
     */
    public function getUserRole()
    {
        return $this->user_role;
    }

    /**
     * @param mixed $user_role
     */
    public function setUserRole($user_role)
    {
        $this->user_role = $user_role;
    }

    /**
     * @return mixed
     */
    public function getPfp()
    {
        return $this->pfp;
    }

    /**
     * @param mixed $pfp
     */
    public function setPfp($pfp)
    {
        $this->pfp = $pfp;
    }

    /**
     * @return mixed
     */
    public function getCrerationDate()
    {
        return $this->creration_date;
    }

    /**
     * @param mixed $creration_date
     */
    public function setCrerationDate($creration_date)
    {
        $this->creration_date = $creration_date;
    }

    /**
     * @return mixed
     */
    public function getWeeklyPicturesUploaded()
    {
        return $this->weekly_pictures_uploaded;
    }

    /**
     * @param mixed $weekly_pictures_uploaded
     */
    public function setWeeklyPicturesUploaded($weekly_pictures_uploaded)
    {
        $this->weekly_pictures_uploaded = $weekly_pictures_uploaded;
    }

    public function addToDB(ConnectToDatabase $db){
        $esc_usr = $db->escapeString($this->username);
        $esc_pwd = $db->escapeString($this->password);
        if (!$this->pfp) $bool = 0;
        else $bool = 1;
        $Q="INSERT INTO users (username, password, email,profile_picture,plan_id,user_login_type)
        VALUES('$esc_usr','$esc_pwd','$this->email',$bool,$this->user_tier,$this->user_type)";

        $action_class = new LogableActions();
        $logger = new LoggingActions($action_class);

        return $logger->UserCreation($db,$Q);
    }

    public function addConsumption(ConnectToDatabase $db,$id)
    {
        $sql = "UPDATE `users` SET `weekly pictures uploaded` = `weekly pictures uploaded`+1 WHERE `users`.`id` = $id;";
        $db->Execute($sql);
        $this->weekly_pictures_uploaded += 1;
    }

    public function alterDB(ConnectToDatabase $db,$admin_id,$u_id)
    {
        if (!$this->pfp) $bool = 0;
        else $bool = 1;

        $sql = "UPDATE users SET username = '$this->username',profile_picture = '$bool',plan_id = '$this->user_tier' WHERE id=$u_id;";

        (new LoggingActions(new LogableActions()))->AdminUserEdit($db,$sql,$admin_id,$u_id);
    }
}