<?php

interface DBUtilityInterface
{
    public function FetchLastInsertID();
    public function EscapeString($string): string;
    public function ensureConnected();
}