<?php
// Call ListObjectTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ListObjectTest::main');
}

require_once('PHPUnit/Framework.php');

require_once('template_engine/classes/ListObject.php');

require_once('set_path.php');


class ListObjectSample extends ListObject
{
	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param bool $p_hasNextElements
	 * @return array
	 */
	protected function CreateList($p_start, $p_limit, &$p_hasNextElements, $p_parameters)
	{
		$objects = array('element 1', 'element 2', 'element 3', 'element 4',
						 'element 5', 'element 6', 'element 7', 'element 8');
		if ($p_start >= count($objects)) {
			$p_hasNextElements = false;
			return array();
		}

		if (!is_numeric($p_start)) {
			$p_start = 0;
		}
		if ($p_start < 0) {
			$p_start = 0;
		}

		if ($p_limit < 0) {
			$p_limit = 0;
		}
		if (!is_numeric($p_limit)) {
			$p_limit = 0;
		}

		$lastElement = $p_start + $p_limit;
		$p_hasNextElements = $lastElement < count($objects) && $lastElement != 0;
		if ($p_limit > 0) {
			return array_slice($objects, $p_start, $p_limit);
		}
		return array_slice($objects, $p_start);
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints($p_constraints)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param string $p_order
	 * @return array
	 */
	protected function ProcessOrder($p_order)
	{
		return array();
	}

	/**
	 * Processes the input parameters passed in an array; drops the invalid
	 * parameters and parameters with invalid values. Returns an array of
	 * valid parameters.
	 *
	 * @param array $p_parameters
	 * @return array
	 */
	protected function ProcessParameters($p_parameters)
	{
		return $p_parameters;
	}
}

/**
 * Test class for ListObject.
 * Generated by PHPUnit on 2007-07-18 at 18:12:15.
 */
class ListObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once('PHPUnit/TextUI/TestRunner.php');

        $suite  = new PHPUnit_Framework_TestSuite('ListObjectTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testDefaultName()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(sha1(time()), $sampleListObject->defaultName());
    }

    public function testGetDefaultIterator()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertTrue(is_a($sampleListObject->defaultIterator(), 'ArrayIterator'));
    }

    public function testGetCurrent()
    {
    	$sampleListObject = new ListObjectSample();
    	$iterator = $sampleListObject->defaultIterator();

    	$this->assertEquals('element 1', $sampleListObject->getCurrent());

    	$iterator->next();
    	$this->assertEquals('element 2', $sampleListObject->getCurrent());

    	$iterator->next();
    	$this->assertEquals('element 3', $sampleListObject->getCurrent());
    }

    public function testGetIndex()
    {
    	$sampleListObject = new ListObjectSample();
    	$iterator = $sampleListObject->defaultIterator();

    	$this->assertEquals(1, $sampleListObject->getIndex());

    	$iterator->next();
    	$this->assertEquals(2, $sampleListObject->getIndex());

    	$iterator->next();
    	$this->assertEquals(3, $sampleListObject->getIndex());
    }

    public function testGetIterator()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertTrue(is_a($sampleListObject->getIterator(), 'ArrayIterator'));
    }

    public function testGetName()
    {
    	$sampleListObject = new ListObjectSample(0, array('name'=>'test name'));
    	$this->assertEquals('test name', $sampleListObject->getName());
    }

    public function testGetLength()
    {
    	$sampleListObject = new ListObjectSample(-1);
    	$this->assertEquals(0, $sampleListObject->getLength());

        $sampleListObject = new ListObjectSample();
    	$this->assertEquals(8, $sampleListObject->getLength());

    	$sampleListObject = new ListObjectSample(3);
    	$this->assertEquals(5, $sampleListObject->getLength());
    }

    public function testIsBlank()
    {
    	$sampleListObject = new ListObjectSample(-1);
    	$this->assertTrue($sampleListObject->isBlank());

    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->isBlank());
    }

    public function testIsEmpty()
    {
    	$sampleListObject = new ListObjectSample(-1);
    	$this->assertTrue($sampleListObject->isEmpty());

    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->isEmpty());

    	$sampleListObject = new ListObjectSample(3, array('length'=>2));
    	$this->assertFalse($sampleListObject->isEmpty());
    }

    public function testIsLimited()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->isLimited());

    	$sampleListObject = new ListObjectSample(0, array('length'=>10));
    	$this->assertTrue($sampleListObject->isLimited());

    	$sampleListObject = new ListObjectSample(5, array('length'=>5));
    	$this->assertTrue($sampleListObject->isLimited());
    }

    public function testGetLimit()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->getLimit());

    	$sampleListObject = new ListObjectSample(0, array('length'=>10));
    	$this->assertEquals(10, $sampleListObject->getLimit());

    	$sampleListObject = new ListObjectSample(0, array('length'=>'invalid value'));
    	$this->assertEquals(0, $sampleListObject->getLimit());
    }

    public function testGetStart()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->getStart());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertEquals(10, $sampleListObject->getStart());

    	$sampleListObject = new ListObjectSample('invalid value');
    	$this->assertEquals(0, $sampleListObject->getStart());
    }

    public function testGetEnd()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(8, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(5);
    	$this->assertEquals(8, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertEquals(10, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(4, array('length'=>2));
    	$this->assertEquals(6, $sampleListObject->getEnd());
    }

    public function testHasNextElements()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(5, array('length'=>5));
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(4, array('length'=>2));
    	$this->assertTrue($sampleListObject->hasNextElements());
    }

    public function testGetColumn()
    {
    	$sampleListObject = new ListObjectSample(0, array('columns'=>3));

    	$iterator = $sampleListObject->getIterator();
    	$this->assertEquals(1, $sampleListObject->getColumn($iterator));

    	$iterator->next();
    	$this->assertEquals(2, $sampleListObject->getColumn($iterator));

    	$iterator->next();
    	$this->assertEquals(3, $sampleListObject->getColumn($iterator));

    	$iterator->next();
    	$this->assertEquals(1, $sampleListObject->getColumn($iterator));
    }

    public function testGetColumns()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->getColumns());

    	$sampleListObject = new ListObjectSample(0, array('columns'=>3));
    	$this->assertEquals(3, $sampleListObject->getColumns());
    }

    public function testGetConstraintsString()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals('', $sampleListObject->getConstraintsString());

    	$sampleListObject = new ListObjectSample(0, array('constraints'=>'test string'));
    	$this->assertEquals('test string', $sampleListObject->getConstraintsString());
    }

    public function testGetOrderString()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals('', $sampleListObject->getOrderString());

    	$sampleListObject = new ListObjectSample(0, array('order'=>'test string'));
    	$this->assertEquals('test string', $sampleListObject->getOrderString());
    }

    public function testParseConstraintsString()
    {
        $constraintsString = "word1 word\ 2 word\"3 word\\\\4";
        $expectedResult = array('word1', 'word 2', 'word"3', 'word\\4');
        $this->assertEquals($expectedResult, ListObject::ParseConstraintsString($constraintsString));
    }
}

// Call ListObjectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'ListObjectTest::main') {
    ListObjectTest::main();
}
?>