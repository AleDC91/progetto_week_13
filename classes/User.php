<?php 

class User {
    public function __construct(
        private string $firstname,
        private string $lastname,
        private string $email,
        private string $password,
        private ?int $id = null
    ){}

    public function __get($name)
    {
        // non posso scrivere cosi perchè altrimenti mi salva 
        // la stringa di return come password nel DB!!!!!!!
        // if($name === "password"){
        //     return "La password è segreta!";
        // }
        
        if(!property_exists($this, $name)){
            return "La proprietà $name non esiste!";
        }

        return $this->$name;
    }
}
?>