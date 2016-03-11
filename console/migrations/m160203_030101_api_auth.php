<?php

use yii\db\Schema;

class m160203_030101_api_auth extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%api_auth_rule}}', [
            'name' => 'varchar(64) NOT NULL   ',
            'data' => 'text  DEFAULT NULL  ',
            'created_at' => 'int(11)  DEFAULT NULL  ',
            'updated_at' => 'int(11)  DEFAULT NULL  ',
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);
        
        $this->createTable('{{%api_auth_item}}', [
            'name' => 'varchar(64) NOT NULL   ',
            'type' => 'int(11) NOT NULL  ',
            'description' => 'text  DEFAULT NULL  ',
            'rule_name' => 'varchar(64)  DEFAULT NULL  ',
            'data' => 'text  DEFAULT NULL  ',
            'created_at' => 'int(11)  DEFAULT NULL  ',
            'updated_at' => 'int(11)  DEFAULT NULL  ',
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES {{%api_auth_rule}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        
        $this->createTable('{{%api_auth_assignment}}', [
            'item_name' => 'varchar(64) NOT NULL   ',
            'user_id' => 'varchar(64) NOT NULL   ',
            'created_at' => 'int(11)  DEFAULT NULL  ',
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES {{%api_auth_item}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        
        $this->createTable('{{%api_auth_item_child}}', [
            'parent' => 'varchar(64) NOT NULL   ',
            'child' => 'varchar(64) NOT NULL   ',
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[child]]) REFERENCES {{%api_auth_item}} ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%api_auth_item_child}}');
        $this->dropTable('{{%api_auth_assignment}}');
        $this->dropTable('{{%api_auth_item}}');
        $this->dropTable('{{%api_auth_rule}}');
    }
}
