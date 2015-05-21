<?php

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    public $recursive=-1;
    public $actsAs = array('DateFormat' => array(
            'dateFormat' => 'Y-m-d',
            'databaseFormat' => 'Y-m-d',
        ),
        'Containable');
    public $validationDomain = 'validation';

    public function __construct($id = false, $table = null, $ds = null) {
        $this->actsAs['DateFormat']['dateFormat'] = Configure::read('dateFormatSimple');
        $this->actsAs['DateFormat']['databaseFormat'] = Configure::read('databaseDateFormat');
        parent::__construct($id, $table, $ds);
    }

    public function generateHabtmJoin($joinModel, $joinType = 'INNER') {
        // If the relation does not exist, return an empty array.
        if (!isset($this->hasAndBelongsToMany[$joinModel])) {
            return array();
        }

        // Init joins, and get HABTM relation.
        $joins = array();
        $assoc = $this->hasAndBelongsToMany[$joinModel];

        // Add the join table.
        $bind = "{$assoc['with']}.{$assoc['foreignKey']} = {$this->alias}.{$this->primaryKey}";
        $joins[] = array(
            'table' => $assoc['joinTable'],
            'alias' => $assoc['with'],
            'type' => $joinType,
            'foreignKey' => false,
            'conditions' => array($bind),
        );

        // Add the next table.
        $bind = "{$joinModel}.{$this->{$joinModel}->primaryKey} = {$assoc['with']}.{$assoc['associationForeignKey']}";
        $joins[] = array(
            'table' => $this->{$joinModel}->table,
            'alias' => $joinModel,
            'type' => $joinType,
            'foreignKey' => false,
            'conditions' => array($bind),
        );

        return $joins;
    }

}
