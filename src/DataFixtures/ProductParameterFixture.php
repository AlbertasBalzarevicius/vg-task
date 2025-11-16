<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Parameter;
use App\Entity\ParameterDependency;
use App\Entity\ParameterValue;
use App\Entity\Product;
use App\Entity\ProductParameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductParameterFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create product
        $tshirt = new Product();
        $tshirt->setName('Premium T-Shirt');
        $manager->persist($tshirt);

        // Create parameters
        $colorParam = new Parameter();
        $colorParam->setName('Color')->setCode('color');
        $manager->persist($colorParam);

        $sizeParam = new Parameter();
        $sizeParam->setName('Size')->setCode('size');
        $manager->persist($sizeParam);

        $materialParam = new Parameter();
        $materialParam->setName('Material')->setCode('material');
        $manager->persist($materialParam);

        // Create ProductParameter relationships
        $productColorParam = new ProductParameter();
        $productColorParam->setProduct($tshirt)->setParameter($colorParam);
        $manager->persist($productColorParam);

        $productSizeParam = new ProductParameter();
        $productSizeParam->setProduct($tshirt)->setParameter($sizeParam);
        $manager->persist($productSizeParam);

        $productMaterialParam = new ProductParameter();
        $productMaterialParam->setProduct($tshirt)->setParameter($materialParam);
        $manager->persist($productMaterialParam);

        // Create parameter values
        $red = new ParameterValue();
        $red->setName('Red')->setValue('red')->setParameter($colorParam);
        $manager->persist($red);

        $blue = new ParameterValue();
        $blue->setName('Blue')->setValue('blue')->setParameter($colorParam);
        $manager->persist($blue);

        $small = new ParameterValue();
        $small->setName('Small')->setValue('S')->setParameter($sizeParam);
        $manager->persist($small);

        $medium = new ParameterValue();
        $medium->setName('Medium')->setValue('M')->setParameter($sizeParam);
        $manager->persist($medium);

        $large = new ParameterValue();
        $large->setName('Large')->setValue('L')->setParameter($sizeParam);
        $manager->persist($large);

        $cotton = new ParameterValue();
        $cotton->setName('Cotton')->setValue('cotton')->setParameter($materialParam);
        $manager->persist($cotton);

        $polyester = new ParameterValue();
        $polyester->setName('Polyester')->setValue('polyester')->setParameter($materialParam);
        $manager->persist($polyester);

        // UNIDIRECTIONAL DEPENDENCIES ONLY
        // Only create dependencies where there are actual constraints

        // CONSTRAINT: Red color allows only Small and Medium sizes
        $dep1 = new ParameterDependency();
        $dep1->setProduct($tshirt)->setParameterValue($red)->setAllowedParameterValue($small);
        $manager->persist($dep1);

        $dep2 = new ParameterDependency();
        $dep2->setProduct($tshirt)->setParameterValue($red)->setAllowedParameterValue($medium);
        $manager->persist($dep2);

        // Blue color has no size constraints (allows all sizes)
        // No dependencies needed for blue -> sizes

        // CONSTRAINT: Small size allows only Cotton
        $dep3 = new ParameterDependency();
        $dep3->setProduct($tshirt)->setParameterValue($small)->setAllowedParameterValue($cotton);
        $manager->persist($dep3);

        // Medium size has no material constraints (allows all materials)
        // No dependencies needed for medium -> materials

        // CONSTRAINT: Large size allows only Polyester
        $dep4 = new ParameterDependency();
        $dep4->setProduct($tshirt)->setParameterValue($large)->setAllowedParameterValue($polyester);
        $manager->persist($dep4);

        // CONSTRAINT: Red color allows both materials (derived from size constraints)
        $dep5 = new ParameterDependency();
        $dep5->setProduct($tshirt)->setParameterValue($red)->setAllowedParameterValue($cotton);
        $manager->persist($dep5);

        $dep6 = new ParameterDependency();
        $dep6->setProduct($tshirt)->setParameterValue($red)->setAllowedParameterValue($polyester);
        $manager->persist($dep6);

        // Blue color has no material constraints (allows all materials through valid sizes)
        // No dependencies needed for blue -> materials

        $manager->flush();
    }
}
