<?php

class EmailHeader extends DynamicObject {

	protected $fieldName;
	protected $fieldBody;

	const LINE_ENDING = "\n";

	protected function init(NamedArguments $arguments) {
		$this->fieldName = $this->fieldNameFromName($arguments->name);
		$this->fieldBody = $arguments->body;

	}

	protected function fieldNameFromName($name) {
		$headerName = ucfirst($name);
		// Hypenate camelCase
		$headerName = preg_replace('/([a-z])([A-Z])/', '\1-\2', $headerName);
		return $headerName;
	}

	public function text() {
		return self::$this->fieldName . ': ' . $this->fieldBody . "\n";
	}

}


class Email extends Object {


	protected $to;
	protected $subject;
	protected $message;
	protected $headers = array();

	protected $from = "CORAL Resources";
	protected $replyTo;


	protected function nameIsBasic($name) {
		return preg_match('/^(to)|(subject)|(message)$/', $name);
	}

	protected function getHeaders() {
		$output = '';

		foreach ($this->headers as $header) {
			$output .= $header->text();
		}
		//append from and reply to
		$output .= "From: " . $this->from . "\r\n";
		$output .= "Reply-To: " . $this->replyTo . "\r\n";
		$output .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";

		return $output;
	}

	public function setValueForKey($key, $value) {
		if ($this->nameIsBasic($key)) {
			parent::setValueForKey($key, $value);
		} else {
			$this->headers[$key] = new EmailHeader(new NamedArguments(array('name' => $key, 'body' => $value)));
		}
	}


	public function fullMessage() {
		return $this->getHeaders() . "\n" . $this->to . "\n" . $this->subject . "\n" . $this->message;
	}

	public function send(){
		$config = new Configuration();

		//add on feedback email address if it exists
		if ($config->settings->feedbackEmailAddress){
			$this->replyTo = $config->settings->feedbackEmailAddress;
			if (!$this->to){
				$this->to = $config->settings->feedbackEmailAddress;
			}
		}

		return mail($this->to, $this->subject, $this->message, rtrim($this->getHeaders()));
	}

}

?>