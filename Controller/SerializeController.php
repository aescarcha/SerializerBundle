<?php

namespace Aescarcha\SerializerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SerializeController extends Controller
{
    protected $serializer, $normalizers, $em;

    public function __construct( EntityManagerInterface $em )
    {
        $this->em = $em;
        $this->instantiateSerializer(array('children', 'postAssets', 'lazyPropertiesDefaults'));
    }

    protected function instantiateSerializer( array $ignoredAttributes )
    {
        $encoder = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes($ignoredAttributes);
        $this->normalizers = array($normalizer);
        $this->serializer = new Serializer($this->normalizers, $encoder);
    }

    public function setIgnoredAttributes( array $attributes )
    {
        $this->instantiateSerializer($attributes);
    }

    /**
     * Returns the serialize object and its class as an array
     * @param  [type] $object [description]
     * @return [type]         [description]
     */
    public function serialize( $object )
    {
        if(is_array($object)){
            $data = [];
            foreach ($object as $index => $value) {
                $data[] = $this->serialize($value);
            }
        } else {
            $data = [
                'object' => $this->serializer->serialize($object, 'json'),
                'class' => get_class($object)
            ];
        }

        return $data;
    }

    /**
     * Returns a json encoded version of serialize, so its easier to save in a string
     * @param  [type] $object [description]
     * @return [type]         [description]
     */
    public function serializeJson( $object )
    {
        return json_encode($this->serialize( $object ));
    }

    public function deserialize( $data )
    {
        $entity = false;
        
        if(is_array($data)){
            $result = [];
            foreach ($data as $index => $value) {
                $result[] = $this->deserialize($value);
            }
            return $result;
        } 

        if( !is_object($data) || !$data->object || $data->object == 'null' || !$data->class){
            return $data;
        }

        try {
            $entity = $this->serializer->deserialize( $data->object, $data->class, 'json' );
        } catch (\Exception $e) {
            //could not deserialize, read it
            $decoded = json_decode($data->object);
            $entity =  $this->em->getRepository($data->class)->find($decoded->id);
        }
        return $entity;
    }

    public function deserializeJson( $data )
    {
        return $this->deserialize( json_decode($data) );
    }
}
