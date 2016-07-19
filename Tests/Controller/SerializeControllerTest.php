<?php

namespace Aescarcha\SerializerBundle\Tests\Controller;

use Aescarcha\SerializerBundle\Tests\BaseKernel;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SerializeControllerTest extends BaseKernel
{
    public function testGetService()
    {
        $service = static::$kernel->getContainer()->get('aescarcha.serializer');
        $this->assertInstanceOf( '\Aescarcha\SerializerBundle\Controller\SerializeController', $service );
    }

    public function testSerialize()
    {
        $service = static::$kernel->getContainer()->get('aescarcha.serializer');

        //now check that user 1 has an item in its queue
        $object = $this->Posts->find(1);
        $result = $service->serializeJson($object);
        $this->assertNotFalse( $result );

        $decoded = json_decode( $result, true );
        $this->assertEquals( 'Aescarcha\ChallengeBundle\Entity\Post', $decoded['class'] );
        $this->assertArrayHasKey( 'object', $decoded );
        return $result;
    }

    public function testDeserialize()
    {
        $serialized = $this->testSerialize();
        $service = static::$kernel->getContainer()->get('aescarcha.serializer');

        $entity = $service->deserializeJson($serialized);

        $object = $this->Posts->find(1);

        $this->assertInstanceOf( 'Aescarcha\ChallengeBundle\Entity\Post', $entity );
        $this->assertEquals( 1, $entity->getId() );
        $this->assertEquals( $object->getContent(), $entity->getContent() );
        $this->assertEquals( $object->getScore(), $entity->getScore() );
        $this->assertEquals( $object->getTitle(), $entity->getTitle() );
    }

    public function testSerializeDeserializeArray()
    {
        $service = static::$kernel->getContainer()->get('aescarcha.serializer');

        $collection = $this->Posts->getLast(10);
        $result = $service->serializeJson($collection);
        $this->assertNotFalse( $result );

        $result = $service->deserializeJson($result);
        $this->assertCount( 10, $result );
        $entity = array_pop($result);
        $this->assertInstanceOf( 'Aescarcha\ChallengeBundle\Entity\Post', $entity );
        $this->assertEquals( 2, $entity->getId() );
    }

}