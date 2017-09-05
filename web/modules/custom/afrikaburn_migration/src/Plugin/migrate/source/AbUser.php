<?php 

/**
 * @file
 * Contains \Drupal\afrikaburn_migration\Plugin\migrate\source\AbUser.
 */
 
namespace Drupal\afrikaburn_migration\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
 
/**
 * Extract users from Drupal 7 database.
 *
 * @MigrateSource(
 *   id = "afrikaburn_user"
 * )
 */
class AbUser extends DrupalSqlBase {

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

    $fields['first_name'] = $this->t('First name');
    $fields['last_name'] = $this->t('Last name');    
    $fields['gender'] = $this->t('Gender');
    $fields['date_of_birth'] = $this->t('Date of birth');
    $fields['sa_id_or_passport_number'] = $this->t('ID or Passport number');
    $fields['drivers_licence_number'] = $this->t('Drivers licence number');
    $fields['mobile_number'] = $this->t('Mobile');
    $fields['secondary_email_address'] = $this->t('Alternate email address');

    return $fields;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $uid = $row->getSourceProperty('uid');
 
    $this->prepField($uid, $row, 'first_name');
    $this->prepField($uid, $row, 'last_name');
    $this->prepField($uid, $row, 'gender');
    $this->prepField($uid, $row, 'date_of_birth');
    $this->prepField($uid, $row, 'sa_id_or_passport_number');
    $this->prepField($uid, $row, 'drivers_licence_number');
    $this->prepField($uid, $row, 'mobile_number');
    $this->prepField($uid, $row, 'secondary_email_address', '_email');

    print "\n";
   
    return parent::prepareRow($row);
  }
 
  /**
   * Prepares a field
   * @param  [Row] $row        [description]
   * @param  [string] $field_name [description]
   */
  public function prepField($uid, &$row, $field_name, $suffix = '_value'){

    $result = $this->getDatabase()->query('
      SELECT
        fld.field_' . $field_name . $suffix .'
      FROM
        {field_data_field_' . $field_name . '} fld
      WHERE
        fld.entity_id = :uid', 
      array(':uid' => $uid)
    );

    foreach ($result as $record) {
      $record = (array)$record;
      $row->setSourceProperty($field_name, $record['field_' . $field_name . $suffix]);
      print $field_name . ': ' . $record['field_' . $field_name . $suffix] . "\n";
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
