<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\Actor;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Xylis\FakerCinema\Provider\Movie as MovieProvider;
use Xylis\FakerCinema\Provider\Person as PersonProvider;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new MovieProvider($faker));
        $faker->addProvider(new PersonProvider($faker));

        // 1. Création de catégories
        $categories = [];
        $categoryTitles = ['Action', 'Comédie', 'Drame', 'Horreur', 'Science-fiction', 'Romance'];
        foreach ($categoryTitles as $title) {
            $category = new Category();
            $category->setTitle($title)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.8)->dateTimeBetween('-1 years', 'now'));
            $manager->persist($category);
            $categories[] = $category;
        }

        // 2. Création de plusieurs acteurs
        $actors = [];
        $numberOfActors = 50;
        for ($i = 0; $i < $numberOfActors; $i++) {
            $actor = new Actor();
            $actor->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setDob($dob = $faker->dateTimeBetween('-70 years', '-18 years'))
                ->setAwards($faker->numberBetween(0, 10))
                ->setBio($faker->paragraph)
                ->setNationality($faker->country)
                ->setGender($faker->randomElement(['male', 'female']))
                ->setMedia($faker->imageUrl(640, 480, 'people', true))
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.8)->dateTimeBetween($dob, 'now'))
                ->setDeathDate($faker->optional(0.15)->dateTimeBetween($dob, 'now'));

            $manager->persist($actor);
            $actors[] = $actor;
        }

        // 3. Création de plusieurs films
        $numberOfMovies = 20;
        for ($i = 0; $i < $numberOfMovies; $i++) {
            $movie = new Movie();
            $movie->setTitle($faker->movie)
                ->setDescription($faker->text)
                ->setReleaseDate($faker->dateTimeBetween('-20 years', 'now'))
                ->setDuration($faker->numberBetween(80, 180))
                ->setEntries($faker->numberBetween(1000, 1000000))
                ->setDirector($faker->director)
                ->setRating($faker->randomFloat(1, 0, 10))
                ->setMedia($faker->imageUrl(640, 480, 'movies', true))
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.9)->dateTimeBetween('-1 years', 'now'));

            // Associer aléatoirement des acteurs et des catégories
            $actorsForMovie = $faker->randomElements($actors, $faker->numberBetween(3, 5));
            foreach ($actorsForMovie as $actor) {
                $movie->addActor($actor);
            }

            $categoriesForMovie = $faker->randomElements($categories, $faker->numberBetween(1, 2));
            foreach ($categoriesForMovie as $category) {
                $movie->addCategory($category);
            }

            $manager->persist($movie);
        }

        // Sauvegarder toutes les entités
        $manager->flush();
    }
}
