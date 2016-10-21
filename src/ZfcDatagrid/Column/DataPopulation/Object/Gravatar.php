<?php

namespace ZfcDatagrid\Column\DataPopulation\Object;

use ZfcDatagrid\Column\DataPopulation\ObjectAwareInterface;

class Gravatar implements ObjectAwareInterface
{
    /** @var string */
    protected $email;

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws \Exception
     */
    private function setParameter($name, $value)
    {
        switch ($name) {

            case 'email':
                $this->email = (string) $value;
                break;

            default:
                throw new \InvalidArgumentException('Not allowed parameter: '.$name);
                break;
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ZfcDatagrid\Column\DataPopulation\ObjectAwareInterface::setParameterFromColumn()
     */
    public function setParameterFromColumn($name, $value)
    {
        $this->setParameter($name, $value);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ZfcDatagrid\Column\DataPopulation\ObjectAwareInterface::toString()
     */
    public function toString()
    {
        $hash = '';
        if ($this->email != '') {
            $hash = md5($this->email);
        }

        return 'http://www.gravatar.com/avatar/'.$hash;
    }
}
