<?php
use PHPUnit\Framework\TestCase;

use Wolfish\GoogleRequest;
use Wolfish\Parameters;
use Wolfish\Config;

class GeneralTest extends TestCase
{

    /**
    *   Tests if response contains basic data
    */
    public function testCseResponse()
    {
        $result = new GoogleRequest('test');
        $result = $result->listCseRequest();
        $resultArray = json_decode($result, true);

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('text', $resultArray);
        $this->assertArrayHasKey('response_type', $resultArray);

        $this->assertThat(
            $resultArray['response_type'],
            $this->logicalOr('in_channel', 'ephemeral')
        );
    }

    /**
    *   Tests if empty result message is returned correctly
    */
    public function testCseEmptyResponse()
    {
        $result = new GoogleRequest('n4isdfb23uyuba84buon');
        $result = $result->listCseRequest();
        $resultArray = json_decode($result, true);

        $this->assertEquals(Config::GOOGLE_NO_RESULT, $resultArray['text']);
    }

    /**
    *   Checks if parameters ensure image search for certain parameters
    */
    public function testImageParameters()
    {
        $imageParams = array(
            '#image',
            '#gif',
            '#mono',
            '#gray'
        );

        foreach($imageParams as $param) {
            $request = new Parameters($param.' test');
            $this->assertTrue($request->isImageSearch());
        }
    }

    /**
    *   Check if CSE result is Image when requesting for it
    *   @depends testImageParameters
    */
    public function testCseImageResponse()
    {
        $result = new GoogleRequest('#image #one test');
        $result = $result->makeCseRequest();
        $this->assertInstanceOf(
            \Google_Service_Customsearch_ResultImage::class, 
            $result->getItems()[0]->getImage()
        );
    }

}