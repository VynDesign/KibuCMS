<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManager
 *
 * @author vyn
 */
class FileManager {

		public function __construct() {

		}

		private function fileHandler($assetID) {
				$files = $this->_files;
				$namereverse = array_reverse(explode('.', $files['file']['name'][$assetID]));
				$ext = $namereverse[0];
				if($ext == 'jpg' | $ext == 'jpeg' | $ext == 'png' | $ext == 'gif') {
						$dir = 'images';
				}
				elseif($ext == 'flv' | $ext == 'swf') {
						$dir = 'flash';
				}
				elseif($ext == 'wmv' | $ext == 'mov') {
						$dir = 'media';
				}
				elseif($ext == 'doc' | $ext == 'docx' | $ext == 'xls' | $ext == 'xlsx' | $ext == 'pdf') {
						$dir = 'docs';
				}
				else {
						$this->_msg = 'Unrecognized file type';
				}
				$uploadDir = 'site_resources/'.$dir.'/';
				$file = $files['file']['name'][$assetID];
				$tempLocation = $files['file']['tmp_name'][$assetID];
				$uploadLocation = $uploadDir . $file;
				$move = move_uploaded_file($tempLocation, $uploadLocation);
				if($move) {
						return $uploadLocation;
				}
				else {
						$this->_msg = 'Moving file '.$file.' to permanent location failed';
				}
		}
}
?>
