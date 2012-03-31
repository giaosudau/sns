<?php
class Register {
	private $fields = array('user' => 'username', 'password' => 'passsword', 'password_confirm' => 'password_confirm', 'email' => 'email', 'email_confirm' => 'email_confirm');
	private $registionErrors = array();
	private $registionErrorLabels = array();
	private $sumbittedValues = array();
	private $santinizedValues = array();
	private $activeValues = 1;
	private function checkRegistration() {
		// if all clear
		$allClear = TRUE;
		// check any blank field
		foreach ($this->fields as $field => $name) {
			if (!isset($_POST['register_' . $field]) || $_POST['register_' . $field] == '') {
				$allClear = FALSE;
				$this -> registationError[] = 'You must enter a' . $name;
				$this -> registrationErrorLables['regsister_' . $field . '_label'] = 'error';
			}
		}
		// check for password match confirm password
		if ($_POST['register_password'] != $_POST['register_password_confirm']) {
			$allClear = FALSE;
			$this -> registionErrors = 'You must confirm your password correctly';
			$this -> registionErrorLabels['register_password_lable'] = 'error';
			$this -> registionErrorLabels['register_password_confirm_label'] = 'error';
		}
		// check password length
		if (strlen($_POST['register_password']) < 6) {
			$allClear = FALSE;
			$this -> registionErrors = "Your must enter password minium 6 character";
			$this -> registionErrorLabels['register_password_lable'] = 'error';
			$this -> registionErrorLabels['register_password_confirm_lable'] = 'error';

		}
		// check email correct
		// Decodes any %## encoding in the given string $str.
		// The string could be encoded with the function urlencode().
		if (strpos(urldecode($_POST['register_email']), "\r") === TRUE || strpos(urldecode($_POST['register_email']), "\n") === TRUE) {
			$allClear = FALSE;
			$this -> registionErrors[] = 'You email is not valid';
			$this -> registionErrorLabels['register_email_lable'] = 'error';

		}
		//check email valid
		if (!preg_match("^[_0-9a-z-] + [(\_0-9a-z] + " + "*@" + "[a-z0-9_] + " + "[a-z0-9-]+)*(\.[a-z]{2,4})^", $_POST['regsiter_email'])) {
			$allClear = FALSE;
			$this -> registionErrors[] = 'You email is not valid';
			$this -> registionErrorLabels['register_email_lable'] = 'error';

		}
		// accept term agrement
		if (isset($_POST['register_terms']) && $_POST['register_terms'] != 1) {
			$allClear = FALSE;
			$this -> registionErrors[] = 'You not agree terms';
			$this -> registionErrorLabels['register_terms_lable'] = 'error';

		}
		// duplicate check email
		$u = $this -> registry -> getObject('db') -> sanitizedData($_POST['register_username']);
		$e = $this -> registry -> getObject('db') -> sanitizeData($_POST['register_email']);
		$sql = "SELECT * FROM users WHERE email ='{$e}'";
		$this -> registry -> getObject('db') -> executeQuery($sql);
		if ($this -> registry -> getObject('db') -> numRows() >= 1) {
			$allClear = FALSE;
			$this -> registionErrors = "Your email already in use";
			$this -> registionErrorLabels['register_email_label'] = "error";

		}

		// capcha
		if ($this -> registry -> getObject('db') -> getSetting('capcha.enabled') == 1) {
			// check capcha
		}
		if ($this -> registrationExtention -> checkRegistationSubmission() == FALSE) {
			$allClear = FALSE;
		}
		if ($allClear == TRUE) {
			$this -> santinizedValues['username'] = $u;
			$this -> santinizedValues['email'] = $e;
			$this -> santinizedValues['password_hash'] = md5($_POST['register_password']);
			$this -> santinizedValues['active'] = $this -> activeValues;
			$this -> santinizedValues['admin'] = 0;
			$this -> santinizedValues['banned'] = 0;
			$this -> santinizedValues['register_password'] = $_POST['register_password'];
			$this -> santinizedValues['register_username'] = $_POST['register_username'];
			return true;
		} else {
			$this -> sumbittedValues['registre_user'] = $_POST['register_username'];
			$this -> sumbittedValues['register_password'] = $_POST['register_password'];
			$this -> submittedValues['register_password_confirm'] = $_POST['register_password_confirm'];
			$this -> sumbittedValues['register_capcha'] = (isset($_POST['register_capcha']) ? $_POST['register_capcha'] : '');
			return FALSE;

		}
		

	}

}
?>