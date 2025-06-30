<?php

interface LogableActionsInterface
{
    public function UserCreation(MySQLiConfig $db, $Q);
    public function UserLogin($id, MySQLiConfig $db);
    public function UserLogout(MySQLiConfig $db, $id);
    public function AnonymousUserLogin(MySQLiConfig $db);
    public function AnonymousUserLogout(MySQLiConfig $db);
    public function UserPhotoUpload(MySQLiConfig $db, $Q, $user_id);
    public function UserPhotoDownload(MySQLiConfig $db, $u_id, $id);
    public function UserPhotoEdit(MySQLiConfig $db, $query, $u_id, $id);
    public function AdminUserEdit(MySQLiConfig $db, $sql, $admin_id, $u_id);
    public function UserPlanChange(MySQLiConfig $db, $Q, $u_id);
    public function AdminPhotoDelete(MySQLiConfig $db, $Q, $u_id);
}