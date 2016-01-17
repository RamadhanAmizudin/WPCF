<?php
require './init.php';

header('Content-type: text/plain');

if(request('action')) {
    switch(request('action')) {
        default:
                jperror('Invalid Action.');
            break;

        case 'error':
                $id = request('id');
                $hash = request('hash');

                if(!is_numeric($id)) {
                    jperror('Invalid ID.');
                    break;
                }

                if(!preg_match('/[a-f0-9]{32}/i', $hash)) {
                    jperror('Invalid Hash.');
                    break;
                }

                $info = $wpcf->getStatus($id, $hash);
                if( !$info ) {
                    jperror('Invalid Hash and ID.');
                    break;
                }
                $wpcf->setError($id, $hash);
            break;

        case 'check':
                $id = request('id');
                $hash = request('hash');

                if(!is_numeric($id)) {
                    jperror('Invalid ID.');
                    break;
                }

                if(!preg_match('/[a-f0-9]{32}/i', $hash)) {
                    jperror('Invalid Hash.');
                    break;
                }

                $info = $wpcf->getStatus($id, $hash);
                if( !$info ) {
                    jperror('Invalid Hash and ID.');
                    break;
                }

                switch($info->status) {
                    case 1:
                            jprint(array('error' => false, 'status' => $info->status, 'message' => 'Processing'));
                        break;

                    case 2:
                            jprint(array('error' => false, 'status' => $info->status, 'message' => 'Completed', 'data' => $info));
                        break;

                    case 3:
                            jprint(array('error' => false, 'status' => $info->status, 'message' => 'Error.'));
                        break;

                    default:
                            jperror('Unknown Status.');
                        break;
                }
            break;

        case 'submit':
                $url = request('url');

                if( !filter_var($url, FILTER_VALIDATE_URL) ) {
                    jperror('Invalid URL');
                    break;
                }

                $p = parse_url($url);
                $url = structure_url($p);
                $wpcf->setURL($url);

                if( !($initial_ip = $wpcf->getInitialIP()) ) {
                    jperror('Failed to resolve the domain.');
                    break;
                }

                if( !$wpcf->isWP() ) {
                    jperror('Remote url does not seem to be running WordPress.');
                    break;
                }

                if( !($xmlrpc_url =$wpcf->xmlrpc_path())) {
                    jperror('Unable to locate XMLRPC path.');
                    break;
                }

                if( !($valid_url = $wpcf->getValidPost()) ) {
                    jperror('No valid posts with pingback enabled found');
                    break;
                }

                $data = array('initial_ip' => $initial_ip,
                              'valid_url' => $valid_url,
                              'xmlrpc_url' => $xmlrpc_url);

                $info = $wpcf->setInitialData($data);

                $remote_url = SITE_URL . '/post/' . $info['id'] . '/' . $info['hash'] . '.html';
                $wpcf->pingback_request($remote_url, $valid_url);

                jprint(array('error' => false, 'message' => 'Processing..', 'data' => $info));
            break;
    }
} else {
    jperror('Invalid Action.');
}

function structure_url($parsed_url) {
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    return "$scheme$user$pass$host$port$path";
}
