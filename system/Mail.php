<?php
/**
 * Designed to send email to a user(s)
 */

namespace afm
{
	include_once('Toolbox.php');
	
	class Mail
	{
		private $m_message;
		private $m_recipients; // array of recipients
		private $m_sender;
		private $m_subject;
		private $m_html; // default is html mail
		
		function __construct()
		{
			$this->m_html = true;
			$this->m_message = " ";
			$this->m_sender = null;
			$this->m_subject = " ";
			$this->m_recipients = array();
		}
		
		function addRecipient($to)
		{
			$filteredEmail = cleanseEmail($to);
			if ($filteredEmail != FALSE)
			{
				$this->m_recipients[] = $filteredEmail;
			}
		}
		
		// needed?
		function removeRecipient($to)
		{
			$filteredEmail = cleanseEmail($to);
			if ($filteredEmail != FALSE)
			{
				if (array_key_exists($filteredEmail, $this->m_recipients) == true)
				{
					unset($this->m_recipients[$filteredEmail]);
				}
			}
		}
		
		function setSender($from)
		{
			$filteredEmail = cleanseEmail($from);
			if ($filteredEmail != FALSE)
			{
				$this->m_sender = $filteredEmail;
			}
			else
			{
				$this->m_sender = null;
			}
		}
		
		function setMessage($message)
		{
			$this->m_message = $message;
		}
		
		function isHtml($htmlFlag)
		{
			$this->m_html = $htmlFlag;
		}
		
		function setSubject($subject)
		{
			$this->m_subject = $subject;
		}
		
		function sendMessage()
		{
			$success = false;
			
			if (count($this->m_recipients) > 0)
			{
				if ($this->m_sender != null)
				{
					$toList = null;
					
					// Build up our to list
					foreach ($this->m_recipients as $name => $value)
					{
						if ($toList != null)
						{
							$toList .= ", " . $value;
						}
						else
						{
							$toList = $value;
						} 
					}
					
					$headers = "From: " . $this->m_sender . PHP_EOL;
					$headers .= "Reply-To: " . $this->m_sender . PHP_EOL;
					$headers .= "X-Mailer: PHP/" . phpversion() . PHP_EOL;
					
					// Build up the headers
					if ($this->m_html == true)
					{
						$headers .= "MIME-Version 1.0" . PHP_EOL;
						$headers .= "Content-type: text/html; charset=iso-8859-1" . PHP_EOL;
					}
					
					$success = mail($toList, $this->m_subject, $this->m_message, $headers);
				}
				else
				{
					error_log("No sender specified.");
				}
			}
			else
			{
				error_log("No recipients specified.");
			}
			return $success;
		}
	}
}
?>