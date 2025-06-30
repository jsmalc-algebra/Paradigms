<?php
require_once __DIR__ . "/LogableActionsInterface.php";
require_once __DIR__ . "/../ConnectToDatabase.php";

class LogableActions implements LogableActionsInterface
{

    public function UserCreation(ConnectToDatabase $db,$Q)
    {
        $db->Execute($Q);
        $id = $db->FetchLastInsertId();
        return $id;
    }

    public function UserLogin($id, ConnectToDatabase $db)
    {
        // login is handled by landing page, but still needs to be logged
    }

    public function UserLogout(ConnectToDatabase $db, $id)
    {
        // logout is handled by logout script, still needs to be logged
    }

    public function AnonymousUserLogin(ConnectToDatabase $db)
    {
        // handled by anonymous logger
    }

    public function AnonymousUserLogout(ConnectToDatabase $db)
    {
        // handled by logout
    }

    /**
     * @throws Exception
     */
    public function UserPhotoUpload(ConnectToDatabase $db, $Q, $user_id)
    {
        $db->Execute($Q);
        return $db->FetchLastInsertId();

    }

    public function UserPhotoDownload(ConnectToDatabase $db, $u_id, $id)
    {
        // handled by upload_image
    }

    /**
     * @throws Exception
     */
    public function UserPhotoEdit(ConnectToDatabase $db, $query, $u_id, $id)
    {
        $db->Execute($query);
    }

    /**
     * @throws Exception
     */
    public function AdminUserEdit(ConnectToDatabase $db, $sql, $admin_id, $u_id)
    {
        $db->Execute($sql);
    }

    /**
     * @throws Exception
     */
    public function UserPlanChange(ConnectToDatabase $db, $Q, $u_id)
    {
        $db->Execute($Q);
    }

    public function AdminPhotoDelete(ConnectToDatabase $db, $Q, $u_id)
    {
        $db->Connect();
        $db->Execute($Q);
    }
}