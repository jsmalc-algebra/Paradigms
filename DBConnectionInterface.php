<?php

interface DBConnectionInterface
{
    public function Connect();
    public function Execute($Q);
    public function Disconnect();
    public function FetchLastInsertID();
    public function EscapeString($string): string;
    public function ensureConnected();


}