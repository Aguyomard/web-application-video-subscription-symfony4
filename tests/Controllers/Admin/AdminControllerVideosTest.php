<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\RoleAdmin;

use App\Entity\Video;

class AdminControllerVideosTest extends WebTestCase
{
    use RoleAdmin;

    public function testDeleteVideo()
    {
        $this->client->request('GET', '/admin/su/delete-video/11/289729765');
        $video = $this->entityManager->getRepository(Video::class)->find(11);
        $this->assertNull($video);
    }

}
