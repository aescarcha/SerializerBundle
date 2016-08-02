# Aescarcha SerializerBundle
## Introduction

This bundle is made to make serialization of Entities easier on Symfony. It handles [symfony/serializer](https://github.com/symfony/serializer) and provides a fallback to read from database if needed.

## Install

    composer require aescarcha/serializer

This bundle depends on doctrine entity manager, you need to add this

##### services.yml

    services:
        aescarcha.serializer:
            class: Aescarcha\SerializerBundle\Controller\SerializeController
            arguments: [ @doctrine.orm.entity_manager ]


##### AppKernel.php

        $bundles = array(
            new Aescarcha\SerializerBundle\AescarchaSerializerBundle(),
        );

## Tests
Tests are provided on the repo, but they're not working because the test requires some Entities and Repositories to work, making them work in a clean symfony install is also a TODO
