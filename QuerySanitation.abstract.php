<?php
/*
/ Web Objects Framework
/ Copyright 2012
/ ralph@globalmediaworx.com
*/
namespace QuerySanitation;
abstract class AbstractQuerySanitation{
	abstract public function query_sanitize($key, $maxlength, $type, $method, $dirtyvar);
}