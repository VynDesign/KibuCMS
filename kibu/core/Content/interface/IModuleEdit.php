<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author vyn
 */
interface IModuleEdit {

		
	public function getSubmit();

	public function getError();
	
	public function getNextStep();
	
	public function setEditParamOpts();	


}

?>