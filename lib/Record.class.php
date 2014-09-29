<?php
		namespace lib;
    abstract class Record implements \ArrayAccess
    {
        protected $id;
        
        public function __construct(array $values = array())
        {
            if (!empty($values))
            {
                $this->hydrate($values);
            }
        }
        
        public function get_class_vars()
        {
        	return get_object_vars($this);
        }
        
        public function isNew()
        {
            return empty($this->id);
        }

        public function id()
        {
            return $this->id;
        }
        
        public function setId($id)
        {
            $this->id = (int) $id;
        }
        
        public function hydrate(array $donnees)
        {
            foreach ($donnees as $attribut => $valeur)
            {
                $methode = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));
                if (is_callable(array($this, $methode)))
                {
                    $this->$methode($valeur);
                }
            }
        }
        
        public function offsetGet($var)
        {
            if (isset($this->$var) && is_callable(array($this, $var)))
            {
                return $this->$var();
            }
        }
        
        public function offsetSet($var, $value)
        {
            $method = 'set'.ucfirst($var);
            if (isset($this->$var) && is_callable(array($this, $method)))
            {
                $this->$method($value);
            }
        }
        
        public function offsetExists($var)
        {
            return isset($this->$var) && is_callable(array($this, $var));
        }
        
        public function offsetUnset($var)
        {
            throw new Exception('Impossible de supprimer une quelconque valeur');
        }
    }
