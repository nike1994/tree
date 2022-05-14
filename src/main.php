<?php
include_once('Structure.php');

$structure = Structure::loadData("tree.json","list.json");
$output = $structure->correlateStructures("id","category_id","name");
$structure->printStructure($output);