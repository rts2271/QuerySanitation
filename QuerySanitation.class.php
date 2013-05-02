<?php
/*
/ Web Objects Framework
/ Copyright 2012
/ ralph@globalmediaworx.com
*/
namespace QuerySanitation;
class QuerySanitation extends AbstractQuerySanitation implements InterfaceQuerySanitation{
    protected $dirtyPostVars 	= null;
    protected $dirtyGetVars 	= null;
    /*
     *  __construct 
	 *  Loads the $_GET and $_POST to 
	 *  $this->dirtyPostVars or $this->dirtyGetVars
	 *  for sanitization and validation.
     *  @PARAMS none
     *  @return none
     */
    public function __construct(){
            if(isset($_GET)){ $this->dirtyGetVars = $_GET; }
            if(isset($_POST)){ $this->dirtyPostVars = $_POST; }		
		$_POST = null;
		$_GET  = null;
    }
    /*
     *  query_sanitize 
     *  @PARAMS string ($url)
     *  string $key
     *  int    $maxlength
     *  string $type
     *  string $method
     *  mixed  $dirtyvar
     *  @return mixed
     */ 
    public function query_sanitize($key, $maxlength=30, $type, $method="post", $dirtyvar=null){
            if($method=="post"){
		if(isset($this->dirtyPostVars[$key])){
		$pvar=trim($this->dirtyPostVars[$key]);
		}else{
		return null;
		}			
            }
            if($method=="get"){ 
                if(isset($this->dirtyGetVars[$key])){
		$pvar=trim($this->dirtyGetVars[$key]); 
		}else{
		return null;
		}
            }
            if ($type == 'float' || $type == 'bool' || $type == 'url' || $type == 'int' || $type == 'string' || $type == 'html'){
            $len = strlen($pvar);
                if(($len > 1 )AND( $len < $maxlength)){
                    if($type=="int"){
                    return $this->sanitize_int($pvar);
                    }                    
                    if($type=="float"){
                    return $this->sanitize_float($pvar);
                    }
                    if($type=="url"){
                    return $this->sanitize_url($pvar);				
                    }
                    if($type=="string"){
                    return $this->sanitize_string($pvar);
                    }			
                    if($type=="html"){
                    return $this->sanitize_html($pvar);    
                    }
                    if($type="bool"){
                    return $this->valid_bool($pvar);
                    }                    
                }else{
		return false;
		}
            }
            if($type=="array"){
            }
    }
    /*
     *  query_validate 
     *  @PARAMS
     *  string $type
     *  mixed $dirtyvar
     *  @return mixed
     */ 
    private function query_validate($type,$dirtyvar){
            if($type=="email"){
                return $this->validate_email($dirtyvar);
            }
            if($type="pass"){
                if (preg_match('/\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{6,}\z/', $dirtyvar)) {
		return true;
		}
            }
        return false;
    }
    /*
     *
     *  validate_email 
     *  @PARAMS string ($temp_email)
     *  @return string ($valid_email)
     */ 
    private function validate_email($temp_email){
	// trim() the entered E-Mail 
	$str_trimmed = trim($temp_email); 
	// find the @ position 
	$at_pos = strrpos($str_trimmed, "@"); 
	// find the . position 
	$dot_pos = strrpos($str_trimmed, "."); 
	// this will cut the local part and return it in $local_part 
	$local_part = substr($str_trimmed, 0, $at_pos); 
	// this will cut the domain part and return it in $domain_part 
	$domain_part = substr($str_trimmed, $at_pos); 
            if(!isset($str_trimmed) || is_null($str_trimmed) || empty($str_trimmed) || $str_trimmed == "") { 
            $this->email_status = "You must insert something"; 
            return false; 
            } 
            elseif(!$this->valid_local_part($local_part)) { 
            $this->email_status = "Invalid E-Mail Address1"; 
            return false; 
            } 
            elseif(!$this->valid_domain_part($domain_part)) { 
            $this->email_status = "Invalid E-Mail Address2"; 
            return false; 
            } 
            elseif($at_pos > $dot_pos) { 
            $this->email_status = "Invalid E-Mail Address3"; 
            return false; 
            } 
            elseif(!$this->valid_local_part($local_part)) { 
            $this->email_status = "Invalid E-Mail Address4"; 
            return false; 
            } 
            elseif(($str_trimmed[$at_pos + 1]) == ".") { 
            $this->email_status = "Invalid E-Mail Address5"; 
            return false; 
            } 
            elseif(!preg_match("/[(@)]/", $str_trimmed) || !preg_match("/[(.)]/", $str_trimmed)) { 
            $this->email_status = "Invalid E-Mail Address6"; 
            return false; 
            } 
            else{ 
            $this->email_status = ""; 
            return true; 
            }
    }
    /*
     *
     *  SanitizeURL 
     *  @PARAMS string ($url)
     *  @return string ($clean_url)
     */ 
    private function valid_dot_pos($email) { 
        $str_len = strlen($email); 
            for($i=0; $i<$str_len; $i++) { 
            $current_element = $email[$i]; 
                if($current_element == "." && ($email[$i+1] == ".")) { 
                return false; 
                break; 
                }
            }
        return true; 
        } 
    /*
     *
     *  SanitizeURL 
     *  @PARAMS string ($url)
     *  @return string ($clean_url)
     */ 
    private function valid_local_part($local_part) { 
        if(preg_match("/[^a-zA-Z0-9-_@.!#$%&'*\/+=?^`{\|}~]/", $local_part)) { 
        return false; 
        }else { 
        return true; 
        } 
    } 
    /*
     *
     *  SanitizeURL 
     *  @PARAMS string ($url)
     *  @return string ($clean_url)
     */ 
    private function valid_domain_part($domain_part){
            if(preg_match("/[^a-zA-Z0-9@#\[\].]/", $domain_part)){ 
            }elseif(preg_match("/[@]/", $domain_part) && preg_match("/[#]/", $domain_part)){ 
            }elseif(preg_match("/[\[]/", $domain_part) || preg_match("/[\]]/", $domain_part)){ 
                $dot_pos = strrpos($domain_part, "."); 
                if(($dot_pos < strrpos($domain_part, "]")) || (strrpos($domain_part, "]") < strrpos($domain_part, "["))){ 
                }
            } 
        $status=$this->url_exists($domain_part);
	return $status;
        } 
    /*
     *
     *  SanitizeURL 
     *  @PARAMS string ($url)
     *  @return string ($clean_url)
     */   
    private function url_exists($url) {
        $resURL = curl_init();
        curl_setopt($resURL, CURLOPT_URL, $url);
        curl_setopt($resURL, CURLOPT_BINARYTRANSFER, 0);
        curl_setopt($resURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($resURL, CURLOPT_FAILONERROR, 1);
        curl_exec ($resURL);
        $intReturnCode = curl_getinfo($resURL, CURLINFO_HTTP_CODE);
        curl_close ($resURL);
	        if ($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304 && $intReturnCode != 301) {
	        return false;
	        }else{
	        return true;
	        }
        }
    /*
     *
     *  sanitize_url 
     *  @PARAMS string ($url)
     *  @return string ($clean_url)
     */    
    private function sanitize_url($url){
        $valid_url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        $clean_url = filter_var($valid_url, FILTER_SANITIZE_URL);
        return $clean_url;
    }
    /*
     *  sanitize_float 
     *  @PARAMS float ($data)
     *  @return float ($clean_data)
     */    
    private function sanitize_float($data){
	$valid_data = filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
	$clean_data = filter_var($valid_data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $clean_data;
    }
    /*
     *  sanitize_string 
     *  @PARAMS float ($data)
     *  @return string ($clean_string)
     */     
    private function sanitize_string($data){
        $clean_string = (string)filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        return $clean_string;
    }
    /*
     *  sanitize_int 
     *  @PARAMS float ($data)
     *  @return int ($clean_int)
     */     
    private function sanitize_int($data){
        $valid_data = filter_var($data, FILTER_VALIDATE_INT);
        $clean_data = filter_var($valid_data, FILTER_SANITIZE_NUMBER_INT);
        return (int)$clean_data;  
    }    
    /*
     *  valid_bool 
     *  @PARAMS bool ($data)
     *  @return bool ($valid_bool)
     */     
    private function valid_bool($data){
        return filter_var($data, FILTER_VALIDATE_BOOLEAN);
    } 
    
		    
}