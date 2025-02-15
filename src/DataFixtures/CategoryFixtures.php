<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

//        $category1 = new Category();
//        $category1->setName('Appareils informatiques');
//        $manager->persist($category1);
//
//        $subCategory1_1 = new SubCategory();
//        $subCategory1_1->setName('Ordinateurs portables');
//        $subCategory1_1->setCategory($category1);
//        $manager->persist($subCategory1_1);
//
//        $subCategory1_2 = new SubCategory();
//        $subCategory1_2->setName('Ordinateurs de bureau');
//        $subCategory1_2->setCategory($category1);
//        $manager->persist($subCategory1_2);
//
//        $subCategory1_3 = new SubCategory();
//        $subCategory1_3->setName('Tablettes');
//        $subCategory1_3->setCategory($category1);
//        $manager->persist($subCategory1_3);
//
//        $subCategory1_4 = new SubCategory();
//        $subCategory1_4->setName('Moniteurs');
//        $subCategory1_4->setCategory($category1);
//        $manager->persist($subCategory1_4);
//
//        $subCategory1_5 = new SubCategory();
//        $subCategory1_5->setName('Claviers et souris');
//        $subCategory1_5->setCategory($category1);
//        $manager->persist($subCategory1_5);
//
//        $subCategory1_6 = new SubCategory();
//        $subCategory1_6->setName('Disques durs externes');
//        $subCategory1_6->setCategory($category1);
//        $manager->persist($subCategory1_6);
//
//        $subCategory1_7 = new SubCategory();
//        $subCategory1_7->setName('Accessoires informatiques');
//        $subCategory1_7->setCategory($category1);
//        $manager->persist($subCategory1_7);
//
//        // 2. Téléphones et tablettes
//        $category2 = new Category();
//        $category2->setName('Téléphones et tablettes');
//        $manager->persist($category2);
//
//        $subCategory2_1 = new SubCategory();
//        $subCategory2_1->setName('Smartphones');
//        $subCategory2_1->setCategory($category2);
//        $manager->persist($subCategory2_1);
//
//        $subCategory2_2 = new SubCategory();
//        $subCategory2_2->setName('Tablettes');
//        $subCategory2_2->setCategory($category2);
//        $manager->persist($subCategory2_2);
//
//        $subCategory2_3 = new SubCategory();
//        $subCategory2_3->setName('Accessoires mobiles');
//        $subCategory2_3->setCategory($category2);
//        $manager->persist($subCategory2_3);
//
//        // 3. Appareils photo et caméras
//        $category3 = new Category();
//        $category3->setName('Appareils photo et caméras');
//        $manager->persist($category3);
//
//        $subCategory3_1 = new SubCategory();
//        $subCategory3_1->setName('Appareils photo numériques');
//        $subCategory3_1->setCategory($category3);
//        $manager->persist($subCategory3_1);
//
//        $subCategory3_2 = new SubCategory();
//        $subCategory3_2->setName('Caméras GoPro');
//        $subCategory3_2->setCategory($category3);
//        $manager->persist($subCategory3_2);
//
//        $subCategory3_3 = new SubCategory();
//        $subCategory3_3->setName('Caméras professionnelles');
//        $subCategory3_3->setCategory($category3);
//        $manager->persist($subCategory3_3);
//
//        $subCategory3_4 = new SubCategory();
//        $subCategory3_4->setName('Objectifs');
//        $subCategory3_4->setCategory($category3);
//        $manager->persist($subCategory3_4);
//
//        $subCategory3_5 = new SubCategory();
//        $subCategory3_5->setName('Trépieds et supports');
//        $subCategory3_5->setCategory($category3);
//        $manager->persist($subCategory3_5);
//
//        // 4. Équipements audio et vidéo
//        $category4 = new Category();
//        $category4->setName('Équipements audio et vidéo');
//        $manager->persist($category4);
//
//        $subCategory4_1 = new SubCategory();
//        $subCategory4_1->setName('Casques et écouteurs');
//        $subCategory4_1->setCategory($category4);
//        $manager->persist($subCategory4_1);
//
//        $subCategory4_2 = new SubCategory();
//        $subCategory4_2->setName('Enceintes Bluetooth');
//        $subCategory4_2->setCategory($category4);
//        $manager->persist($subCategory4_2);
//
//        $subCategory4_3 = new SubCategory();
//        $subCategory4_3->setName('Microphones');
//        $subCategory4_3->setCategory($category4);
//        $manager->persist($subCategory4_3);
//
//        $subCategory4_4 = new SubCategory();
//        $subCategory4_4->setName('Projecteurs');
//        $subCategory4_4->setCategory($category4);
//        $manager->persist($subCategory4_4);
//
//        $subCategory4_5 = new SubCategory();
//        $subCategory4_5->setName('Systèmes de son surround');
//        $subCategory4_5->setCategory($category4);
//        $manager->persist($subCategory4_5);
//
//        $subCategory4_6 = new SubCategory();
//        $subCategory4_6->setName('Home cinéma');
//        $subCategory4_6->setCategory($category4);
//        $manager->persist($subCategory4_6);

        // Continue the same pattern for the rest of the categories...

        // Final flush to persist all data
        $manager->flush();
    }
}
