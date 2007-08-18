<?php

/*
Copyright (c) 2007, Till Brehm, projektfarm Gmbh
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/*
	This function returns a string that describes the installed
	linux distribution. e.g. debian40 for Debian Linux 4.0

*/



/*
Comments to completion forever ;-)
commandline arguments
$argv[1]


<?
echo "Total argument passed are : $argc \n";
for( $i = 0 ; $i <= $argc -1 ;$i++)
{
echo "Argument $i : $argv[$i] \n";
}
?> 

*/
error_reporting(E_ALL|E_STRICT);


$FILE = realpath('../install.php');

function get_distname() {
	global $conf; // TODO wtf ?
	$distname = $conf['distname'];
	return $distname;
}

function sread() {
    $f = fopen('/dev/stdin', 'r');
    $input = fgets($f, 255);
    fclose($f);
    return rtrim($input);
}

function swrite($text = '') {
	echo $text;
}

function swriteln($text = '') {
	echo $text."\n";
}

function ilog($msg){
	$logfile = '/var/log/ispconfig_install.log';
  	exec("echo `date` \"- [ISPConfig] - \"".$msg." >> ".$logfile);
}

function error($msg){
	ilog($msg);
	die($msg."\n");
}

function caselog($command, $file = '', $line = '', $success = '', $failure = ''){
	exec($command,$arr,$ret_val);
	$arr = NULL;
	if(!empty($file) && !empty($line)){
		$pre = $file.', Line '.$line.': ';
	} else {
		$pre = '';
	}
	if($ret_val != 0){
		if($failure == '') $failure = 'could not '.$command;
		ilog($pre.'WARNING: '.$failure);
	} else {
		if($success == '') $success = $command;
		ilog($pre.$success);
	}
}

function phpcaselog($ret_val, $msg, $file = '', $line = ''){
	if(!empty($file) && !empty($line)){
		$pre = $file.', Line '.$line.': ';
	} else {
		$pre = '';
	}
	if($ret_val == true){
		ilog($pre.$msg);
	} else {
		ilog($pre.'WARNING: could not '.$msg);
	}
	return $ret_val;
}

function mkdirs($strPath, $mode = '0755'){
	if(isset($strPath) && $strPath != ""){
		//* Verzeichnisse rekursiv erzeugen
		if(is_dir($strPath)){
			return true;
		}
		$pStrPath = dirname($strPath);
		if(!mkdirs($pStrPath, $mode)){
			return false;
		}
		$old_umask = umask(0);
		$ret_val = mkdir($strPath, octdec($mode));
		umask($old_umask);
		return $ret_val;
	}
	return false;
}

function rf($file){
	clearstatcache();
	if(!$fp = fopen ($file, 'rb')){
		ilog('WARNING: could not open file '.$file);
	}
	return filesize($file) > 0 ? fread($fp, filesize($file)) : '';
}

function wf($file, $content){
	mkdirs(dirname($file));
	if(!$fp = fopen ($file, 'wb')){
		ilog('WARNING: could not open file '.$file);
	}
	fwrite($fp, $content);
	fclose($fp);
}

function af($file, $content){
	mkdirs(dirname($file));
	if(!$fp = fopen ($file, 'ab')){
		ilog('WARNING: could not open file '.$file);
	}
	fwrite($fp,$content);
	fclose($fp);
}

function aftsl($file, $content){
	if(!$fp = fopen ($file, 'ab')){
		ilog('WARNING: could not open file '.$file);
	}
	fwrite($fp,$content);
	fclose($fp);
}

function unix_nl($input){
	$output = str_replace("\r\n", "\n", $input);
	$output = str_replace("\r", "\n", $output);
	return $output;
}

function remove_blank_lines($input, $file = 1){
	//TODO ? Leerzeilen l�schen
	if($file){
		$content = unix_nl(rf($input)); // WTF -pedro !
	}else{
		$content = $input;
	}
	$lines = explode("\n", $content);
	if(!empty($lines)){
		foreach($lines as $line){
			if(trim($line) != '') $new_lines[] = $line;
		}
	}
	if(is_array($new_lines)){
		$content = implode("\n", $new_lines);
	} else {
		$content = '';
	}
	if($file){
		wf($input, $content);
	}else{
		return $content;
	}
}

function no_comments($file, $comment = '#'){
	$content = unix_nl(rf($file));
	$lines = explode("\n", $content);
	if(!empty($lines)){
		foreach($lines as $line){
			if(strstr($line, $comment)){
				$pos = strpos($line, $comment);
				if($pos != 0){
					$new_lines[] = substr($line,0,$pos);
				}else{
					$new_lines[] = '';
				}
			}else{
				$new_lines[] = $line;
			}
		}
	}
	if(is_array($new_lines)){
		$content_without_comments = implode("\n", $new_lines);
		$new_lines = NULL;
		return $content_without_comments;
	} else {
		return '';
	}
}

