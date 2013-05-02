<?php
/*
/ Web Objects Framework
/ Copyright 2012
/ ralph@globalmediaworx.com
*/
namespace QuerySanitation;
interface InterfaceQuerySanitation{
	public function query_sanitize($key, $maxlength, $type, $method, $dirtyvar);
}