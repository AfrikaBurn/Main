drush sqlc < quicket-export.sql | sed 's/\t/,/g' > ~/quicket-export.csv
