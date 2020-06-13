<?php

namespace App\Tests;

trait RoleUser {

    public function setUp()
    {
        parent::setUp();

        self::bootKernel();
        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();
        // gets the special container that allows fetching private services
        $container = self::$container;
        $cache = self::$container->get('App\Utils\Interfaces\CacheInterface');
        $this->cache = $cache->cache;
        $this->cache->clear();


        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'jd@symf4.loc',
            'PHP_AUTH_PW' => 'passw',
        ]);
        // $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        // $this->entityManager->beginTransaction();
        // $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cache->clear();
        // $this->entityManager->rollback();    
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks   
    }
}
