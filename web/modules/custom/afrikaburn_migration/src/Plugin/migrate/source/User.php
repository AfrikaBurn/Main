<?php 

/**
 * @file
 * Contains \Drupal\afrikaburn_migration\Plugin\migrate\source\User.
 */
 
namespace Drupal\afrikaburn_migration\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\SqlBase;
 
/**
 * Extract users from Drupal 7 database.
 *
 * @MigrateSource(
 *   id = "custom_user"
 * )
 */
class User extends DrupalSqlBase implements SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('users', 'u')
      ->fields('u', array_keys($this->baseFields()))
      ->condition('uid', 0, '>');
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    $fields['first_name'] = $this->t('First Name');
    $fields['last_name'] = $this->t('Last Name');
    $fields['sa_id_or_passport_number'] = $this->t('ID or Passport number');
    return $fields;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
 
    $this->prepField($row, 'first_name');
    $this->prepField($row, 'last_name');
    $this->prepField($row, 'sa_id_or_passport_number');
   
    return parent::prepareRow($row);
  }
 

  public function prepField($row, $field_name){
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_' . $field_name . '_value
      FROM
        {field_data_field_' . $field_name . '} fld
      WHERE
        fld.entity_id = :uid
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      $row->setSourceProperty($field_name, $record['field_' . $field_name . '_value'] );
    }    
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'uid' => array(
        'type' => 'integer',
        'alias' => 'u',
      ),
    );
  }
 
  /**
   * Returns the user base fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function baseFields() {
    $fields = array(
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email address'),
      'created' => $this->t('Registered timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'timezone' => $this->t('Timezone'),
      'language' => $this->t('Language'),
      'picture' => $this->t('Picture'),
      'init' => $this->t('Init'),
    );
    return $fields;
 
}
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'user';
  }
 
}
