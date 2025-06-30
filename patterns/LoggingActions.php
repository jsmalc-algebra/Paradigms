<?php
require_once __DIR__ . "/../MySQLiConfig.php";
require_once __DIR__ . "/LogableActions.php";
require_once __DIR__ . "/LogableActionsInterface.php";
require_once __DIR__ . "/../functions.php";
class LoggingActions implements LogableActionsInterface
{
    private LogableActions $logableActions;

    /**
     * @param LogableActions $logableActions
     */
    public function __construct(LogableActions $logableActions)
    {
        $this->logableActions = $logableActions;
    }

    public function UserCreation(MySQLiConfig $db, $Q)
    {
        $id = $this->logableActions->UserCreation($db,$Q);
        $log = "INSERT INTO logs (user_id, event) VALUES ($id,'USER CREATED')";
        $db->Execute($log);
        $db->Disconnect();
        return $id;
    }

    public function UserLogin($id, MySQLiConfig $db)
    {
        $log ="INSERT INTO logs (user_id, event) VALUES ($id,'USER LOGIN')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserLogout(MySQLiConfig $db, $id)
    {
        $log = "INSERT INTO logs (user_id,event) VALUES ($id,'USER LOGOUT')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function AnonymousUserLogin(MySQLiConfig $db)
    {
        $db->execute("INSERT INTO logs (event) VALUES('ANONYMOUS USER LOGIN')");
    }

    public function AnonymousUserLogout($db)
    {
        $log="INSERT INTO logs (event) VALUES('ANONYMOUS USER LOGOUT')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserPhotoUpload(MySQLiConfig $db, $Q, $user_id)
    {
        $img_id = $this->logableActions->UserPhotoUpload($db,$Q,$user_id);
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($user_id, 'USER UPLOADED PHOTO', $img_id)";
        $db->Execute($log);
        $db->Disconnect();
    }

    /**
     * @throws Exception
     */
    public function UserPhotoDownload(MySQLiConfig $db, $u_id, $id)
    {
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($u_id,'USER DOWNLOADED PHOTO',$id)";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserPhotoEdit(MySQLiConfig $db, $query, $u_id, $id)
    {
        $this->logableActions->UserPhotoEdit($db,$query,$u_id,$id);
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($u_id, 'USER EDITED PHOTO', '$id')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function AdminUserEdit(MySQLiConfig $db, $sql, $admin_id, $u_id)
    {
        $this->logableActions->AdminUserEdit($db,$sql,$admin_id,$u_id);
        $log = "INSERT INTO logs (user_id, event,altered_user_id) VALUES ($admin_id,'EDITED USER',$u_id)";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserPlanChange(MySQLiConfig $db, $Q, $u_id)
    {
        $this->logableActions->UserPlanChange($db,$Q,$u_id);
        $log = "INSERT INTO logs (user_id, event) VALUES ('$u_id','USER CHANGED PLAN') ";
        $db->Execute($log);
        $db->Disconnect();
    }

    public function AdminPhotoDelete(MySQLiConfig $db, $Q, $u_id)
    {
        $this->logableActions->AdminPhotoDelete($db,$Q,$u_id);
        $log="INSERT INTO logs (user_id, event) VALUES ($u_id, 'DELETED PHOTO')";
        $db->Execute($log);
        $db->Disconnect();
    }
}