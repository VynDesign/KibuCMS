<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author vyn
 */
interface IModule {
	
	public function loadModule();
	
	public function getModuleOutput();

	public function getSubmit();

	public function returnData();
	
	public function getError();

}

?>