function find_includes($file){
  global $httpd_root;
  clearstatcache();
  if(is_file($file) && filesize($file) > 0){
    $includes[] = $file;
    $inhalt = unix_nl(no_comments($file));
    $lines = explode("\n", $inhalt);
    if(!empty($lines)){
      foreach($lines as $line){
        if(stristr($line, "include ")){
          $include_file = str_replace("\n", "", trim(shell_exec("echo \"$line\" | awk '{print \$2}'")));
          if(substr($include_file,0,1) != "/"){
            $include_file = $httpd_root."/".$include_file;
          }
          if(is_file($include_file)){
            if($further_includes = find_includes($include_file)){
              $includes = array_merge($includes, $further_includes);
            }
          } else {
            if(strstr($include_file, "*")){
              $more_files = explode("\n", shell_exec("ls -l $include_file | awk '{print \$9}'"));
              if(!empty($more_files)){
                foreach($more_files as $more_file){
                  if(is_file($more_file)){
                    if($further_includes = find_includes($more_file)){
                      $includes = array_merge($includes, $further_includes);
                    }
                  }
                }
              }
              unset($more_files);
              $more_files = explode("\n", shell_exec("ls -l $include_file | awk '{print \$10}'"));
              if(!empty($more_files)){
                foreach($more_files as $more_file){
                  if(is_file($more_file)){
                    if($further_includes = find_includes($more_file)){
                      $includes = array_merge($includes, $further_includes);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
  if(is_array($includes)){
    $includes = array_unique($includes);
    return $includes;
  } else {
    return false;
  }
}

function comment_out($file, $string){
	$inhalt = no_comments($file);
	$gesamt_inhalt = rf($file);
	$modules = explode(',', $string);
	foreach($modules as $val){
		$val = trim($val);
		if(strstr($inhalt, $val)){
			$gesamt_inhalt = str_replace($val, '##ISPConfig INSTALL## '.$val, $gesamt_inhalt);
		}
	}
	wf($file, $gesamt_inhalt);
}

function is_word($string, $text, $params = ''){
  //* params: i ??
  return preg_match("/\b$string\b/$params", $text);
  /*
  if(preg_match("/\b$string\b/$params", $text)) {
    return true;
  } else {
    return false;
  }
  */
}

function grep($content, $string, $params = ''){
  // params: i, v, w
  $content = unix_nl($content);
  $lines = explode("\n", $content);
  foreach($lines as $line){
    if(!strstr($params, 'w')){
      if(strstr($params, 'i')){
        if(strstr($params, 'v')){
          if(!stristr($line, $string)) $find[] = $line;
        } else {
          if(stristr($line, $string)) $find[] = $line;
        }
      } else {
        if(strstr($params, 'v')){
          if(!strstr($line, $string)) $find[] = $line;
        } else {
          if(strstr($line, $string)) $find[] = $line;
        }
      }
    } else {
      if(strstr($params, 'i')){
        if(strstr($params, 'v')){
          if(!is_word($string, $line, 'i')) $find[] = $line;
        } else {
          if(is_word($string, $line, 'i')) $find[] = $line;
        }
      } else {
        if(strstr($params, 'v')){
          if(!is_word($string, $line)) $find[] = $line;
        } else {
          if(is_word($string, $line)) $find[] = $line;
        }
      }
    }
  }
  if(is_array($find)){
    $ret_val = implode("\n", $find);
    if(substr($ret_val,-1) != "\n") $ret_val .= "\n";
    $find = NULL;
    return $ret_val;
  } else {
    return false;
  }
}

function edit_xinetd_conf($service){
	$xinetd_conf = '/etc/xinetd.conf';
	$contents = unix_nl(rf($xinetd_conf));
	$lines = explode("\n", $contents);
	$j = sizeof($lines);
	for($i=0;$i<sizeof($lines);$i++){
		if(grep($lines[$i], $service, 'w')){
			$fundstelle_anfang = $i;
			$j = $i;
			$parts = explode($lines[$i], $contents);
		}
		if($j < sizeof($lines)){
			if(strstr($lines[$i], '}')){
				$fundstelle_ende = $i;
				$j = sizeof($lines);
			}
		}
	}
	if(isset($fundstelle_anfang) && isset($fundstelle_ende)){
		for($i=$fundstelle_anfang;$i<=$fundstelle_ende;$i++){
			if(strstr($lines[$i], 'disable')){
				$disable = explode('=', $lines[$i]);
				$disable[1] = ' yes';
				$lines[$i] = implode('=', $disable);
			}
		}
	}
	$fundstelle_anfang = NULL;
	$fundstelle_ende = NULL;
	$contents = implode("\n", $lines);
	wf($xinetd_conf, $contents);
}

?>