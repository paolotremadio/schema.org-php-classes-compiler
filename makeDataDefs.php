<?


require dirname(__FILE__).'/schema.class.php';
/**
 * $json->properties
 * $json->types
 * $json->datatypes
 */
/*
$dataTypes = $json->datatypes;

$props = $json->properties;
*/
$builder = new builder();

$builder->fetchSchema();



//die(var_dump($builder->schema->datatypes));

foreach($builder->schema->types as $key => $type){
    //var_dump($key,$type);

    $className = '';
    $path = '';

    foreach($type->ancestors as $extends){
        $className .= $extends.'_';
        $path .= $extends.'/';
    }

   // $className = $className.$key;


    $builder->startClass($className.$key, rtrim($className, "_"));


    foreach($type->properties as  $prop){

        $builder->addClassProp($prop,$type->ancestors);
       // die(var_dump($key,$prop));
    }

    $builder->endClass();
    mkdir('data/'.$path, 0777, true);
    file_put_contents("data/$path$key.php",$builder->data

    );
    $builder->clearBuffer();
   // die();
}

//var_dump(get_object_vars($json->types));




