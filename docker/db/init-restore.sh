#!/bin/sh
# Runs only when the Postgres data directory is empty.
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
  -c "CREATE EXTENSION IF NOT EXISTS postgis;"

if [ -f /dump/selgem.dump ]; then
  echo "Restoring /dump/selgem.dump into ${POSTGRES_DB}"
  if ! pg_restore --no-owner --no-acl --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" /dump/selgem.dump; then
    echo "pg_restore finished with warnings. Confirm the sites count below before relying on this database."
  fi
  psql --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" -c "SELECT COUNT(*) AS sites FROM sites;" || true
else
  echo "No dump found. Laravel migrations will create an empty schema."
fi
