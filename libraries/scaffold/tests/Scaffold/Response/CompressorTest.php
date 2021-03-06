<?php

require_once __DIR__.'/../../_init.php';

/**
 * Test class for Scaffold_Response_Encoder.
 * Generated by PHPUnit on 2010-04-02 at 18:39:27.
 */
class Scaffold_Response_EncoderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scaffold_Response_Encoder
     */
    protected $object;
    
    private $methodTests = array(
        array(
            'ua' => 'Any browser'
            ,'ae' => 'compress, x-gzip'
            ,'exp' => 'gzencode'
            ,'desc' => 'recognize "x-gzip" as gzip'
        )
        ,array(
            'ua' => 'Any browser'
            ,'ae' => 'compress, x-gzip;q=0.5'
            ,'exp' => 'gzencode'
            ,'desc' => 'gzip w/ non-zero q'
        )
        ,array(
            'ua' => 'Any browser'
            ,'ae' => 'compress, x-gzip;q=0'
            ,'exp' => 'gzencode'
            ,'desc' => 'gzip w/ zero q'
        )
        ,array(
            'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)'
            ,'ae' => 'gzip, deflate'
            ,'exp' => false
            ,'desc' => 'IE6 w/o "enhanced security"'
        )
        ,array(
            'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
            ,'ae' => 'gzip, deflate'
            ,'exp' => false
            ,'desc' => 'IE6 w/ "enhanced security"'
        )
        ,array(
            'ua' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.01)'
            ,'ae' => 'gzip, deflate'
            ,'exp' => false
            ,'desc' => 'IE5.5'
        )
        ,array(
            'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 9.25'
            ,'ae' => 'gzip,deflate'
            ,'exp' => false
            ,'desc' => 'Opera identifying as IE6'
        )
        ,array(
            'ua' => null
            ,'ae' => 'gzip,deflate'
            ,'exp' => 'gzencode'
            ,'desc' => 'No user agent set'
        )
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Scaffold_Response_Encoder;
    }

    public function testNoCompression()
    {
    	$encoder = new Scaffold_Response_Encoder(false);
    	$this->assertEquals($encoder->get_encoding_method(),false);
    	$this->assertEquals($encoder->get_compression_level(),false);
    }
    
    public function testCompressionLevelOne()
    {
    	$encoder = new Scaffold_Response_Encoder(1);
    	$this->assertEquals($encoder->get_compression_level(),1);
    }
    
    public function testCompressionNegativeValue()
    {
    	$encoder = new Scaffold_Response_Encoder(-1);
    	$this->assertEquals($encoder->get_encoding_method(),false);
    	$this->assertEquals($encoder->get_compression_level(),false);
    }
    
    public function testCompressionZeroValue()
    {
    	$encoder = new Scaffold_Response_Encoder(0);
    	$this->assertEquals($encoder->get_encoding_method(),false);
    	$this->assertEquals($encoder->get_compression_level(),false);
    }
    
    public function testCompressionTrue()
    {
    	$encoder = new Scaffold_Response_Encoder(true);
    }
    
    public function testEncoding()
    {
    	foreach($this->methodTests as $test)
    	{
    		$_SERVER['HTTP_ACCEPT_ENCODING'] = $test['ae'];
    		$_SERVER['HTTP_USER_AGENT'] = $test['ua'];
    		
    		$encoder = new Scaffold_Response_Encoder(1);
    		$encoding = $encoder->get_encoding_method();
    		
    		$this->assertEquals($encoding,$test['exp']);
    	}
    }
 
    public function testGzipEncode()
    {
    	$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
    	$_SERVER['HTTP_USER_AGENT'] = false;
    	
    	$encoder = new Scaffold_Response_Encoder(1);
 
        $content = 'foo';
        $content = $encoder->compress($content);
        $this->assertEquals($content,gzencode('foo',1));
    }
    
    public function testGzipDeflate()
	{
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'deflate';
		$_SERVER['HTTP_USER_AGENT'] = false;
		
		$encoder = new Scaffold_Response_Encoder(1);
		
		$content = 'foo';
		$content = $encoder->compress($content);
		$this->assertEquals($content,gzdeflate('foo',1));
	}
}
