<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Game;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword(password_hash('adminpass', PASSWORD_BCRYPT));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstName("Admin");
        $admin->setLastName("Le Boss");
        $admin->setIsBlocked(false);
        $manager->persist($admin);

        // Utilisateur normal
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword(password_hash('userpass', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName("Jean");
        $user->setLastName("Petit");
        $user->setIsBlocked(false);
        $manager->persist($user);


        // Catégorie
        $category = new Category();
        $category->setLabel("Categorie");
        $manager->persist($category);


        // Jeu
        $game = new Game();
        $game->setName("Game");
        $game->setDescription("Description");
        $game->setCategory($category);
        $game->setMinimumNumberOfPlayers(4);
        $game->setMaximumNumberOfPlayers(8);
        $game->setAverageGameDuration(45);
        $manager->persist($game);


        // Address
        $address = new Address();
        $address->setStreet("Jardin Lecoq");
        $address->setPostcode("63000");
        $address->setCity("Clermont-Ferrand");
        $address->setLatitude(45.77230088622623);
        $address->setLongitude(3.088408916741754);
        $manager->persist($address);


        // Event
        $event = new Event();
        $event->setOrganizer($admin);
        $event->setTitle("Evènement");
        $event->setDescription("Description");
        $event->setDuration(60);
        $event->setEventAt(new DateTime());
        $event->setAddress($address);
        $event->addParticipant($user);
        $event->setIsFree(true);
        $manager->persist($event);

        $manager->flush();
    }
}
