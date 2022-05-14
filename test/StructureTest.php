<?php declare(strict_types=1);
include_once('./src/Structure.php');

use PHPUnit\Framework\TestCase;


final class StructureTest extends TestCase
{
    public function testCanBeCreatedFromExistingFiles(): void
    {
        $this->assertInstanceOf(
            Structure::class,
            Structure::loadData("tree.json","list.json")
        );
    }

    public function testCannotBeCreatedFromNonexistingFiles(): void
    {
        $this->expectException(ErrorException::class);

        Structure::loadData("tree2.json","list.json");
    }

    public function testCannotBeCreatedFromWrongJSONFile(): void
    {
        $this->expectException(ErrorException::class);

        Structure::loadData("wrongtree.json","list.json");
    }

    public function testExceptionWhenWrongKeyTree(): void
    {
        $this->expectException(ErrorException::class);

        $Structure= Structure::loadData("tree.json","list.json");
        $Structure->correlateStructures("wrongid","category_id","name");
        
    }

    public function testExceptionWhenWrongKeyList(): void
    {
        $this->expectException(ErrorException::class);

        $Structure= Structure::loadData("tree.json","list.json");
        $Structure->correlateStructures("id","wrong_id","name");
        
    }


    public function testExceptionWhenWrongProperty(): void
    {
        $this->expectException(ErrorException::class);

        $Structure= Structure::loadData("tree.json","list.json");
        $Structure->correlateStructures("id","category_id","wrong_name");
        
    }

}

