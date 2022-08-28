<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\SubCategories;
use App\Entity\TopicResponses;
use App\Entity\Topics;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        // users

        $users = [];
        for ($k=0; $k < 15; $k++) { 
            $user = new Users();
            $user->setPseudo($this->faker->userName())
                ->setPlainPassword('Azerty123')
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
            ;

            $users[] = $user;
            $manager->persist($user);
        }

        // categories

        $categories = [];
        for ($i=0; $i < 5; $i++) { 
            $category = new Categories();
            $category->setTitle($this->faker->word())
                ->setDescription($this->faker->text(200))
                ;
            
            $categories[] = $category;
            $manager->persist($category);
        }

        // sub categories

        $subCategories = [];
        foreach ($categories as $category) {
            for ($j=0; $j < mt_rand(1, 6); $j++) { 
                $sub = new SubCategories();
                $sub->setTitle($this->faker->word())
                    ->setDescription($this->faker->text(200))
                    ->setCategory($category)
                    ;
                
                $subCategories[] = $sub;
                $manager->persist($sub);
            }
        }

        // topics

        $topics = [];
        foreach ($subCategories as $subCat) {   
            for ($l=0; $l < mt_rand(1, 70); $l++) { 
                $topic = new Topics();
                $topic->setTitle($this->faker->sentence(mt_rand(1, 7)))
                    ->setSubTitle(mt_rand(0, 1) === 1 ? $this->faker->sentence(mt_rand(1, 7)) : null)
                    ->setMainContent($this->faker->text(350))
                    ->setIsActive(mt_rand(0, 1))
                    ->setIsPinned(mt_rand(0, 5) === 3 ? 1 : 0)
                    ->setIsLock(mt_rand(0, 5) === 3 ? 1 : 0)
                    ->setSubCategory($subCat)
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                ;

                $topics[] = $topic;
                $manager->persist($topic);
            }
        }

        // topics response 
        
        foreach ($topics as $topic) {
            for ($n=0; $n < mt_rand(1, 75); $n++) { 
                $resp = new TopicResponses();
                $resp->setContent($this->faker->text(mt_rand(25, 400)))
                ->setTopic($topic)
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ;

                $manager->persist($resp);
            }
        }

        $manager->flush();
    }
}
