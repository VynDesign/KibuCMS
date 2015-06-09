<?php

/**
 * Description of Feedback
 *
 * @author vyn
 */

	require_once './kibu/core/framework/template/Template.php';

	final class FeedbackTypes {
		const General = 0;
		const Warning = 1;
		const Error = 2;
		const Success = 3;
	}

	class Feedback {
		protected $msg;
		protected $type;
		protected $template;
		protected $msgOutput;

		public function __construct($msg = null, $type = FeedbackTypes::General) {
			$this->msg = $msg;
			$this->type = $type;
		}
	
		public function __get($name) {
			return $this->$name;
		}
		
		public function SetMsg($msg) {
			$this->msg = $msg;
		}
		
		public function SetType($type = FeedbackTypes::General) {
			$this->type = $type;
		}

		public function getMsg() {
			if($this->msg != null) {
				$this->_chooseTemplate();							
				$msgTpl = new Template('./kibu/core/framework/html/form/templates/');
				$msgTpl->set('msg', $this->msg);		
				$this->msgOutput = $msgTpl->fetch($this->template);
				return $this->msgOutput;
			}
		}	
	
		private function _chooseTemplate() {
			switch($this->type) {
				case FeedbackTypes::Warning : $this->template = 'form_feedback_warning.tpl.php';
					break;
				case FeedbackTypes::Error : $this->template = is_array($this->msg) ? 'form_feedback_multi_error.tpl.php' : 'form_feedback_error.tpl.php';
					break;
				case FeedbackTypes::Success : $this->template = 'form_feedback_success.tpl.php';
					break;
				case FeedbackTypes::General :
					default : $this->template = "form_feedback_msg.tpl.php";
			}
		}
	}
?>
