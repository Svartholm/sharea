<?php
namespace lib\entities;
class Profile extends \lib\Record
{
    protected $lastname,
        $firstname,
        $pseudo,
        $email,
        $avatar;

    /* Getters */
    public function lastname()
    {
        return $this->lastname;
    }

    public function firstname()
    {
        return $this->firstname;
    }

    public function email()
    {
        return $this->email;
    }

    public function pseudo()
    {
        return $this->pseudo;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function avatar()
    {
        return $this->avatar;
    }

    /* Setters */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /* Methods */
    public function isValid()
    {
        $error = new \lib\Error();

        if (!\lib\Regex::isName($this->firstname))
            $error->setMessage("Prénom invalide");

        if (!\lib\Regex::isName($this->lastname))
            $error->setMessage("Nom invalide");

        if (!\lib\Regex::isEmail($this->email))
            $error->setMessage("Email invalide");

        if (!\lib\Regex::isPseudo($this->pseudo))
            $error->setMessage("Pseudonyme invalide");

        $error->setWarnLevel(\lib\Error::wl_LOW);

        $m = $error->message();
        if (!empty($m))
            return $error;
        else
            return true;
    }
}

?>
