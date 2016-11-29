<?php

class builder
{

    public $data = '';

    public $schema;

    private $declaredProps = array();

    function addToFile($data)
    {
        $this->data .= $data;
    }

    private function generateNamespace($underscoreClassName)
    {
        $classNameParts = explode('_', $underscoreClassName);
        $classNameParts = array_splice($classNameParts, 0, -1);

        if (count($classNameParts) === 0) {
            return 'SchemaOrg';
        } else {
            return 'SchemaOrg\\' . implode('\\', $classNameParts);
        }
    }

    private function generateNamespacedClassName($className)
    {
        return '\\SchemaOrg\\' . str_replace('_', '\\', $className);
    }

    function startClass($name, $extends = null)
    {
        $namespace = $this->generateNamespace($name);

        $classNameParts = explode('_', $name);
        $className = array_pop($classNameParts);

        $data = "<?php
        
namespace $namespace;

class $className";

        if ($extends) {
            $data .= " extends " . $this->generateNamespacedClassName($extends);
        }

        $this->addToFile(
            $data . '{
'
        );
    }

    function endClass()
    {
        $this->addToFile('}');
    }

    function addClassProp($name, $ancestors)
    {


        foreach ($ancestors as $class) {


            if (in_array($name, $this->schema->types->{$class}->properties)) {
                return true;
            }
        }

        //var_dump($this->schema->properties->{$name});
        $dataType = 'null|';
        $count = 0;
        foreach ($this->schema->properties->{$name}->ranges as $range) {

            if ($count > 0) {
                $dataType .= '|';
            }

            if ($range == 'Text' || $range == 'URL') {
                $range = 'string';
            }

            if (isset($this->schema->types->{$range})) {
                $tmp = '';
                foreach ($this->schema->types->{$range}->ancestors as $parent) {
                    $tmp .= $parent . '_';
                }
                $range = $this->generateNamespacedClassName($tmp . $range);
            }

            $dataType .= $range;
            $count++;

        }

        $comment = $this->schema->properties->{$name}->comment_plain;

        $data =
            "
     /**
      * $comment
      *
      * @var $dataType $$name
      */
      protected $$name;
";
        $this->addToFile($data);
//die(var_dump($this->schema->properties->{$name}));
    }

    function clearBuffer()
    {
        $this->data = '';
    }


    function fetchSchema()
    {
        $this->schema = json_decode(file_get_contents('http://schema.rdfs.org/all.json'));
    }


}
