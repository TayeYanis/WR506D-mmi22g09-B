<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Xylis\FakerCinema\Provider\Movie as MovieProvider;
use Xylis\FakerCinema\Provider\Person as PersonProvider;
use DateTimeImmutable;
use App\Resources\MovieImages;
use App\Resources\ActorImages;

class AppFixtures extends Fixture
{
    private const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/original';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new MovieProvider($faker));
        $faker->addProvider(new PersonProvider($faker));

        $categories = [];
        $categoryTitles = ['Action', 'Comédie', 'Drame', 'Horreur', 'Science-fiction', 'Romance'];
        foreach ($categoryTitles as $title) {
            $category = new Category();
            $category->setTitle($title)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.8)->dateTimeBetween('now'));
            $manager->persist($category);
            $categories[] = $category;
        }


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
                ->setMedia(self::IMAGE_BASE_URL . $faker->randomElement(ActorImages::ACTOR_IMAGE_PATHS))
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.8)->dateTimeBetween('now'))
                ->setDeathDate($faker->optional(0.15)->dateTimeBetween($dob, 'now'));

            $manager->persist($actor);
            $actors[] = $actor;
        }

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
                ->setMedia(self::IMAGE_BASE_URL . $faker->randomElement(MovieImages::MOVIE_IMAGE_PATHS))
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->optional(0.9)->dateTimeBetween('now'));


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


        $user = new User();
        $user->setEmail('user@gmail.com')
            ->setPassword(password_hash('password', PASSWORD_BCRYPT)) // <-- Utilise un hasher sécurisé
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);



        $manager->flush();
    }
}
