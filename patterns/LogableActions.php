<?php
require_once __DIR__ . "/LogableActionsInterface.php";
require_once __DIR__ . "/../MySQLiConfig.php";

class LogableActions implements LogableActionsInterface
{

    public function UserCreation(MySQLiConfig $db, $Q)
    {
        $db->Execute($Q);
        $id = $db->FetchLastInsertId();
        return $id;
    }

    public function UserLogin($id, MySQLiConfig $db)
    {
        // login is handled by landing page, but still needs to be logged
    }

    public function UserLogout(MySQLiConfig $db, $id)
    {
        // logout is handled by logout script, still needs to be logged
    }

    public function AnonymousUserLogin(MySQLiConfig $db)
    {
        // handled by anonymous logger
    }

    public function AnonymousUserLogout(MySQLiConfig $db)
    {
        // handled by logout
    }

    /**
     * @throws Exception
     */
    public function UserPhotoUpload(MySQLiConfig $db, $Q, $user_id)
    {
        $db->Execute($Q);
        return $db->FetchLastInsertId();

    }

    public function UserPhotoDownload(MySQLiConfig $db, $u_id, $id)
    {
        // handled by upload_image
    }

    /**
     * @throws Exception
     */
    public function UserPhotoEdit(MySQLiConfig $db, $query, $u_id, $id)
    {
        $db->Execute($query);
    }

    /**
     * @throws Exception
     */
    public function AdminUserEdit(MySQLiConfig $db, $sql, $admin_id, $u_id)
    {
        $db->Execute($sql);
    }

    /**
     * @throws Exception
     */
    public function UserPlanChange(MySQLiConfig $db, $Q, $u_id)
    {
        $db->Execute($Q);
    }

    public function AdminPhotoDelete(MySQLiConfig $db, $Q, $u_id)
    {
        $db->Connect();
        $db->Execute($Q);
    }
}