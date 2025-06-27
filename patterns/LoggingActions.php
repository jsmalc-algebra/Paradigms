<?php
require_once "../ConnectToDatabase.php";
require_once "LogableActions.php";
require_once "LogableActionsInterface.php";
require_once "../functions.php";
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

    public function UserCreation(ConnectToDatabase $db,$Q)
    {
        $id = $this->logableActions->UserCreation($db,$Q);
        $log = "INSERT INTO logs (user_id, event) VALUES ($id,'USER CREATED')";
        $db->Execute($log);
        $db->close();
        return $id;
    }

    public function UserLogin($id, ConnectToDatabase $db)
    {
        $log ="INSERT INTO logs (user_id, event) VALUES ($id,'USER LOGIN')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserLogout(ConnectToDatabase $db, $id)
    {
        $log = "INSERT INTO logs (user_id,event) VALUES ($id,'USER LOGOUT')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function AnonymousUserLogin(ConnectToDatabase $db)
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
    public function UserPhotoUpload(ConnectToDatabase $db, $Q, $user_id)
    {
        $img_id = $this->logableActions->UserPhotoUpload($db,$Q,$user_id);
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($user_id, 'USER UPLOADED PHOTO', $img_id)";
        $db->Execute($log);
        $db->Close();
    }

    /**
     * @throws Exception
     */
    public function UserPhotoDownload(ConnectToDatabase $db, $u_id,$id)
    {
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($u_id,'USER DOWNLOADED PHOTO',$id)";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserPhotoEdit(ConnectToDatabase $db, $query, $u_id, $id)
    {
        $this->logableActions->UserPhotoEdit($db,$query,$u_id,$id);
        $log = "INSERT INTO logs (user_id, event, photo_id) VALUES ($u_id, 'USER EDITED PHOTO', '$id')";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function AdminUserEdit(ConnectToDatabase $db, $sql, $admin_id, $u_id)
    {
        $this->logableActions->AdminUserEdit($db,$sql,$admin_id,$u_id);
        $log = "INSERT INTO logs (user_id, event,altered_user_id) VALUES ($admin_id,'EDITED USER',$u_id)";
        $db->Execute($log);
    }

    /**
     * @throws Exception
     */
    public function UserPlanChange(ConnectToDatabase $db, $Q, $u_id)
    {
        $this->logableActions->UserPlanChange($db,$Q,$u_id);
        $log = "INSERT INTO logs (user_id, event) VALUES ('$u_id','USER CHANGED PLAN') ";
        $db->Execute($log);
        $db->Close();
    }

    public function AdminPhotoDelete(ConnectToDatabase $db, $Q, $u_id)
    {
        $this->logableActions->AdminPhotoDelete($db,$Q,$u_id);
        $log="INSERT INTO logs (user_id, event) VALUES ($u_id, 'DELETED PHOTO')";
        $db->Execute($log);
        $db->Close();
    }
}