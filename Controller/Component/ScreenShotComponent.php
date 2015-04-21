<?php
/*
-----------------------------------------------------------------------------
 Usage: CutyCapt --url=http://www.example.org/ --out=localfile.png            
 -----------------------------------------------------------------------------
  --help                         Print this help page and exit                
  --url=<url>                    The URL to capture (http:...|file:...|...)   
  --out=<path>                   The target file (.png|pdf|ps|svg|jpeg|...)   
  --out-format=<f>               Like extension in --out, overrides heuristic 
  --min-width=<int>              Minimal width for the image (default: 800)   
  --min-height=<int>             Minimal height for the image (default: 600)  
  --max-wait=<ms>                Don't wait more than (default: 90000, inf: 0)
  --delay=<ms>                   After successful load, wait (default: 0)     
  --user-style-path=<path>       Location of user style sheet file, if any    
  --user-style-string=<css>      User style rules specified as text           
  --header=<name>:<value>        request header; repeatable; some can't be set
  --method=<get|post|put>        Specifies the request method (default: get)  
  --body-string=<string>         Unencoded request body (default: none)       
  --body-base64=<base64>         Base64-encoded request body (default: none)  
  --app-name=<name>              appName used in User-Agent; default is none  
  --app-version=<version>        appVers used in User-Agent; default is none  
  --user-agent=<string>          Override the User-Agent header Qt would set  
  --javascript=<on|off>          JavaScript execution (default: on)           
  --java=<on|off>                Java execution (default: unknown)            
  --plugins=<on|off>             Plugin execution (default: unknown)          
  --private-browsing=<on|off>    Private browsing (default: unknown)          
  --auto-load-images=<on|off>    Automatic image loading (default: on)        
  --js-can-open-windows=<on|off> Script can open windows? (default: unknown)  
  --js-can-access-clipboard=<on|off> Script clipboard privs (default: unknown)
  --print-backgrounds=<on|off>   Backgrounds in PDF/PS output (default: off)  
  --zoom-factor=<float>          Page zoom factor (default: no zooming)       
  --zoom-text-only=<on|off>      Whether to zoom only the text (default: off) 
  --http-proxy=<url>             Address for HTTP proxy server (default: none)
  --insecure                     Ignore SSL/TLS certificate errors      

*/
App::uses('Component', 'Controller');

class ScreenShotComponent extends Component {
	
	public $options = array(
		'url' => null,
		'out' => null,
		'out-format' => 'png',
		'plugins' => 'on',
		'min-width' => 1200,
		'max-wait' => 10000,
		'delay' => 4000,
		'app-name' => 'ocbshot',
	);
	
	public function set($key = null, $val = null ){
		$this->options[$key] = $val;
	}
	
	public function capture($url = null, $out = null ){
		if(!empty($url)) $this->options['url'] = $url;
		if(!empty($out)) $this->options['out'] = $out;
		$cmd = "DISPLAY=:1 /usr/bin/CutyCapt/CutyCapt ";
		foreach( $this->options as $key => $val ){
			$cmd .= "--{$key}={$val} ";
		}
		
	
		return exec($cmd);
	}
	
}