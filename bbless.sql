SELECT
mail
FROM
d8_newusers_field_data LEFT Join d8_newuser__field_quicket_code ON uid = entity_id 
WHERE
d8_newuser__field_quicket_code.entity_id IS NULL
