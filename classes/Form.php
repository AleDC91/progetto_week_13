<?php

namespace ADC {

    class Form
    {
        protected string $buttonId;
        protected string $buttonName;
        protected string $method;
        protected string $action;
        protected array $textFields;
        protected array $emailFields;
        protected array $passwordFields;
        protected array $checkboxFields;
        protected array $radioFields;
        protected array $selectFields;
        protected array $allNames;
        protected bool $generaFile;


        function __construct(
            string $action,
            string $method = "POST",
            string $buttonName = "Submit",
            string $buttonId = "",
            array $textFields = ["username"],
            array $emailFields = ["email"],
            array $passwordFields = ["password"],
            array $checkboxFields = [],
            array $radioFields = [],
            array $selectFields = [],
            bool $generaFile = false
        ) {

            $this->method = $method;
            $this->action = $action;
            $this->buttonName = $buttonName;
            $this->buttonId = $buttonId;
            $this->textFields = $textFields;
            $this->emailFields = $emailFields;
            $this->passwordFields = $passwordFields;
            $this->checkboxFields = $checkboxFields;
            $this->radioFields = $radioFields;
            $this->selectFields = $selectFields;
            $this->generaFile = $generaFile;

            $this->allNames = [...$this->textFields, ...$this->emailFields, ...$this->passwordFields, ...$this->checkboxFields, ...$this->radioFields, ...$this->selectFields];
        }

        private function isValidFieldName($newName)
        {
            if (!in_array($newName, $this->allNames)) {
                return true;
            } else {
                return false;
            }
        }

        public function setTextFields(array $text)
        {
            $this->textFields = $text;
        }

        public function setEmailFields(array $email)
        {
            $this->emailFields = $email;
        }

        public function setPasswordFields(array $password)
        {
            $this->passwordFields = $password;
        }

        public function setCheckboxFields(array $checkbox)
        {
            $this->checkboxFields = $checkbox;
        }

        public function setRadioFields(array $radio)
        {
            $this->radioFields = $radio;
        }

        public function setSelectFields(array $select)
        {
            $this->selectFields = $select;
        }

        public function setButtonName(string $buttonName){
            $this->buttonName = $buttonName;
        }

        public function setButtonId(string $buttonId){
            $this->buttonId = $buttonId;

        }

        public function generaFile(){
            $this->generaFile = true;
        }

        public function addText(string $text)
        {
            if ($this->isValidFieldName($text)) {
                $this->textFields[] = $text;
            }
        }

        public function addEmail(string $email)
        {
            if ($this->isValidFieldName($email)) {
                $this->emailFields[] = $email;
            }
        }

        public function addPassword(string $password)
        {
            if ($this->isValidFieldName($password)) {
                $this->passwordFields[] = $password;
            }
        }

        public function addCheckbox(string $checkbox)
        {
            if ($this->isValidFieldName($checkbox)) {
                $this->checkboxFields[] = $checkbox;
            }
        }

        public function addRadio(array $radio)
        {
            foreach ($radio as $key => $value) {
                if ($this->isValidFieldName($key)) {
                    $this->radioFields[$key] = $value;
                }
            }
        }


        public function addSelect(array $select)
        {
            foreach ($select as $key => $value) {
                if ($this->isValidFieldName($key)) {
                    $this->selectFields[$key] = $value;
                }
            }
        }



        public function drawForm()
        {
            $textHTML = '';
            $emailHTML = '';
            $passwordHTML = '';
            $checkboxHTML = '';
            $radioHTML = '';
            $selectHTML = '';

            foreach ($this->textFields as $textField) {
                $textHTML .= "<div class='mb-3'>
                <label for='$textField' class='form-label'>" . ucwords($textField) . "</label>
                <input type='text' id='$textField' name='$textField' class='form-control'>
                </div> ";
            }

            foreach ($this->emailFields as $emailField) {
                $emailHTML .= "<div class='mb-3'>
                <label for='$emailField' class='form-label'>" . ucwords($emailField) . "</label>
                <input type='email' id='$emailField' name='$emailField' class='form-control'>
                </div> ";
            }

            foreach ($this->passwordFields as $passwordField) {
                $passwordHTML .= "<div class='mb-3'>
                <label for='$passwordField' class='form-label'>" . ucwords($passwordField) . "</label>
                <input type='password' id='$passwordField' name='$passwordField' class='form-control'>
                </div> ";
            }


            foreach ($this->checkboxFields as $checkboxField) {
                $checkboxHTML .= "
                <div class='form-check mb-3'>
                    <input class='form-check-input' type='checkbox' name='$checkboxField' id='$checkboxField'>
                    <label class='form-check-label' for='$checkboxField'>" . ucwords($checkboxField) . "</label>
                </div>
                ";
            }


            foreach ($this->radioFields as $radioID => $radioOptions) {
                $radioHTML .= "<div class='mb-3'><h4>" . ucwords($radioID) . "</h4>";
                foreach ($radioOptions as $radioOption) {
                    $radioHTML .=
                        "<div class='form-check'>
                <input class='form-check-input' type='radio' name='$radioID' id='$radioID' value='$radioOption'>
                <label class='form-check-label' for='$radioID'>
                 " . ucwords($radioOption) . "
                </label>
              </div>";
                }
                $radioHTML .=  "</div>";
            }

            foreach ($this->selectFields as $selectID => $selectOptions) {
                $selectHTML .= "<div class='mb-3'><h4>" . ucwords($selectID) . "</h4>" . "<select class='form-select' aria-label='$selectID' name='$selectID'>";
                foreach ($selectOptions as $selectOption) {
                    $selectHTML .= "<option value='$selectOption'>"  . ucwords($selectOption) . "</option>";
                }
                $selectHTML .=  "</select></div>";
            }


            $form = "<form class='mt-5' action='$this->action' method='$this->method'> 
            $textHTML $emailHTML $passwordHTML $radioHTML $checkboxHTML $selectHTML
            <button type='submit' name='$this->buttonId' id='$this->buttonId' class='btn btn-primary mb-5'>$this->buttonName</button>
            </form>";

            echo $form;

            if ($this->generaFile) {
                $dir = 'HTMLform/';
                $file = uniqid() . ".txt";
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $resource = fopen($dir . $file, 'w');
                if ($resource !== false) {
                    fwrite($resource, $form);
                    fclose($resource);
                    
                } 
            }
        }
    }
}