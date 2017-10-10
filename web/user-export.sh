drush sqlc < user-export.sql | sed 's/\t/,/g' > ~/user-export.csv
