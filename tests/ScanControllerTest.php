<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ScanControllerTest extends WebTestCase
{
    public function testScanPageIsAccessible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/scan');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Scanner une carte de visite');
    }

    public function testContactAndEventCrud(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create an Event
        $event = new Event();
        $event->setName('Tech Conference 2026');
        $event->setLocation('Paris');
        $em->persist($event);
        $em->flush();

        // Access event list
        $client->request('GET', '/events');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table', 'Tech Conference 2026');

        // Access contact list
        $client->request('GET', '/contacts');
        $this->assertResponseIsSuccessful();
    }
}
