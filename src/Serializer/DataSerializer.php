<?php

namespace App\Serializer;

use Symfony\Component\Serializer\SerializerInterface;


class DataSerializer
{

    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function deseialize(array $data, mixed $object): object
    {
        return $this->serializer->deserialize(json_encode($data), $object, "json");

    }


    public function serialize(object $object, array $context = []): string
    {
        return $this->serializer->serialize($object, "json", $context);
    }
}
