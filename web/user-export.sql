SELECT
  field_first_name_value,
  field_last_name_value,
  mail,
  field_mobile_value,
  field_id_number_value

FROM
  d8_newusers users,
  d8_newuser__field_first_name first_name,
  d8_newuser__field_last_name last_name,
  d8_newusers_field_data mail,
  d8_newuser__field_mobile mobile,
  d8_newuser__field_id_number id

WHERE 
  users.uid > '1' AND
  users.uid = first_name.entity_id AND
  users.uid = last_name.entity_id AND
  users.uid = mail.uid AND
  users.uid = mobile.entity_id AND
  users.uid = id.entity_id
