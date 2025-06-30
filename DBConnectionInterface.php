<?php

interface DBConnectionInterface
{
    public function Connect();
    public function Execute($Q);
    public function Disconnect();


}