<?php
function iterate($node, $output = [], $map = 'data')
{
    $types = [
        'form' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'components' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'columns' => [
            'map_append' => false,
            'iterable_node' => 'columns'
        ],
        'panel' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'table' => [
            'map_append' => false,
            'iterable_node' => 'rows'
        ],
        'fieldset' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'datagrid' => [
            'map_append' => 'key',
            'iterable_node' => 'components'
        ],
        'container' => [
            'map_append' => 'key',
            'iterable_node' => 'components'
        ],
        'datamap' => [
            'map_append' => 'key',
            'iterable_node' => 'valueComponent'
        ],
        'tabs' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'selectboxes' => [
            'map_append' => 'key',
            'iterable_node' => 'values'
        ],
        'well' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
    ];

    $ignore_types = ['button'];
    $t_map = $map;

    if (isset($node->type) && in_array($node->type, $ignore_types) || empty($node)) return $output;

    if (isset($node->key) && isset($types[$node->type])) {
        if ($types[$node->type]['map_append'] !== false) {
            $map .= '|' . $node->{$types[$node->type]['map_append']};
        }
    }elseif(isset($node->key) && isset($node->type) && !isset($ignore_types[$node->type])){
        $map .= '|' . $node->key;
    }elseif(!isset($node->key) && isset($node->value)){
        $map .= '|' . $node->value;
    }

    if ($map != 'data' && $t_map !== $map)
        $output[] = [$map, $node->label];

    if(is_array($node)){
        foreach ($node as $item) {
            $output = iterate($item, $output, $map);
        }
    }else{
        if(isset($types[$node->type]) && $types[$node->type]['iterable_node'] !== false){
            foreach ($node->{$types[$node->type]['iterable_node']} as $item) {
                $output = iterate($item, $output, $map);
            }
        }elseif(isset($node->components)){
            foreach ($node->components as $item) {
                $output = iterate($item, $output, $map);
            }
        }else{
            return $output;
        }
    }

    return $output;
}
$manager = new MongoDB\Driver\Manager(
    'mongodb://mongodb:27017'
);
$db = new mysqli("mysql","osticket","secret","osticket");
