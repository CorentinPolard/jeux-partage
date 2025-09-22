<?php

namespace App\Tests\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityTest extends WebTestCase
{
    public function testUserGetFullName(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'firstName' => 'Jean',
            'lastName' => 'Petit',
        ]);

        $this->assertEquals('Jean Petit', $user->getFullName());
    }
}
