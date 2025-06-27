<?php

interface LogableActionsInterface
{
    public function UserCreation(ConnectToDatabase $db,$Q);
    public function UserLogin($id,ConnectToDatabase $db);
    public function UserLogout(ConnectToDatabase $db, $id);
    public function AnonymousUserLogin(ConnectToDatabase $db);
    public function AnonymousUserLogout(ConnectToDatabase $db);
    public function UserPhotoUpload(ConnectToDatabase $db, $Q, $user_id);
    public function UserPhotoDownload(ConnectToDatabase $db, $u_id, $id);
    public function UserPhotoEdit(ConnectToDatabase $db,$query,$u_id,$id);
    public function AdminUserEdit(ConnectToDatabase $db,$sql,$admin_id,$u_id);
    public function UserPlanChange(ConnectToDatabase $db,$Q,$u_id);
    public function AdminPhotoDelete(ConnectToDatabase $db, $Q, $u_id);
}