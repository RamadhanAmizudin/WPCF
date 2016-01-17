<?php

class WPCF {
    var $url, $sc, $xmlrpc_path;
    var $xmlrpc_url = false;

    function setURL($url) {
        $this->url = rtrim($url, '/');
    }

    function isWP() {
        $this->sc = HTTPRequest($this->url);
        preg_match('/x-pingback: (.+)/i', $this->sc, $xmlrpc);
        $this->xmlrpc_path = (isset($xmlrpc[1])) ? trim($xmlrpc[1]) : false;
        if(preg_match('#wp-content#i', $this->sc)) {
            return true;
        } else {
            $resp = HTTPRequest($this->xmlrpc_path);
            if(preg_match('#XML-RPC server accepts POST requests only#i', $resp)) {
                $this->xmlrpc_url = $this->xmlrpc_path;
                return true;
            }
        }
        return false;
    }

    function xmlrpc_path() {

        if( $this->xmlrpc_url ) {
            return $this->xmlrpc_url;
        }

        if( !$this->xmlrpc_path ) {
            $this->xmlrpc_path = $this->url . '/xmlrpc.php';
        }

        $resp = HTTPRequest($this->xmlrpc_path);
        if(preg_match('#XML-RPC server accepts POST requests only#i', $resp)) {
            $this->xmlrpc_url = $this->xmlrpc_path;
            return $this->xmlrpc_url;
        }

        return false;
    }

    function pingback_request($remote_url, $blogpost_url) {
        $xml  = '<?xml version="1.0" encoding="iso-8859-1"?>';
        $xml .= '<methodCall>';
        $xml .= '<methodName>pingback.ping</methodName>';
        $xml .= '<params>';
        $xml .= "<param><value><string>{$remote_url}</string></value></param>";
        $xml .= "<param><value><string>{$blogpost_url}</string></value></param>";
        $xml .= '</params>';
        $xml .= '</methodCall>';
        return HTTPRequest($this->xmlrpc_path(), true, $xml);
    }

    function getValidPost() {
        $feed_url = $this->url . '/?feed=rss2';
        $feed = HTTPRequest($feed_url);
        preg_match_all('/<link>([^<]+)<\/link>/i', $feed, $match);
        if( !isset($match[1]) ) {
            return false;
        }
        $posturls = $match[1];
        unset($posturls[0]);
        foreach( $posturls as $url ) {
            $resp = $this->pingback_request('http://www.google.com/', $url);
            if(!preg_match('/<value><int>33<\/int><\/value>/i', $resp) AND
               // !preg_match('/<name>faultCode\<\/name>/i', $resp) AND
                preg_match('/200 ok/i', $resp)) {
                return $url;
            }
        }
        return false;
    }

    function getInitialIP() {
        $hostname = parse_url($this->url, PHP_URL_HOST);
        $ip = gethostbyname($hostname);
        if( $ip == $hostname ) {
            return false;
        }
        return $ip;
    }

    function getStatus($id, $hash) {
        global $mysqli;
        $query = sprintf("SELECT id, uniqid, status, resolved_ip FROM tbl_url WHERE id = %1\$d AND uniqid = '%2\$s'", intval($id), $mysqli->clean($hash));
        $q = $mysqli->query($query);
        if( $q->num_rows > 0 ) {
            return $q->fetch_object();
        }
        return false;
    }

    function setError($id, $hash) {
        global $mysqli;
        $query = sprintf("UPDATE tbl_url SET status = 3 WHERE id = %1\$d AND uniqid = '%2\$s'", $id, $hash);
        $mysqli->query($query);
    }

    function saveData($id, $hash, $ip) {
        global $mysqli;
        $query = sprintf("UPDATE tbl_url SET resolved_ip = '%1\$s', status = 2 WHERE id = %2\$d AND uniqid = '%3\$s'", $ip, $id, $hash);
        $mysqli->query($query);
    }

    function setInitialData($data) {
        global $mysqli;
        $hash = md5(str_shuffle(uniqid() . md5(time()) . rand_str(rand(4, 10))));
        $query = sprintf("INSERT INTO tbl_url SET uniqid = '%1\$s', blog_url = '%2\$s', valid_post_url = '%3\$s', xmlrpc_url = '%4\$s', initial_ip = '%5\$s', user_ip = '%6\$s', user_ua = '%7\$s';",
            $hash,
            $mysqli->clean($this->url),
            $mysqli->clean($data['valid_url']),
            $mysqli->clean($data['xmlrpc_url']),
            $data['initial_ip'],
            server('REMOTE_ADDR'),
            $mysqli->clean(server('HTTP_USER_AGENT')));

        $mysqli->query( $query );

        return array('hash' => $hash, 'id' => $mysqli->insert_id);
    }

}

function HTTPRequest($url = '', $post = false, $postfield = '', $follow_redirection = true) {
    $options = array(
        CURLOPT_HEADER => 1,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT => (server('HTTP_USER_AGENT')) ? server('HTTP_USER_AGENT') : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/6.0)'
    );

    if ( $follow_redirection ) {
        $options[CURLOPT_FOLLOWLOCATION] = 1;
    }

    if ( $post && $postfield != '' ) {
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = $postfield;
    }

    $handle = curl_init($url);
    $ok = false;
    $data = "";
    if ( is_resource($handle) ) {
        if ( curl_setopt_array($handle, $options) != false ) {
            if ( ($data = curl_exec($handle)) != false ) {
                $ok = true;
            }
        }
    }
    curl_close($handle);
    return ( $ok ? $data : false );
}
