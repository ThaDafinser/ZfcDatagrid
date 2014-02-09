<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;
use Zend\Stdlib\ErrorHandler;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Standard
 */
class StandardTest extends PHPUnit_Framework_TestCase
{

    public function testConstructDefaultBoth()
    {
        ErrorHandler::start(E_USER_DEPRECATED);
        $col = new Column\Standard('id', 'user');
        $err = ErrorHandler::stop();
        
        $this->assertInstanceOf('ErrorException', $err);
        
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);
    }
}
