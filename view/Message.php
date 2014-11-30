<?phpnamespace view;
// Hanterar meddelande
class Message {	private $currentMessage;
	public function hasMessage() {		return isset($this->currentMessage);	}
	public function setMessage($message) {		$this->currentMessage = $message;	}
	public function getMessage() {		$output = $this->currentMessage;		unset($this->currentMessage);		return $output;	}}