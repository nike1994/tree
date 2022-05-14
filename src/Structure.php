<?php 

class Structure{
    private $tree = array();
    private $keyTree="";
    private $list = array();
    private $keyList="";

    private	function __construct($pathTree,$pathList)
	{
		$this->tree = $this->loadJSON($pathTree);
		$this->list = $this->loadJSON($pathList);
	}

    public static function loadData($pathTree,$pathList): self
    {
        return new self($pathTree,$pathList);
    }


    private function loadJSON($path): array
    {   
        try{

            $file = file_get_contents($path);
        }catch(Exception $e){
            throw new ErrorException("No such file or directory ".$path);
        }

        if($file === false){
            throw new ErrorException("No such file or directory ".$path);
        }

        $JSON = json_decode($file,true);
        if(is_null($JSON)){
            throw new ErrorException("decode JSON error ".$path);
        }
    
        return $JSON;
    }

    private function deep_clone($object)
    {
        return unserialize(serialize($object));
    }


    function printStructure($object)
    {
        $output = json_encode($object,JSON_PRETTY_PRINT);
        echo $output;
    }

    private function recursiveChangeLeaves(&$node,$propertiesValues,$propertyName): void
    {
        if(empty($node['children'])){
           $this->setProperty($node,$propertiesValues,$propertyName);
        }else{
            foreach ($node['children'] as &$child) {
                $this->recursiveChangeLeaves($child,$propertiesValues,$propertyName);
            }
        }
    }

    function ChangeLeaves($propertiesValues,$propertyName): array
    {
        $tree = $this->deep_clone($this->tree);

        foreach($tree as &$node){
            $this->recursiveChangeLeaves($node,$propertiesValues,$propertyName);
        }
    
        return $tree;
    }

    
    private function setProperty(&$node, $propertiesValues, $propertyName): array
    {
        $node = (array)$node;

        if(array_key_exists($this->keyTree,$node)){
            $id= $node[$this->keyTree];
        }else{
            throw new ErrorException("No such key ".$this->keyTree);
        }

        if(array_key_exists($id,$propertiesValues)){
            $node[$propertyName] = $propertiesValues[$id];
        }else{
            $node[$propertyName] = "";
        }
        
        return $node;
    }

    private function getListPropertyValue($propertyName): array
    {
        $output = array();

        foreach($this->list as $item){
            if(array_key_exists($this->keyList,$item["translations"]["pl_PL"])){
                $key = $item["translations"]["pl_PL"][$this->keyList];
            }else{
                throw new ErrorException("No such key = ".$this->keyList." in list structure");
            }
            
            if(array_key_exists($propertyName,$item["translations"]["pl_PL"])){
                $output[$key]=$item["translations"]["pl_PL"][$propertyName];
            }else{
                throw new ErrorException("No such key = ".$propertyName." in list structure");
            }
            
            
        }
        
        return $output;
    }

    function correlateStructures($keyTree, $keyList, $changePropertyName): array
    {
        $this->keyTree = $keyTree;
        $this->keyList = $keyList;

        $listPropertyValue=$this->getListPropertyValue($changePropertyName);
        $changedTree=$this->ChangeLeaves($listPropertyValue,$changePropertyName);
        return $changedTree;
    }

}