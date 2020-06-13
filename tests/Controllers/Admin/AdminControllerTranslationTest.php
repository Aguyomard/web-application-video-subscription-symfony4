<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTranslationTest extends WebTestCase
{
    use RoleUser;

    public function testTranslations()
    {

        $this->client->request('GET', '/fr/admin/');

        $this->assertContains( 'Mon profil', $this->client->getResponse()->getContent() );
        $this->assertContains( 'liste-de-videos', $this->client->getResponse()->getContent() );
    }
}
