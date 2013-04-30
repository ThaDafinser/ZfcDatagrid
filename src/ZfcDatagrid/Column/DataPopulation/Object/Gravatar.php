<?php
namespace ZfcDatagrid\Column\DataPopulation\Object;

use ZfcDatagrid\Column\DataPopulation\ObjectAwareInterface;

class Gravatar implements ObjectAwareInterface
{

    protected $email;

    public function setParameter ($name, $value)
    {
        switch ($name) {
            case 'email':
                $this->email = (string) $value;
                break;
            
            default:
                throw new \Exception('Not allowed parameter: ' . $name);
                break;
        }
    }

    public function setParameterFromColumn ($name, $value)
    {
        $this->setParameter($name, $value);
    }

    public function toString ()
    {
        return 'http://www.gravatar.com/avatar/' . md5($this->email);
    }
}